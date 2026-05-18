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
  useI18n,
  useTheme,
  formatToShort,
  FileText,
  Plus,
  Search,
  Edit3,
  Trash2,
  Eye,
  Clock,
  Tag,
  ChevronLeft,
  ChevronRight,
  Filter,
  ConfirmDialog,
  ContentForm,
  AdminPagination
} from '../../composables/useAdminImports';
import { POSTS } from '../../data/posts';
import { categories } from '../../data/categories';
import { adminUsers } from '../../data/users';
import { getCategoryNameById, getCategoryLabelById } from '../../utils/categoryUtils';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const getAuthorName = (authorId) => {
  const user = adminUsers.find(u => u.id === authorId);
  return user ? (user.penName || user.name) : 'Unknown';
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
  return ['all', ...categories.map(cat => cat.name)];
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

const filteredPosts = computed(() => {
  let result = [...POSTS];

  result.sort((a, b) => new Date(b.publishedAt) - new Date(a.publishedAt));

  if (selectedCategory.value !== 'all') {
    result = result.filter(post => getCategoryNameById(categories, post.categoryId) === selectedCategory.value);
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(post =>
      post.title.toLowerCase().includes(query) ||
      getAuthorName(post.authorId).toLowerCase().includes(query) ||
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
  editingPost.value = { ...post };
  isFormVisible.value = true;
};

const handleDelete = (id) => {
  deletingPostId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingPostId.value !== null) {
    console.log('Delete post:', deletingPostId.value);
    deletingPostId.value = null;
  }
  showDeleteConfirm.value = false;
};

const handleSave = (data) => {
  console.log('Save post:', data);
  isFormVisible.value = false;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingPost.value = null;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h2 class="font-display text-4xl tracking-tighter mb-2">{{ t('admin_posts') }}</h2>
      <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">{{ t('admin_posts_subtitle') }}</p>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
      <!-- Search -->
      <div class="relative flex-1">
        <Search :class="['absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_placeholder')"
          :class="[
            'w-full pl-10 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
          ]"
        />
      </div>
      
      <!-- Category Filter -->
      <div class="relative">
        <Filter :class="['absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
        <select
          v-model="selectedCategory"
          :class="[
            'pl-10 pr-8 py-3 border focus:border-construct-red focus:outline-none appearance-none cursor-pointer min-w-[150px]',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
          ]"
        >
          <option v-for="cat in categoryOptions" :key="cat" :value="cat">
            {{ cat === 'all' ? t('admin_all') : cat }}
          </option>
        </select>
      </div>
      
      <!-- Add Button -->
      <button
        @click="handleAdd"
        class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded"
      >
        <Plus size="16" class="!text-white" />
        {{ t('admin_add') }}
      </button>
    </div>

    <!-- Posts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="post in paginatedPosts"
        :key="post.id"
        :class="[
          'border overflow-hidden hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <!-- Post Thumbnail -->
        <div class="relative aspect-video bg-gray-900">
          <img
            v-if="post.coverImage"
            :src="post.coverImage"
            :alt="post.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <FileText class="w-12 h-12 text-gray-600" />
          </div>
          <div class="absolute top-2 right-2 px-2 py-1 text-xs font-bold uppercase" :class="getCategoryColor(getCategoryNameById(categories, post.categoryId))">
            {{ getCategoryNameById(categories, post.categoryId) }}
          </div>
        </div>
        
        <!-- Post Info -->
        <div class="p-4">
          <h3 :class="['font-bold mb-2 line-clamp-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ post.title }}</h3>
          <p :class="['text-sm mb-4 line-clamp-2', isDarkMode ? 'text-gray-400' : 'text-gray-600']">{{ post.excerpt }}</p>
          
          <!-- Tags -->
          <div class="flex flex-wrap gap-1 mb-4">
            <span
              v-for="tag in post.tags.slice(0, 3)"
              :key="tag"
              :class="['text-xs px-2 py-1', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']"
              class="rounded"
            >
              {{ tag }}
            </span>
            <span v-if="post.tags.length > 3" :class="['text-xs px-2 py-1', isDarkMode ? 'bg-gray-700 text-gray-400' : 'bg-gray-100 text-gray-500']" class="rounded">
              +{{ post.tags.length - 3 }}
            </span>
          </div>
          
          <div class="flex items-center justify-between">
            <div :class="['flex items-center gap-2 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              <Clock size="12" />
              {{ formatToShort(post.publishedAt) }}
              <span class="ml-2">{{ getAuthorName(post.authorId) }}</span>
            </div>
            
            <div class="flex gap-2">
              <button
                @click="handleEdit(post)"
                :class="[
                  'p-2 transition-colors',
                  isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-500 hover:text-blue-500 hover:bg-gray-100'
                ]"
                :title="t('admin_edit')"
              >
                <Edit3 size="14" />
              </button>
              <button
                :class="[
                  'p-2 transition-colors',
                  isDarkMode ? 'text-gray-400 hover:text-green-400 hover:bg-gray-700' : 'text-gray-500 hover:text-green-500 hover:bg-gray-100'
                ]"
                :title="t('admin_view')"
              >
                <Eye size="14" />
              </button>
              <button
                @click="handleDelete(post.id)"
                :class="[
                  'p-2 transition-colors',
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
    <AdminPagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredPosts.length"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage.value = page"
      @update:items-per-page="(size) => { itemsPerPage.value = size; currentPage.value = 1; }"
    />

    <!-- Content Form Modal -->
    <ContentForm
      content-type="post"
      :edit-data="editingPost"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      :title="t('admin_delete_post')"
      :message="t('admin_delete_post_confirm')"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>