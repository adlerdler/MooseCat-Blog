<script setup>
/**
 * AdminPosts.vue - 文章管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有文章
 * - 支持搜索和筛选功能
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
  getAuthorName
} from '../../composables/useAdminImports';
import { POSTS } from '../../data/posts';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;
const isFormVisible = ref(false);
const editingPost = ref(null);
const showDeleteConfirm = ref(false);
const deletingPostId = ref(null);

const filteredPosts = computed(() => {
  let result = [...POSTS];
  
  result.sort((a, b) => new Date(b.date) - new Date(a.date));
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(post =>
      post.title.toLowerCase().includes(query) ||
      getAuthorName(post.userId).toLowerCase().includes(query) ||
      post.tags.some(tag => tag.toLowerCase().includes(query))
    );
  }
  
  return result;
});

const totalPages = computed(() => Math.ceil(filteredPosts.value.length / itemsPerPage));

const paginatedPosts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredPosts.value.slice(start, start + itemsPerPage);
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
</script>

<template>
  <div class="min-h-screen">
    <!-- Header -->
    <div :class="['flex items-center justify-between mb-8', isDarkMode ? 'text-white' : 'text-gray-900']">
      <div>
        <h1 class="text-3xl font-bold tracking-wider">{{ t('admin_posts') }}</h1>
        <p :class="['mt-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_manage_all_posts') }}</p>
      </div>
      <button @click="handleAdd" class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded">
        <Plus size="16" /> {{ t('admin_add_post') }}
      </button>
    </div>

    <!-- Search -->
    <div :class="['flex items-center gap-4 mb-6', isDarkMode ? 'bg-gray-800' : 'bg-gray-50']" class="p-4 rounded-lg">
      <Search :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="20" />
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="t('admin_search_posts')"
        :class="[
          'flex-1 px-4 py-3 border bg-transparent focus:border-construct-red focus:outline-none transition-colors',
          isDarkMode ? 'border-gray-700 text-white placeholder-gray-500' : 'border-gray-300 text-gray-900 placeholder-gray-400'
        ]"
      />
      <div :class="['flex items-center gap-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        <Filter size="18" />
        <select
          :class="[
            'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
          ]"
        >
          <option value="all">{{ t('admin_all') }}</option>
          <option value="recent">{{ t('admin_recent') }}</option>
          <option value="popular">{{ t('admin_popular') }}</option>
        </select>
      </div>
    </div>

    <!-- Posts Table -->
    <div :class="['border overflow-hidden', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']" class="rounded-lg">
      <table class="w-full">
        <thead>
          <tr :class="['text-left', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-50 text-gray-600']">
            <th :class="['px-6 py-4 text-xs font-bold tracking-widest uppercase']">ID</th>
            <th :class="['px-6 py-4 text-xs font-bold tracking-widest uppercase']">{{ t('admin_table_title') }}</th>
            <th :class="['px-6 py-4 text-xs font-bold tracking-widest uppercase']">{{ t('admin_table_category') }}</th>
            <th :class="['px-6 py-4 text-xs font-bold tracking-widest uppercase']">{{ t('admin_table_author') }}</th>
            <th :class="['px-6 py-4 text-xs font-bold tracking-widest uppercase']">{{ t('admin_table_date') }}</th>
            <th :class="['px-6 py-4 text-xs font-bold tracking-widest uppercase']">{{ t('admin_table_tags') }}</th>
            <th :class="['px-6 py-4 text-xs font-bold tracking-widest uppercase']">{{ t('admin_table_actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="post in paginatedPosts"
            :key="post.id"
            :class="['border-t hover:bg-gray-50 transition-colors', isDarkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-200']"
          >
            <td :class="['px-6 py-4 text-sm font-mono', isDarkMode ? 'text-gray-400' : 'text-gray-500']">#{{ String(post.id).padStart(4, '0') }}</td>
            <td class="px-6 py-4">
              <div :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ post.title }}</div>
              <p :class="['text-sm mt-1 line-clamp-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ post.excerpt }}</p>
            </td>
            <td class="px-6 py-4">
              <span
                :class="[
                  'text-xs px-3 py-1 uppercase font-bold',
                  post.category === 'Theory' ? (isDarkMode ? 'bg-purple-900 text-purple-300' : 'bg-purple-100 text-purple-700') :
                  post.category === 'Design' ? (isDarkMode ? 'bg-blue-900 text-blue-300' : 'bg-blue-100 text-blue-700') :
                  post.category === 'Technology' ? (isDarkMode ? 'bg-green-900 text-green-300' : 'bg-green-100 text-green-700') :
                  (isDarkMode ? 'bg-orange-900 text-orange-300' : 'bg-orange-100 text-orange-700')
                ]"
              >
                {{ post.category }}
              </span>
            </td>
            <td :class="['px-6 py-4 text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ getAuthorName(post.userId) }}</td>
            <td :class="['px-6 py-4 text-sm flex items-center gap-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              <Clock size="14" />
              {{ formatToShort(post.date) }}
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
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
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <button
                  @click="handleEdit(post)"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-300' : 'hover:bg-gray-100 text-gray-600']"
                  :title="t('admin_edit')"
                >
                  <Edit3 size="16" />
                </button>
                <button
                  @click="handleDelete(post.id)"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-red-900/50 text-red-400' : 'hover:bg-red-50 text-red-500']"
                  :title="t('admin_delete')"
                >
                  <Trash2 size="16" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-8">
      <div :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        {{ t('admin_showing') }} {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredPosts.length) }} {{ t('admin_of') }} {{ filteredPosts.length }} {{ t('admin_posts') }}
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg font-bold transition-colors',
            currentPage === 1
              ? (isDarkMode ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed')
              : (isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50')
          ]"
        >
          <ChevronLeft size="16" />
          {{ t('admin_prev') }}
        </button>
        <span :class="['px-4 py-2 font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ currentPage }}</span>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg font-bold transition-colors',
            currentPage === totalPages
              ? (isDarkMode ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed')
              : (isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50')
          ]"
        >
          {{ t('admin_next') }}
          <ChevronRight size="16" />
        </button>
      </div>
    </div>

    <!-- Form Modal -->
    <ContentForm
      :edit-data="editingPost"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="isFormVisible = false"
    />

    <!-- Delete Confirmation -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      :title="t('admin_delete_post')"
      :message="t('admin_delete_post_confirm')"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>