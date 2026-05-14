<script setup>
/**
 * AdminPosts.vue - 文章管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有文章
 * - 支持搜索和筛选功能
 * - 支持文章的增删改查操作
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
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
  Filter
} from 'lucide-vue-next';
import { POSTS } from '../../data/posts';
import { useTheme } from '../../composables/useTheme';
import { formatToShort } from '../../utils/dateUtils';
import ContentForm from '../../components/admin/ContentForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;
const selectedCategory = ref('all');
const isFormVisible = ref(false);
const editingPost = ref(null);
const showDeleteConfirm = ref(false);
const deletingPostId = ref(null);

const categories = ['all', 'Theory', 'Design', 'Technology', 'Culture'];

const filteredPosts = computed(() => {
  let result = [...POSTS];
  
  if (selectedCategory.value !== 'all') {
    result = result.filter(post => post.category === selectedCategory.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(post =>
      post.title.toLowerCase().includes(query) ||
      post.author.toLowerCase().includes(query) ||
      post.tags.some(tag => tag.toLowerCase().includes(query))
    );
  }
  
  return result;
});

const paginatedPosts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredPosts.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredPosts.value.length / itemsPerPage);
});

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

const handleEdit = (post) => {
  editingPost.value = {
    id: post.id,
    title: post.title,
    author: post.author,
    category: post.category,
    tags: post.tags.join(', '),
    excerpt: post.excerpt,
    content: post.content,
    date: post.date,
    status: 'published'
  };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingPost.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingPost.value) {
    console.log('Update post:', data);
  } else {
    console.log('Create post:', data);
  }
  isFormVisible.value = false;
  editingPost.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingPost.value = null;
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_posts') }}</h2>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_posts_subtitle') }}</p>
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
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-500' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-400'
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
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-200 text-gray-900'
          ]"
        >
          <option v-for="cat in categories" :key="cat" :value="cat">
            {{ cat === 'all' ? t('admin_filter_all') : cat }}
          </option>
        </select>
      </div>
      
      <!-- Add Button -->
      <button
        @click="handleAdd"
        class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded"
      >
        <Plus size="16" class="!text-white" />
        {{ t('admin_add_new') }}
      </button>
    </div>

    <!-- Posts Table -->
    <div :class="['border overflow-hidden', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead :class="['border-b', isDarkMode ? 'bg-gray-900 border-gray-700' : 'bg-gray-50 border-gray-200']">
          <tr>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_title') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_category') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_author') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_date') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_actions') }}</th>
          </tr>
        </thead>
        <tbody :class="['divide-y', isDarkMode ? 'divide-gray-700' : 'divide-gray-200']">
          <tr
            v-for="post in paginatedPosts"
            :key="post.id"
            :class="['transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50']"
          >
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-construct-red flex items-center justify-center">
                  <FileText :class="isDarkMode ? 'text-white' : 'text-white'" size="18" />
                </div>
                <div>
                  <div :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ post.title }}</div>
                  <div class="flex gap-2 mt-1">
                    <span
                      v-for="tag in post.tags.slice(0, 2)"
                      :key="tag"
                      :class="['text-[10px] px-2 py-0.5 uppercase', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']"
                    >
                      #{{ tag }}
                    </span>
                  </div>
                </div>
              </div>
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
            <td :class="['px-6 py-4 text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ post.author }}</td>
            <td :class="['px-6 py-4 text-sm flex items-center gap-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              <Clock size="14" />
              {{ formatToShort(post.date) }}
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button
                  @click="handleEdit(post)"
                  :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-600' : 'text-gray-500 hover:text-blue-500 hover:bg-gray-100']"
                  :title="t('admin_edit')"
                >
                  <Edit3 size="16" />
                </button>
                <button
                  :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-green-400 hover:bg-gray-600' : 'text-gray-500 hover:text-green-500 hover:bg-gray-100']"
                  :title="t('admin_view')"
                >
                  <Eye size="16" />
                </button>
                <button
                  @click="handleDelete(post.id)"
                  :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-600' : 'text-gray-500 hover:text-red-500 hover:bg-gray-100']"
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
    <div class="flex items-center justify-between mt-6">
      <div :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        {{ t('admin_showing') }} {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredPosts.length) }} {{ t('admin_of') }} {{ filteredPosts.length }}
      </div>
      <div class="flex gap-2">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          :class="['p-2 border transition-colors disabled:opacity-50 disabled:cursor-not-allowed', isDarkMode ? 'border-gray-700 text-gray-400 hover:text-white hover:border-gray-500' : 'border-gray-200 text-gray-500 hover:text-gray-900 hover:border-gray-400']"
        >
          <ChevronLeft size="18" />
        </button>
        <button
          v-for="page in totalPages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-4 py-2 border text-sm font-bold transition-colors',
            page === currentPage
              ? 'border-construct-red text-construct-red bg-gray-800'
              : isDarkMode ? 'border-gray-700 text-gray-400 hover:text-white hover:border-gray-500' : 'border-gray-200 text-gray-500 hover:text-gray-900 hover:border-gray-400'
          ]"
        >
          {{ page }}
        </button>
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          :class="['p-2 border transition-colors disabled:opacity-50 disabled:cursor-not-allowed', isDarkMode ? 'border-gray-700 text-gray-400 hover:text-white hover:border-gray-500' : 'border-gray-200 text-gray-500 hover:text-gray-900 hover:border-gray-400']"
        >
          <ChevronRight size="18" />
        </button>
      </div>
    </div>

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
      :title="t('admin_confirm_delete')"
      :content="t('admin_delete_warning')"
      :confirm-text="t('admin_delete')"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
