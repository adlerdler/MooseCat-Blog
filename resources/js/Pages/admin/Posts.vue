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
  FileText,
  Plus,
  Search,
  Edit3,
  Trash2,
  Clock,
  Tag,
  ChevronLeft,
  ChevronRight,
  Filter,
  ConfirmDialog,
  PostForm,
  Pagination,
  SearchFilterModal,
  router
} from '../../composables/useAdminImports';
import { usePage } from '@inertiajs/vue3';
import { formatToSmartDate } from '../../utils/dateUtils';

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

const categoryOptions = computed(() => {
  return ['all', ...props.categories.map(cat => cat.name)];
});

const getCategoryColor = (category) => {
  const colorMap = {
    'THEORY': isDarkMode ? 'bg-purple-900 text-purple-300' : 'bg-purple-100 text-purple-700',
    'DESIGN': isDarkMode ? 'bg-blue-900 text-blue-300' : 'bg-blue-100 text-blue-700',
    'TECHNOLOGY': isDarkMode ? 'bg-green-900 text-green-300' : 'bg-green-100 text-green-700',
    'CULTURE': isDarkMode ? 'bg-yellow-900 text-yellow-300' : 'bg-yellow-100 text-yellow-700',
    'SYSTEM-DESIGN': isDarkMode ? 'bg-indigo-900 text-indigo-300' : 'bg-indigo-100 text-indigo-700',
    'ENGINEERING': isDarkMode ? 'bg-orange-900 text-orange-300' : 'bg-orange-100 text-orange-700'
  };
  return colorMap[category] || (isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600');
};

const getStatusBadge = (status) => {
  const base = 'px-2 py-1 text-xs font-bold uppercase';
  if (status === 'published') {
    return `${base} ${isDarkMode ? 'bg-green-900 text-green-300' : 'bg-green-100 text-green-700'}`;
  }
  if (status === 'draft') {
    return `${base} ${isDarkMode ? 'bg-yellow-900 text-yellow-300' : 'bg-yellow-100 text-yellow-700'}`;
  }
  if (status === 'scheduled') {
    return `${base} ${isDarkMode ? 'bg-blue-900 text-blue-300' : 'bg-blue-100 text-blue-700'}`;
  }
  return `${base} ${isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'}`;
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
    router.delete(route('posts.destroy', deletingPostId.value), {
      onSuccess: () => {
        deletingPostId.value = null;
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
  };

  if (editingPost.value && editingPost.value.id) {
    router.put(route('posts.update', editingPost.value.id), payload, {
      onError: (errors) => console.error('Update errors:', errors),
      onSuccess: () => console.log('Update success'),
    });
  } else {
    router.post(route('posts.store'), payload, {
      onError: (errors) => console.error('Store errors:', errors),
      onSuccess: () => console.log('Store success'),
    });
  }
  isFormVisible.value = false;
  editingPost.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingPost.value = null;
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
      <div
        v-for="post in paginatedPosts"
        :key="post.id"
        :class="[
          'flex flex-col rounded-lg shadow-md',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]"
      >
        <!-- Post Thumbnail -->
        <div :class="['relative aspect-video box-border rounded-t-lg overflow-hidden', !post.cover_image ? 'border-2 border-b-0 border-dashed' : '', isDarkMode ? 'bg-gray-900' : 'bg-gray-100', !post.cover_image && !isDarkMode ? 'border-gray-300' : '', !post.cover_image && isDarkMode ? 'border-gray-600' : '']">
          <img
            v-if="post.cover_image"
            :src="post.cover_image"
            :alt="post.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <FileText :class="['w-12 h-12', isDarkMode ? 'text-gray-600' : 'text-gray-400']" />
          </div>
          <div class="absolute top-2 left-2 px-2 py-1 text-xs font-bold uppercase rounded" :class="getStatusBadge(post.status)">
            {{ getStatusLabel(post.status) }}
          </div>
          <div class="absolute top-2 right-2 px-2 py-1 text-xs font-bold uppercase rounded" :class="getCategoryColor(getCategoryNameById(categories, post.category_id))">
            {{ getCategoryNameById(categories, post.category_id) }}
          </div>
        </div>
        
        <!-- Post Info -->
        <div class="p-4 flex flex-col flex-1">
          <h3
            :class="['font-bold mb-2 text-lg', isDarkMode ? 'text-white' : 'text-gray-900']"
            style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden;"
          >{{ post.title }}</h3>
          <p
            :class="['text-sm mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-600']"
            style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden; min-height: 2.5rem;"
          >{{ post.excerpt }}</p>
          
          <!-- Tags -->
          <div class="flex flex-wrap gap-1 mb-4">
            <span
              v-for="tag in post.tags.slice(0, 3)"
              :key="tag"
              :class="['text-xs px-2 py-1 rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']"
            >
              {{ tag }}
            </span>
            <span v-if="post.tags.length > 3" :class="['text-xs px-2 py-1 rounded', isDarkMode ? 'bg-gray-700 text-gray-400' : 'bg-gray-100 text-gray-500']">
              +{{ post.tags.length - 3 }}
            </span>
          </div>
          
          <div class="flex items-center justify-between mt-auto">
            <div :class="['flex items-center gap-2 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              <Clock size="12" />
              {{ formatToSmartDate(post.created_at) }}
              <span class="ml-2">{{ getAuthorName(post.author_id) }}</span>
            </div>
            
            <div class="flex gap-2">
              <button
                @click="handleEdit(post)"
                :class="[
                  'p-2 transition-colors rounded',
                  isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-500 hover:text-blue-500 hover:bg-gray-100'
                ]"
                :title="t('admin_edit')"
              >
                <Edit3 size="14" />
              </button>
              <button
                @click="handleDelete(post.id)"
                :class="[
                  'p-2 transition-colors rounded',
                  isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700' : 'text-gray-500 hover:text-red-500 hover:bg-gray-100'
                ]"
                :title="t('admin_delete')"
              >
                <Trash2 size="14" />
              </button>
            </div>
          </div>
        </div>
      </div>
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
  </div>
</template>