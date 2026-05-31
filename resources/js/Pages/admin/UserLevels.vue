<script setup>
/**
 * UserLevels.vue - 用户等级管理页面
 *
 * 功能说明：
 * - 管理系统用户等级
 * - 等级列表展示（名称、等级、折扣、颜色、状态）
 * - 等级搜索和筛选
 * - 添加、编辑、删除等级
 */
import {
  ref,
  computed,
  watch,
  useI18n,
  useTheme,
  Crown,
  Plus,
  Search,
  Edit3,
  Trash2,
  X,
  Filter,
  Check,
  ChevronUp,
  ChevronDown,
  UserLevelForm,
  ConfirmDialog,
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  levels: {
    type: Array,
    default: () => []
  }
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const activeFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 6;
const isFormVisible = ref(false);
const editingLevel = ref(null);
const showDeleteConfirm = ref(false);
const deletingLevelId = ref(null);

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
    router.put(route('admin.user-levels.update', editingLevel.value.id), data);
  } else {
    router.post(route('admin.user-levels.store'), data);
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
    router.delete(route('admin.user-levels.destroy', deletingLevelId.value));
    deletingLevelId.value = null;
  }
  showDeleteConfirm.value = false;
};

const moveUp = (level) => {
  const index = filteredLevels.value.findIndex(l => l.id === level.id);
  if (index > 0) {
    const actualIndex = levels.value.findIndex(l => l.id === level.id);
    const newSortOrder = levels.value[actualIndex - 1].sort_order;
    router.put(route('admin.user-levels.update', level.id), {
      sort_order: newSortOrder,
    });
    router.put(route('admin.user-levels.update', levels.value[actualIndex - 1].id), {
      sort_order: level.sort_order,
    });
  }
};

const moveDown = (level) => {
  const index = filteredLevels.value.findIndex(l => l.id === level.id);
  if (index < filteredLevels.value.length - 1) {
    const actualIndex = levels.value.findIndex(l => l.id === level.id);
    const newSortOrder = levels.value[actualIndex + 1].sort_order;
    router.put(route('admin.user-levels.update', level.id), {
      sort_order: newSortOrder,
    });
    router.put(route('admin.user-levels.update', levels.value[actualIndex + 1].id), {
      sort_order: level.sort_order,
    });
  }
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
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Crown class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ t('admin_user_levels') }}
            </h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            {{ t('admin_user_levels_subtitle') }}
          </p>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus size="18" />
          {{ t('admin_add_level') }}
        </button>
      </div>
    </div>

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

    <div :class="['border overflow-hidden', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
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
            v-for="(level, index) in paginatedLevels"
            :key="level.id"
            :class="['border-t transition-colors', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-200 hover:bg-gray-50']"
          >
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-1">
                <button
                  @click="moveUp(level)"
                  :disabled="index === 0"
                  :class="['p-1 rounded transition-colors', index === 0 ? 'opacity-20 cursor-not-allowed' : (isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100')]"
                >
                  <ChevronUp size="14" />
                </button>
                <span class="text-xs font-bold w-4 text-center">{{ index + 1 }}</span>
                <button
                  @click="moveDown(level)"
                  :disabled="index === paginatedLevels.length - 1"
                  :class="['p-1 rounded transition-colors', index === paginatedLevels.length - 1 ? 'opacity-20 cursor-not-allowed' : (isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100')]"
                >
                  <ChevronDown size="14" />
                </button>
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
              <div class="flex items-center justify-center gap-2">
                <button
                  @click="handleEdit(level)"
                  :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']"
                >
                  <Edit3 size="16" />
                </button>
                <button
                  @click="handleDelete(level.id)"
                  :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']"
                >
                  <Trash2 size="16" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredLevels.length"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage = page"
    />

    <UserLevelForm
      :edit-data="editingLevel"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

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
