<script setup>
/**
 * AdminCategories.vue - 分类管理页面
 * 
 * 功能说明：
 * - 管理文章和视频的分类
 * - 分类列表展示（名称、描述、文章数、状态）
 * - 分类搜索和筛选
 * - 添加、编辑、删除分类
 * - 分类状态管理（启用/禁用）
 */
import {
  ref,
  computed,
  watch,
  router,
  useI18n,
  useTheme,
  Folder,
  Plus,
  Edit3,
  Trash2,
  MetaForm,
  ConfirmDialog,
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { Motion } from 'motion-v';

const props = defineProps({
  categories: {
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

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const isFormVisible = ref(false);
const editingCategory = ref(null);
const showDeleteConfirm = ref(false);
const deletingCategoryId = ref(null);

// 本地状态用于管理数据变更
const localCategories = ref([...props.categories]);

// 当 props 变化时更新本地状态
watch(() => props.categories, (newCategories) => {
  localCategories.value = [...newCategories];
}, { immediate: true });

const filteredCategories = computed(() => {
  return localCategories.value.filter(category => {
    const matchesSearch = category.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         category.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || category.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredCategories.value.length / itemsPerPage.value));

const paginatedCategories = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredCategories.value.slice(start, start + itemsPerPage.value);
});

const toggleStatus = (category) => {
  const newStatus = category.status === 'active' ? 'inactive' : 'active';
  router.put(`/admin/categories/${category.id}`, { ...category, status: newStatus }, {
    onSuccess: () => {
      category.status = newStatus;
    },
  });
};

const handleEdit = (category) => {
  editingCategory.value = { ...category };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingCategory.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingCategory.value) {
    router.put(`/admin/categories/${editingCategory.value.id}`, data, {
      onSuccess: () => {
        isFormVisible.value = false;
        editingCategory.value = null;
      },
    });
  } else {
    router.post('/admin/categories', data, {
      onSuccess: () => {
        isFormVisible.value = false;
        editingCategory.value = null;
      },
    });
  }
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingCategory.value = null;
};

const handleDelete = (id) => {
  deletingCategoryId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingCategoryId.value !== null) {
    router.delete(`/admin/categories/${deletingCategoryId.value}`, {
      onSuccess: () => {
        localCategories.value = localCategories.value.filter(c => c.id !== deletingCategoryId.value);
        deletingCategoryId.value = null;
      },
    });
  }
  showDeleteConfirm.value = false;
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'status') {
    statusFilter.value = value;
  }
  currentPage.value = 1;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Folder class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_categories') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage content categories</p>
        </div>
        <button @click="handleAdd" class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl">
          <Plus size="18" class="!text-white" /> {{ t('admin_add_category') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_categories')"
      :filters="[
        {
          key: 'status',
          options: [
            { value: 'all', label: t('admin_all_status') },
            { value: 'active', label: t('admin_active') },
            { value: 'inactive', label: t('admin_inactive') }
          ]
        }
      ]"
      :filter-values="{ status: statusFilter }"
      @filter-change="handleFilterChange"
    />

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <Motion
        v-for="(category, index) in paginatedCategories" 
        :key="category.id"
        :initial="{ opacity: 0, y: 24 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ duration: 0.35, delay: index * 0.06, ease: 'easeOut' }"
        :whileHover="{ scale: 1.02, y: -3, boxShadow: '0 16px 40px rgba(0,0,0,0.14)' }"
        :whileTap="{ scale: 0.98 }"
        :class="[
          'group relative flex flex-col rounded-xl overflow-hidden cursor-default',
          isDarkMode 
            ? 'bg-gray-800/60 border border-gray-700/50 shadow-[0_4px_20px_rgba(0,0,0,0.2)]'
            : 'bg-white border border-gray-200/60 shadow-[0_4px_20px_rgba(0,0,0,0.04)]'
        ]"
      >
        <!-- Card Background Accent -->
        <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-10 transition-opacity duration-500 pointer-events-none">
          <Folder size="80" class="rotate-12" />
        </div>

        <!-- Card Body -->
        <div class="p-5 pb-3 flex flex-col flex-1">
          <!-- Top Row: Icon + Status -->
          <div class="flex items-start justify-between mb-4">
            <div :class="[
              'w-12 h-12 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6',
              isDarkMode 
                ? 'bg-gradient-to-br from-construct-red/20 to-construct-red/10 text-construct-red' 
                : 'bg-gradient-to-br from-construct-red/10 to-construct-red/5 text-construct-red'
            ]">
              <Folder size="24" />
            </div>
            <button
              @click="toggleStatus(category)"
              class="flex items-center gap-1 cursor-pointer z-10 shrink-0"
              :title="category.status === 'active' ? t('admin_active') : t('admin_inactive')"
            >
              <div :class="[
                'w-10 h-5 rounded-full relative transition-all duration-300',
                category.status === 'active' ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-300')
              ]">
                <div :class="[
                  'absolute top-0.5 w-4 h-4 rounded-full bg-white shadow-sm transition-all duration-300',
                  category.status === 'active' ? 'left-[22px]' : 'left-0.5'
                ]"></div>
              </div>
            </button>
          </div>

          <!-- Name -->
          <h3 :class="[
            'font-bold text-lg mb-1.5 truncate transition-colors duration-300',
            isDarkMode ? 'text-white group-hover:text-construct-red' : 'text-gray-900 group-hover:text-construct-red'
          ]">
            {{ category.name }}
          </h3>

          <!-- Slug -->
          <p :class="['text-xs mb-2 truncate', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            {{ category.slug }}
          </p>

          <!-- Description -->
          <p :class="[
            'text-xs line-clamp-2 leading-relaxed flex-1',
            isDarkMode ? 'text-gray-400' : 'text-gray-500'
          ]">
            {{ category.description || 'No description' }}
          </p>
        </div>

        <!-- Bottom Bar: Badges + Actions -->
        <div class="flex items-center justify-between px-5 py-3 mt-auto">
          <div class="flex gap-1.5 text-[11px] font-bold">
            <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="category.posts_count">
              📝 {{ category.posts_count }}
            </span>
            <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="category.videos_count">
              🎬 {{ category.videos_count }}
            </span>
            <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="category.projects_count">
              📁 {{ category.projects_count }}
            </span>
            <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="category.resources_count">
              📦 {{ category.resources_count }}
            </span>
          </div>
          <div class="flex gap-0.5">
            <button 
              @click="handleEdit(category)" 
              :class="['p-2 rounded-lg transition-all duration-200 hover:scale-110', isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-400 hover:text-blue-500 hover:bg-gray-200']"
            >
              <Edit3 size="14" />
            </button>
            <button 
              @click="handleDelete(category.id)" 
              :class="['p-2 rounded-lg transition-all duration-200 hover:scale-110', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700/50' : 'text-gray-400 hover:text-red-500 hover:bg-red-50']"
            >
              <Trash2 size="14" />
            </button>
          </div>
        </div>
      </Motion>
    </div>

    <!-- Pagination -->
    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredCategories.length"
      :items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Meta Form Modal -->
    <MetaForm
      type="category"
      :edit-data="editingCategory"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      type="delete"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
