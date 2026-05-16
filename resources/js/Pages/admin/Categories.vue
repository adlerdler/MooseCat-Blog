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
  useI18n,
  useTheme,
  Folder,
  Plus,
  Search,
  Edit3,
  Trash2,
  Filter,
  FileText,
  adminCategories,
  MetaForm,
  ConfirmDialog,
  AdminPagination
} from '../../composables/useAdminImports';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const isFormVisible = ref(false);
const editingCategory = ref(null);
const showDeleteConfirm = ref(false);
const deletingCategoryId = ref(null);

const categories = ref([...adminCategories]);

const filteredCategories = computed(() => {
  return categories.value.filter(category => {
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
  category.status = category.status === 'active' ? 'inactive' : 'active';
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
    const index = categories.value.findIndex(c => c.id === editingCategory.value.id);
    if (index !== -1) {
      categories.value[index] = { ...categories.value[index], ...data };
    }
  } else {
    const newId = Math.max(...categories.value.map(c => c.id), 0) + 1;
    categories.value.push({
      id: newId,
      ...data,
      postCount: 0
    });
  }
  isFormVisible.value = false;
  editingCategory.value = null;
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
    categories.value = categories.value.filter(c => c.id !== deletingCategoryId.value);
    deletingCategoryId.value = null;
  }
  showDeleteConfirm.value = false;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-2">
        <Folder class="text-construct-red" size="32" />
        <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_categories') }}</h2>
      </div>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage content categories</p>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_categories')"
          :class="[
            'w-full pl-12 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
        />
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <Filter :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
          <select
            v-model="statusFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="all">{{ t('admin_all_status') }}</option>
            <option value="active">{{ t('admin_active') }}</option>
            <option value="inactive">{{ t('admin_inactive') }}</option>
          </select>
        </div>
        <button @click="handleAdd" class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded">
          <Plus size="16" class="!text-white" /> {{ t('admin_add_category') }}
        </button>
      </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div 
        v-for="category in paginatedCategories" 
        :key="category.id"
        :class="[
          'group relative border p-6 transition-all duration-500 hover:-translate-y-2',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/50 backdrop-blur-xl shadow-[0_8px_32px_0_rgba(0,0,0,0.3)]' 
            : 'bg-white/80 border-gray-200/80 hover:border-construct-red/30 backdrop-blur-md shadow-[0_8px_32px_0_rgba(31,38,135,0.07)]'
        ]"
      >
        <!-- Card Background Accent -->
        <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-10 transition-opacity duration-500 pointer-events-none">
          <Folder size="80" class="rotate-12" />
        </div>

        <div class="flex items-start justify-between mb-6">
          <div :class="[
            'w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg',
            isDarkMode 
              ? 'bg-gradient-to-br from-gray-700 to-gray-800 text-construct-red' 
              : 'bg-gradient-to-br from-gray-50 to-gray-100 text-construct-red'
          ]">
            <Folder size="28" />
          </div>
          <button
            @click="toggleStatus(category)"
            class="flex items-center gap-1 cursor-pointer z-10"
            :title="category.status === 'active' ? t('admin_active') : t('admin_inactive')"
          >
            <div :class="[
              'w-11 h-6 rounded-full relative transition-all duration-300 shadow-inner',
              category.status === 'active' ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-700' : 'bg-gray-300')
            ]">
              <div :class="[
                'absolute top-1 w-4 h-4 rounded-full bg-white shadow-md transition-all duration-300 flex items-center justify-center',
                category.status === 'active' ? 'left-6' : 'left-1'
              ]">
                <div v-if="category.status === 'active'" class="w-1.5 h-1.5 rounded-full bg-construct-red animate-pulse"></div>
              </div>
            </div>
          </button>
        </div>
        
        <div class="mb-4">
          <h3 :class="['font-display text-2xl tracking-tighter mb-2 group-hover:text-construct-red transition-colors', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ category.name }}
          </h3>
          <p :class="['text-sm line-clamp-2 leading-relaxed', isDarkMode ? 'text-gray-400' : 'text-gray-600']">
            {{ category.description || 'No description provided for this category.' }}
          </p>
        </div>
        
        <div :class="['flex items-center justify-between pt-5 border-t mt-auto', isDarkMode ? 'border-gray-700/50' : 'border-gray-100']">
          <div :class="['flex items-center gap-2 text-xs font-black tracking-widest uppercase', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            <div class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-500/10">
              <FileText size="12" />
            </div>
            <span>{{ category.postCount }} {{ t('admin_posts') }}</span>
          </div>
          <div class="flex items-center gap-2">
            <button 
              @click="handleEdit(category)" 
              :class="['p-2.5 rounded-xl transition-all duration-300 hover:scale-110', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900']"
            >
              <Edit3 size="18" />
            </button>
            <button 
              @click="handleDelete(category.id)" 
              :class="['p-2.5 rounded-xl transition-all duration-300 hover:scale-110', isDarkMode ? 'text-gray-400 hover:bg-red-500/10 hover:text-red-400' : 'text-gray-500 hover:bg-red-50 hover:text-red-600']"
            >
              <Trash2 size="18" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <AdminPagination
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
      title="确认删除"
      content="确定要删除这个分类吗？此操作不可撤销。"
      confirm-text="删除"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
