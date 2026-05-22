<script setup>
/**
 * AdminTags.vue - 标签管理页面
 * 
 * 功能说明：
 * - 管理文章和视频的标签
 * - 标签列表展示（名称、使用次数、状态）
 * - 标签搜索和筛选
 * - 添加、编辑、删除标签
 * - 标签状态管理（启用/禁用）
 */
import {
  ref,
  computed,
  useI18n,
  useTheme,
  Tag,
  Plus,
  Search,
  Edit3,
  Trash2,
  Filter,
  FileText,
  adminTags,
  MetaForm,
  ConfirmDialog,
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const isFormVisible = ref(false);
const editingTag = ref(null);
const showDeleteConfirm = ref(false);
const deletingTagId = ref(null);

const tags = ref([...adminTags]);

const filteredTags = computed(() => {
  return tags.value.filter(tag => {
    const matchesSearch = tag.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || tag.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredTags.value.length / itemsPerPage.value));

const paginatedTags = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredTags.value.slice(start, start + itemsPerPage.value);
});

const toggleStatus = (tag) => {
  tag.status = tag.status === 'active' ? 'inactive' : 'active';
};

const handleEdit = (tag) => {
  editingTag.value = { ...tag };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingTag.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingTag.value) {
    const index = tags.value.findIndex(t => t.id === editingTag.value.id);
    if (index !== -1) {
      tags.value[index] = { ...tags.value[index], ...data };
    }
  } else {
    const newId = Math.max(...tags.value.map(t => t.id), 0) + 1;
    tags.value.push({
      id: newId,
      ...data,
      usageCount: 0
    });
  }
  isFormVisible.value = false;
  editingTag.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingTag.value = null;
};

const handleDelete = (id) => {
  deletingTagId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingTagId.value !== null) {
    tags.value = tags.value.filter(t => t.id !== deletingTagId.value);
    deletingTagId.value = null;
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
            <Tag class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_tags') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage content tags</p>
        </div>
        <button @click="handleAdd" class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl">
          <Plus size="18" class="!text-white" /> {{ t('admin_add_tag') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_tags')"
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

    <!-- Tags Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
      <div 
        v-for="tag in paginatedTags" 
        :key="tag.id"
        :class="[
          'group relative border p-5 transition-all duration-300 hover:shadow-lg hover:border-construct-red/30',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 backdrop-blur-md rounded-2xl' 
            : 'bg-white/80 border-gray-200/80 backdrop-blur-md rounded-2xl shadow-sm'
        ]"
      >
        <div class="flex flex-col h-full">
          <div class="flex items-start justify-between mb-4">
            <div :class="[
              'px-3 py-1.5 rounded-xl flex items-center gap-2 border transition-all duration-300 group-hover:scale-105',
              isDarkMode 
                ? 'bg-gray-900/50 border-gray-700 text-white' 
                : 'bg-gray-50 border-gray-100 text-gray-900'
            ]">
              <Tag size="14" class="text-construct-red" />
              <span class="font-bold text-sm tracking-tight truncate max-w-[80px]">{{ tag.name }}</span>
            </div>
            
            <button
              @click="toggleStatus(tag)"
              class="flex items-center cursor-pointer"
            >
              <div :class="[
                'w-2 h-2 rounded-full',
                tag.status === 'active' ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-gray-400'
              ]"></div>
            </button>
          </div>
          
          <div :class="['mt-auto flex items-center justify-between pt-4 border-t border-dashed', isDarkMode ? 'border-gray-700' : 'border-gray-100']">
            <div :class="['flex items-center gap-1.5 text-[10px] font-black tracking-widest uppercase', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              <FileText size="12" />
              <span>{{ tag.usageCount }}</span>
            </div>
            <div class="flex items-center gap-1">
              <button 
                @click="handleEdit(tag)" 
                :class="['p-1.5 rounded-lg transition-all duration-300 hover:scale-110', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900']"
              >
                <Edit3 size="14" />
              </button>
              <button 
                @click="handleDelete(tag.id)" 
                :class="['p-1.5 rounded-lg transition-all duration-300 hover:scale-110', isDarkMode ? 'text-gray-400 hover:bg-red-500/10 hover:text-red-400' : 'text-gray-500 hover:bg-red-50 hover:text-red-600']"
              >
                <Trash2 size="14" />
              </button>
            </div>
          </div>
        </div>

        <!-- Status Tooltip/Overlay (Optional subtle indicator) -->
        <div 
          v-if="tag.status === 'inactive'"
          class="absolute inset-0 bg-gray-900/10 backdrop-grayscale rounded-2xl pointer-events-none transition-opacity duration-300"
          :class="isDarkMode ? 'opacity-40' : 'opacity-20'"
        ></div>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredTags.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Meta Form Modal -->
    <MetaForm
      type="tag"
      :edit-data="editingTag"
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
