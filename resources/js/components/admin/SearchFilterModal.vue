<script setup>
/**
 * SearchFilterModal.vue - 管理后台通用搜索筛选组件
 *
 * 功能说明：
 * - 提供搜索输入框和筛选下拉框
 * - 支持自定义筛选选项
 * - 响应式布局，移动端自动堆叠
 *
 * Props:
 * - searchQuery: 搜索关键词
 * - searchPlaceholder: 搜索框占位符
 * - filters: 筛选配置数组 [{ key, options: [{ value, label }] }]
 * - filterValues: 当前筛选值 { key: value }
 *
 * Events:
 * - update:searchQuery: 搜索关键词变化
 * - filter-change: 筛选条件变化 { key, value }
 *
 * 使用示例：
 * <SearchFilterModal
 *   v-model:search-query="searchQuery"
 *   :search-placeholder="t('admin_search_users')"
 *   :filters="[
 *     { key: 'role', options: roleOptions }
 *   ]"
 *   :filter-values="{ role: roleFilter }"
 *   @filter-change="handleFilterChange"
 * />
 */
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';
import { Search, Filter, ChevronDown, Check } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const props = defineProps({
  searchQuery: {
    type: String,
    default: ''
  },
  searchPlaceholder: {
    type: String,
    default: '搜索...'
  },
  filters: {
    type: Array,
    default: () => []
  },
  filterValues: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update:searchQuery', 'filter-change']);

const openDropdowns = ref({});

const handleSearchInput = (event) => {
  emit('update:searchQuery', event.target.value);
};

const handleFilterChange = (key, value) => {
  emit('filter-change', { key, value });
  openDropdowns.value[key] = false;
};

const handleCheckboxChange = (key, currentValue) => {
  emit('filter-change', { key, value: !currentValue });
};

const toggleDropdown = (key) => {
  openDropdowns.value[key] = !openDropdowns.value[key];
};

const getOptionLabel = (filter) => {
  if (!filter) return '';
  if (filter.type === 'checkbox') {
    return props.filterValues[filter.key] ? filter.checkedLabel : filter.uncheckedLabel;
  }
  const currentValue = props.filterValues[filter.key];
  const option = filter.options?.find(o => o.value === currentValue);
  return option ? option.label : filter.options?.[0]?.label || '';
};

const handleClickOutside = (event) => {
  Object.keys(openDropdowns.value).forEach(key => {
    if (openDropdowns.value[key]) {
      const dropdownEl = document.querySelector(`[data-dropdown="${key}"]`);
      if (dropdownEl && !dropdownEl.contains(event.target)) {
        openDropdowns.value[key] = false;
      }
    }
  });
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="flex flex-col md:flex-row gap-4 mb-10">
    <!-- Search Input -->
    <div class="flex-1 relative">
      <Search :class="['absolute left-4 top-1/2 -translate-y-1/2 transition-colors', isDarkMode ? 'text-gray-500' : 'text-gray-400']" size="20" />
      <input
        :value="searchQuery"
        @input="handleSearchInput"
        type="text"
        :placeholder="searchPlaceholder"
        :class="[
          'w-full pl-12 pr-4 py-4 border focus:border-construct-red focus:outline-none transition-all rounded-2xl font-bold',
          isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-500 shadow-inner' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-400 shadow-sm'
        ]"
      />
    </div>

    <!-- Filters -->
    <div v-if="filters.length > 0" class="flex items-center gap-4 flex-wrap">
      <template v-for="filter in filters" :key="filter.key">
        <template v-if="filter">
        <!-- Checkbox type filter -->
        <div v-if="filter.type === 'checkbox'" class="flex items-center gap-2">
          <label :class="['flex items-center gap-2 px-4 py-3 rounded-lg cursor-pointer transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
            <input
              :checked="filterValues[filter.key]"
              @change="handleCheckboxChange(filter.key, filterValues[filter.key])"
              type="checkbox"
              :class="['w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red']"
            />
            <span :class="['text-sm font-bold tracking-wider uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-600']">
              {{ getOptionLabel(filter) }}
            </span>
          </label>
        </div>
        <!-- Dropdown type filter -->
        <div v-else :data-dropdown="filter.key" class="relative">
          <Filter :class="['absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none transition-colors z-10', isDarkMode ? 'text-gray-500' : 'text-gray-400']" size="18" />
          <button
            @click.stop="toggleDropdown(filter.key)"
            :class="[
              'pl-12 pr-4 py-4 border focus:border-construct-red focus:outline-none transition-all rounded-2xl font-bold text-sm min-w-[160px] cursor-pointer flex items-center gap-2',
              isDarkMode
                ? 'bg-gray-800 border-gray-700 text-white shadow-inner hover:border-gray-600'
                : 'bg-white border-gray-200 text-gray-900 shadow-sm hover:border-gray-300'
            ]"
          >
            <span class="truncate flex-1">{{ getOptionLabel(filter) }}</span>
            <ChevronDown :class="['transition-transform flex-shrink-0', openDropdowns[filter.key] ? 'rotate-180' : '', isDarkMode ? 'text-gray-500' : 'text-gray-400']" size="16" />
          </button>

          <!-- Dropdown Menu -->
          <div
            v-show="openDropdowns[filter.key]"
            :class="[
              'absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border shadow-xl',
              isDarkMode
                ? 'bg-gray-800 border-gray-700 shadow-black/30'
                : 'bg-white border-gray-200 shadow-gray-200/50'
            ]"
          >
            <div class="py-2">
              <button
                v-for="option in filter.options"
                :key="option.value"
                @click.stop="handleFilterChange(filter.key, option.value)"
                :class="[
                  'w-full px-4 py-3 text-left text-sm font-bold flex items-center justify-between gap-2 transition-colors',
                  filterValues[filter.key] === option.value
                    ? (isDarkMode ? 'text-construct-red bg-construct-red/10' : 'text-construct-red bg-construct-red/5')
                    : (isDarkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-50')
                ]"
              >
                <span class="truncate">{{ option.label }}</span>
                <Check v-if="filterValues[filter.key] === option.value" size="16" class="text-construct-red flex-shrink-0" />
              </button>
            </div>
          </div>
        </div>
        </template>
      </template>
    </div>
  </div>
</template>
