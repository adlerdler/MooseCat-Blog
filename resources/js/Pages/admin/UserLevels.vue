<script setup>
/**
 * UserLevels.vue - 用户等级管理页面
 *
 * 功能说明：
 * - 管理系统用户等级
 * - 等级列表展示（名称、等级、折扣、颜色、状态）
 * - 等级搜索和筛选
 * - 添加、编辑、删除等级
 * - 拖拽排序
 */
import {
  ref,
  computed,
  watch,
  useI18n,
  useTheme,
  useToast,
  Crown,
  Plus,
  Search,
  Edit3,
  Trash2,
  X,
  Filter,
  LayoutGrid,
  List,
  GripVertical,
  UserLevelForm,
  ConfirmDialog,
  Pagination,
  EmptyState,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { router, useForm } from '@inertiajs/vue3';
import { useDragSort } from '../../composables/useDragSort';

const props = defineProps({
  levels: {
    type: Array,
    default: () => []
  }
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const searchQuery = ref('');
const activeFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 6;
const isFormVisible = ref(false);
const editingLevel = ref(null);
const showDeleteConfirm = ref(false);
const deletingLevelId = ref(null);

// Load saved view mode from localStorage
const savedViewMode = localStorage.getItem('admin_user_levels_view_mode');
const viewMode = ref(savedViewMode || 'card');

// Save view mode when changed
watch(viewMode, (newMode) => {
  localStorage.setItem('admin_user_levels_view_mode', newMode);
});

// Use drag sort composable
const { handleDragStart, handleDragOver, handleDragEnd } = useDragSort({
  batchUpdateUrl: route('admin.user-levels.batch-update'),
  onUpdateSuccess: () => toastSuccess(t('toast.sort_updated')),
  onUpdateError: (err) => {
    console.error('UserLevels sort update error:', err);
    toastError(t('toast.sort_update_error'));
  },
  debounceDelay: 800,
  itemKey: 'id',
  sortField: 'sort_order',
  dataKey: 'levels'
});

const levels = ref([...props.levels]);

watch(() => props.levels, (newLevels) => {
  levels.value = [...newLevels];
});

const filteredLevels = computed(() => {
  return levels.value.filter(level => {
    const matchesSearch = level.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         level.description?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesActive = activeFilter.value === 'all' || 
                         (activeFilter.value === 'active' && level.is_active) ||
                         (activeFilter.value === 'inactive' && !level.is_active);
    return matchesSearch && matchesActive;
  });
});

const totalPages = computed(() => Math.ceil(filteredLevels.value.length / itemsPerPage));

const paginatedLevels = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredLevels.value.slice(start, start + itemsPerPage);
});

const handleEdit = (level) => {
  editingLevel.value = { ...level };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingLevel.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingLevel.value) {
    router.put(route('admin.user-levels.update', editingLevel.value.id), data, {
      preserveState: true,
      onSuccess: () => toastSuccess(t('toast.update_success')),
      onError: (err) => {
        console.error('UserLevel update error:', err);
        toastError(t('toast.update_error'));
      },
    });
  } else {
    router.post(route('admin.user-levels.store'), data, {
      preserveState: true,
      onSuccess: () => toastSuccess(t('toast.create_success')),
      onError: (err) => {
        console.error('UserLevel create error:', err);
        toastError(t('toast.create_error'));
      },
    });
  }
  isFormVisible.value = false;
  editingLevel.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingLevel.value = null;
};

const handleDelete = (id) => {
  deletingLevelId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingLevelId.value !== null) {
    router.delete(route('admin.user-levels.destroy', deletingLevelId.value), {
      preserveState: true,
      onSuccess: () => toastSuccess(t('toast.delete_success')),
      onError: (err) => {
        console.error('UserLevel delete error:', err);
        toastError(t('toast.delete_error'));
      },
    });
    deletingLevelId.value = null;
  }
  showDeleteConfirm.value = false;
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'active') {
    activeFilter.value = value;
  }
  currentPage.value = 1;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Crown class="text-construct-red" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_user_levels') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_user_levels_subtitle') }}</p>
          </div>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-wider rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-all"
        >
          <Plus size="18" />
          {{ t('admin_add_level') }}
        </button>
      </div>
    </div>

    <!-- Search and View Toggle -->
    <div class="flex items-center gap-4 mt-6">
      <div class="flex-1">
        <div class="-mb-10">
          <SearchFilterModal
            v-model:search-query="searchQuery"
            :search-placeholder="t('admin_search_levels')"
            :filters="[
              {
                key: 'active',
                options: [
                  { value: 'all', label: t('admin_all_status') || '所有状态' },
                  { value: 'active', label: t('admin_active') },
                  { value: 'inactive', label: t('admin_inactive') }
                ]
              }
            ]"
            :filter-values="{ active: activeFilter }"
            @filter-change="handleFilterChange"
          />
        </div>
      </div>
      <!-- View Toggle -->
      <div class="flex items-center gap-1 p-1 rounded-xl flex-shrink-0 h-14" :class="isDarkMode ? 'bg-gray-800' : 'bg-gray-100'">
        <button
          @click="viewMode = 'card'"
          :class="['p-3 rounded-lg transition-all h-full flex items-center justify-center', viewMode === 'card' ? 'bg-white dark:bg-gray-700 shadow-sm text-construct-red' : 'text-gray-400 hover:text-gray-600']"
          title="Card View"
        >
          <LayoutGrid size="18" />
        </button>
        <button
          @click="viewMode = 'table'"
          :class="['p-3 rounded-lg transition-all h-full flex items-center justify-center', viewMode === 'table' ? 'bg-white dark:bg-gray-700 shadow-sm text-construct-red' : 'text-gray-400 hover:text-gray-600']"
          title="Table View"
        >
          <List size="18" />
        </button>
      </div>
    </div>

    <!-- Levels Cards -->
    <template v-if="filteredLevels.length > 0">
      <div v-if="viewMode === 'card'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
        <div
          v-for="level in paginatedLevels"
          :key="level.id"
          :class="[
            'p-6 rounded-xl transition-all duration-200 hover:scale-[1.02] cursor-move',
            isDarkMode ? 'bg-gray-800/80 hover:bg-gray-800' : 'bg-white hover:bg-gray-50'
          ]"
          draggable="true"
          @dragstart="handleDragStart($event, level)"
          @dragover="(e) => handleDragOver(e, level, levels)"
          @dragend="() => handleDragEnd(levels)"
        >
          <!-- Level Header -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['w-12 h-12 rounded-xl flex items-center justify-center shadow-md']" :style="{ backgroundColor: level.color }">
                <span class="font-bold text-white text-lg">{{ level.level }}</span>
              </div>
              <div>
                <h4 :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ level.name }}</h4>
                <p :class="['text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ level.description }}</p>
              </div>
            </div>
            <GripVertical :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="20" />
          </div>

          <!-- Discount -->
          <div class="mb-4">
            <span v-if="level.discount > 0" :class="['px-3 py-1 rounded-full text-xs font-bold', isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-50 text-green-600']">
              {{ level.discount }}% {{ t('admin_discount') }}
            </span>
            <span v-else :class="['text-sm', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ t('admin_no_discount') }}
            </span>
          </div>

          <!-- Status -->
          <div class="flex items-center justify-between">
            <span
              :class="[
                'px-3 py-1 rounded-full text-xs font-bold',
                level.is_active
                  ? (isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-50 text-green-600')
                  : (isDarkMode ? 'bg-red-500/20 text-red-400' : 'bg-red-50 text-red-600')
              ]"
            >
              {{ level.is_active ? t('admin_active') : t('admin_inactive') }}
            </span>
            <div class="flex items-center gap-1">
              <button @click="handleEdit(level)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
                <Edit3 size="16" />
              </button>
              <button @click="handleDelete(level.id)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-red-500/20 text-gray-400 hover:text-red-400' : 'hover:bg-red-50 text-gray-500 hover:text-red-500']">
                <Trash2 size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Levels Table -->
      <div v-if="viewMode === 'table'" :class="['mt-6 rounded-xl overflow-hidden', isDarkMode ? 'bg-gray-800/80' : 'bg-white']">
        <table class="w-full">
          <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-100/50'">
            <tr>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_order') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_level') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_name') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_discount') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_color') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_status') }}</th>
              <th :class="['px-6 py-4 text-center text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="level in paginatedLevels"
              :key="level.id"
              :class="['transition-colors cursor-move', isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50']"
              draggable="true"
              @dragstart="handleDragStart($event, level)"
              @dragover="(e) => handleDragOver(e, level, levels)"
              @dragend="() => handleDragEnd(levels)"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <GripVertical :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="16" />
                  <span class="text-xs font-bold">{{ level.sort_order }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div :class="['w-10 h-10 rounded-lg flex items-center justify-center font-bold text-white']" :style="{ backgroundColor: level.color }">
                  {{ level.level }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div>
                  <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ level.name }}</span>
                  <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ level.description }}</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <span v-if="level.discount > 0" :class="['px-3 py-1 rounded-full text-xs font-bold', isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-50 text-green-600']">
                  {{ level.discount }}% {{ t('admin_discount') }}
                </span>
                <span v-else :class="['text-sm', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                  {{ t('admin_no_discount') }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full border-2" :style="{ borderColor: level.color, backgroundColor: level.color + '40' }" />
                  <span :class="['text-xs font-mono', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ level.color }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-bold',
                    level.is_active
                      ? (isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-50 text-green-600')
                      : (isDarkMode ? 'bg-red-500/20 text-red-400' : 'bg-red-50 text-red-600')
                  ]"
                >
                  {{ level.is_active ? t('admin_active') : t('admin_inactive') }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-1">
                  <button @click="handleEdit(level)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
                    <Edit3 size="16" />
                  </button>
                  <button @click="handleDelete(level.id)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-red-500/20 text-gray-400 hover:text-red-400' : 'hover:bg-red-50 text-gray-500 hover:text-red-500']">
                    <Trash2 size="16" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredLevels.length"
        :items-per-page="itemsPerPage"
        @update:current-page="(page) => currentPage = page"
      />
    </template>

    <!-- Empty State -->
    <template v-else>
      <div class="mt-8">
        <EmptyState
          :title="t('admin_no_user_levels_found')"
          :description="t('admin_no_user_levels_description')"
          :icon="Crown"
        />
      </div>
    </template>

    <!-- User Level Form -->
    <UserLevelForm
      :edit-data="editingLevel"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      :title="t('admin_confirm_delete')"
      :content="t('admin_delete_level_warning')"
      :confirm-text="t('admin_delete')"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
