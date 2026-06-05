<script setup>
/**
 * AdminPosts.vue - 文章管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有文章
 * - 支持搜索和分类筛选功能
 * - 支持文章的增删改查操作
 */
import {
  ref,
  computed,
  watch,
  useI18n,
  useTheme,
  useToast,
  FileText,
  Plus,
  ConfirmDialog,
  PostForm,
  Pagination,
  SearchFilterModal,
  router
} from '../../composables/useAdminImports';
import SeoForm from '../../components/admin/SeoForm.vue';
import ContentCard from '../../components/admin/ContentCard.vue';
import { usePage } from '@inertiajs/vue3';
import { formatToSmartDate } from '../../utils/dateUtils';

const toDatetimeLocal = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  if (isNaN(d.getTime())) return '';
  const pad = n => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const page = usePage();

const props = defineProps({
  posts: {
    type: Array,
    default: () => []
  },
  post: {
    type: Object,
    default: null
  },
  categories: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  }
});

const { t: originalT } = useI18n();
const t = (key, fallback = '') => {
  if (!key) return fallback || '';
  try {
    return originalT(key) || fallback;
  } catch (e) {
    return fallback;
  }
};
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const getAuthorName = (authorId) => {
  const user = props.users.find(u => u.id === authorId);
  return user ? (user.penName || user.name) : 'Unknown';
};

const getCategoryNameById = (categories, categoryId) => {
  const category = categories.find(c => c.id === categoryId);
  return category ? category.name : 'Unknown';
};

const getCategoryLabelById = (categories, categoryId) => {
  const category = categories.find(c => c.id === categoryId);
  return category ? category.name : 'Unknown';
};

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedCategory = ref('all');
const isFormVisible = ref(false);
const editingPost = ref(null);
const showDeleteConfirm = ref(false);
const deletingPostId = ref(null);
const showSeoModal = ref(false);
const editingSeoPost = ref({ id: null, meta_title: '', meta_description: '', meta_keywords: '' });

const categoryOptions = computed(() => {
  return ['all', ...props.categories.map(cat => cat.name)];
});

const getCategoryColor = (category) => {
  const dark = isDarkMode.value;
  const colorMap = {
    'THEORY': dark ? 'bg-purple-900 text-purple-300' : 'bg-purple-100 text-purple-700',
    'DESIGN': dark ? 'bg-blue-900 text-blue-300' : 'bg-blue-100 text-blue-700',
    'TECHNOLOGY': dark ? 'bg-green-900 text-green-300' : 'bg-green-100 text-green-700',
    'CULTURE': dark ? 'bg-yellow-900 text-yellow-300' : 'bg-yellow-100 text-yellow-700',
    'SYSTEM-DESIGN': dark ? 'bg-indigo-900 text-indigo-300' : 'bg-indigo-100 text-indigo-700',
    'ENGINEERING': dark ? 'bg-orange-900 text-orange-300' : 'bg-orange-100 text-orange-700'
  };
  return colorMap[category] || (dark ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600');
};

const getStatusBadge = (status) => {
  const base = 'px-2 py-1 text-xs font-bold uppercase';
  const dark = isDarkMode.value;
  if (status === 'published') {
    return `${base} ${dark ? 'bg-green-900 text-green-300' : 'bg-green-100 text-green-700'}`;
  }
  if (status === 'draft') {
    return `${base} ${dark ? 'bg-yellow-900 text-yellow-300' : 'bg-yellow-100 text-yellow-700'}`;
  }
  if (status === 'scheduled') {
    return `${base} ${dark ? 'bg-blue-900 text-blue-300' : 'bg-blue-100 text-blue-700'}`;
  }
  return `${base} ${dark ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'}`;
};

const getStatusLabel = (status) => {
  const labels = {
    published: t('admin_post_status_published'),
    draft: t('admin_post_status_draft'),
    scheduled: t('admin_post_status_scheduled'),
  };
  return labels[status] || status;
};

const filteredPosts = computed(() => {
  let result = [...props.posts];

  result.sort((a, b) => new Date(b.published_at) - new Date(a.published_at));

  if (selectedCategory.value !== 'all') {
    result = result.filter(post => getCategoryNameById(props.categories, post.category_id) === selectedCategory.value);
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(post =>
      post.title.toLowerCase().includes(query) ||
      getAuthorName(post.author_id).toLowerCase().includes(query) ||
      post.tags.some(tag => tag.toLowerCase().includes(query))
    );
  }

  return result;
});

const totalPages = computed(() => Math.ceil(filteredPosts.value.length / itemsPerPage.value));

const paginatedPosts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredPosts.value.slice(start, start + itemsPerPage.value);
});

const handleAdd = () => {
  editingPost.value = null;
  isFormVisible.value = true;
};

const handleEdit = (post) => {
  editingPost.value = {
    id: post.id,
    title: post.title,
    slug: post.slug,
    excerpt: post.excerpt,
    content: post.content,
    thumbnail: post.cover_image || '',
    category: post.category_id,
    status: post.status || 'draft',
    tags: Array.isArray(post.tags) ? post.tags.join(', ') : (post.tags || ''),
    color: post.color || 'red',
    date: post.published_at ? toDatetimeLocal(post.published_at) : '',
  };
  isFormVisible.value = true;
};

const handleEditFromProp = () => {
  if (props.post) {
    editingPost.value = { ...props.post };
    isFormVisible.value = true;
  }
};

const handleDelete = (id) => {
  deletingPostId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingPostId.value !== null) {
    router.delete(route('admin.posts.destroy', deletingPostId.value), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess(t('admin_post_deleted_success', 'Post deleted successfully'));
        deletingPostId.value = null;
      },
      onError: (err) => {
        toastError(err?.message || t('admin_post_delete_error', 'Failed to delete post'));
      }
    });
  }
  showDeleteConfirm.value = false;
};

const handleSave = (data) => {
  const categoryId = data.category ? Number(data.category) : null;
  const category = categoryId ? props.categories.find(c => c.id === categoryId) : null;
  const payload = {
    title: data.title,
    excerpt: data.excerpt,
    content: data.content,
    cover_image: data.thumbnail,
    category_id: category ? category.id : null,
    status: data.status || 'published',
    tags: data.tags,
    color: data.color,
    published_at: data.date || null,
  };

  if (editingPost.value && editingPost.value.id) {
    router.put(route('admin.posts.update', editingPost.value.id), payload, {
      preserveState: true,
      onError: (err) => toastError(err?.message || t('admin_post_update_error', 'Failed to update post')),
      onSuccess: () => toastSuccess(t('admin_post_updated_success', 'Post updated successfully')),
    });
  } else {
    router.post(route('admin.posts.store'), payload, {
      preserveState: true,
      onError: (err) => toastError(err?.message || t('admin_post_create_error', 'Failed to create post')),
      onSuccess: () => toastSuccess(t('admin_post_created_success', 'Post created successfully')),
    });
  }
  isFormVisible.value = false;
  editingPost.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingPost.value = null;
};

const handleSeoEdit = (post) => {
  editingSeoPost.value = {
    id: post.id,
    meta_title: post.meta_title || '',
    meta_description: post.meta_description || '',
    meta_keywords: post.meta_keywords || '',
  };
  showSeoModal.value = true;
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'category') {
    selectedCategory.value = value;
  }
  currentPage.value = 1;
};

watch(() => props.post, (newPost) => {
  if (newPost) {
    editingPost.value = { ...newPost };
    isFormVisible.value = true;
  }
}, { immediate: true });
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <FileText class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_posts') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_posts_subtitle') }}</p>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus size="18" />
          {{ t('admin_add') }}
        </button>
      </div>
    </div>

    <!-- Toolbar -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_placeholder')"
      :filters="[
        {
          key: 'category',
          options: categoryOptions.map(cat => ({
            value: cat,
            label: cat === 'all' ? t('admin_all') : cat
          }))
        }
      ]"
      :filter-values="{ category: selectedCategory }"
      @filter-change="handleFilterChange"
    />

    <!-- Posts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <ContentCard
        v-for="post in paginatedPosts"
        :key="post.id"
        :item="post"
        :cover-image="post.cover_image"
        :title="post.title"
        :description="post.excerpt"
        :tags="post.tags"
        :date="formatToSmartDate(post.published_at || post.created_at)"
        :author="getAuthorName(post.author_id)"
        :placeholder-icon="FileText"
        @edit="handleEdit"
        @seo-edit="handleSeoEdit"
        @delete="handleDelete"
      >
        <template #top-left-badges>
          <span class="px-2 py-1 text-xs font-bold uppercase rounded" :class="getStatusBadge(post.status)">
            {{ getStatusLabel(post.status) }}
          </span>
          <span class="px-2 py-1 text-xs font-bold uppercase rounded" :class="getCategoryColor(getCategoryNameById(categories, post.category_id))">
            {{ getCategoryNameById(categories, post.category_id) }}
          </span>
        </template>
      </ContentCard>
    </div>

    <!-- Pagination -->
    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredPosts.length"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage.value = page"
      @update:items-per-page="(size) => { itemsPerPage.value = size; currentPage.value = 1; }"
    />

    <!-- Content Form Modal -->
    <PostForm
      :edit-data="editingPost"
      :visible="isFormVisible"
      :categories="categories"
      :media-files="page.props.mediaFiles || []"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      type="delete"
      :title="t('admin_delete_post')"
      :content="t('admin_delete_post_confirm')"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />

    <!-- SEO Edit Modal -->
    <SeoForm
      v-model:visible="showSeoModal"
      :item="editingSeoPost"
      resource-route="posts.update"
    />
  </div>
</template>