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
  <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 mb-10 h-14">
    <!-- Search Input Container -->
    <div class="flex-1 relative group h-full">
      <Search :class="[
        'absolute left-5 top-1/2 -translate-y-1/2 transition-all duration-300 group-focus-within:text-construct-red z-10', 
        isDarkMode ? 'text-gray-500' : 'text-gray-400'
      ]" size="20" />
      <input
        :value="searchQuery"
        @input="handleSearchInput"
        type="text"
        :placeholder="searchPlaceholder"
        :class="[
          'w-full h-full pl-14 pr-6 py-2 border-2 focus:outline-none transition-all duration-300 rounded-2xl font-bold tracking-tight backdrop-blur-xl',
          isDarkMode 
            ? 'bg-gray-900/40 border-gray-800/50 text-white placeholder-gray-600 focus:border-construct-red/50 focus:bg-gray-900/60 focus:ring-4 focus:ring-construct-red/10' 
            : 'bg-white/40 border-white text-gray-900 placeholder-gray-400 shadow-xl shadow-gray-200/20 focus:border-construct-red/30 focus:ring-4 focus:ring-construct-red/5'
        ]"
      />
    </div>

    <!-- Filter Icon (Between Search and Filters) -->
    <div v-if="filters.length > 0" :class="[
      'w-14 h-full rounded-2xl border-2 transition-all duration-300 hidden md:flex items-center justify-center shrink-0 backdrop-blur-xl',
      isDarkMode 
        ? 'bg-gray-900/40 border-gray-800/50 text-gray-500' 
        : 'bg-white/40 border-white text-gray-400 shadow-xl shadow-gray-200/20'
    ]">
      <Filter size="20" />
    </div>

    <!-- Filters List -->
    <div v-if="filters.length > 0" class="flex items-center gap-3 flex-wrap h-full">
      <template v-for="filter in filters" :key="filter.key">
        <template v-if="filter">
        <!-- Checkbox type filter -->
        <div v-if="filter.type === 'checkbox'" class="h-full">
          <label :class="[
            'flex items-center h-full gap-2.5 px-4 rounded-xl border-2 cursor-pointer transition-all duration-300 select-none group backdrop-blur-xl',
            filterValues[filter.key]
              ? (isDarkMode ? 'border-construct-red/50 bg-construct-red/20 text-construct-red' : 'border-construct-red/30 bg-construct-red/10 text-construct-red')
              : (isDarkMode ? 'border-gray-800/50 bg-gray-900/40 hover:border-gray-700 text-gray-500 hover:text-gray-300' : 'border-white bg-white/40 hover:bg-white/60 text-gray-500 hover:text-gray-700 shadow-xl shadow-gray-200/20')
          ]">
            <div :class="[
              'w-4 h-4 rounded-md border-2 flex items-center justify-center transition-all duration-300 group-hover:scale-110',
              filterValues[filter.key] ? 'bg-construct-red border-construct-red' : (isDarkMode ? 'border-gray-700' : 'border-gray-300')
            ]">
              <Check v-if="filterValues[filter.key]" size="12" class="text-white font-bold" />
            </div>
            <input
              :checked="filterValues[filter.key]"
              @change="handleCheckboxChange(filter.key, filterValues[filter.key])"
              type="checkbox"
              class="hidden"
            />
            <span class="text-[10px] font-black tracking-widest uppercase">
              {{ getOptionLabel(filter) }}
            </span>
          </label>
        </div>
        <!-- Dropdown type filter -->
        <div v-else :data-dropdown="filter.key" class="relative h-full">
          <button
            @click.stop="toggleDropdown(filter.key)"
            :class="[
              'px-4 h-full border-2 focus:outline-none transition-all duration-300 rounded-xl font-bold text-sm min-w-[120px] cursor-pointer flex items-center gap-2 backdrop-blur-xl',
              openDropdowns[filter.key]
                ? (isDarkMode ? 'border-construct-red/50 bg-construct-red/20 text-construct-red' : 'border-construct-red/30 bg-construct-red/10 text-construct-red')
                : (isDarkMode ? 'border-gray-800/50 bg-gray-900/40 hover:border-gray-700 text-gray-400' : 'border-white bg-white/40 hover:bg-white/60 text-gray-600 shadow-xl shadow-gray-200/20')
            ]"
          >
            <span class="truncate flex-1 text-[10px] font-black tracking-widest uppercase">{{ getOptionLabel(filter) }}</span>
            <ChevronDown :class="['transition-transform duration-300 flex-shrink-0', openDropdowns[filter.key] ? 'rotate-180 text-construct-red' : 'opacity-40']" size="14" />
          </button>

          <!-- Dropdown Menu -->
          <div
            v-show="openDropdowns[filter.key]"
            :class="[
              'absolute z-50 mt-2 w-full min-w-[140px] overflow-hidden rounded-xl border-2 shadow-2xl backdrop-blur-2xl transition-all duration-300',
              isDarkMode
                ? 'bg-gray-900/90 border-gray-800 shadow-black/50'
                : 'bg-white/90 border-white shadow-gray-200/50'
            ]"
          >
            <div class="py-1">
              <button
                v-for="option in filter.options"
                :key="option.value"
                @click.stop="handleFilterChange(filter.key, option.value)"
                :class="[
                  'w-full px-4 py-2 text-left text-[10px] font-black tracking-widest uppercase flex items-center justify-between gap-2 transition-colors',
                  filterValues[filter.key] === option.value
                    ? (isDarkMode ? 'text-construct-red bg-construct-red/10' : 'text-construct-red bg-construct-red/5')
                    : (isDarkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-50')
                ]"
              >
                <span class="truncate">{{ option.label }}</span>
                <Check v-if="filterValues[filter.key] === option.value" size="12" class="text-construct-red flex-shrink-0" />
              </button>
            </div>
          </div>
        </div>
        </template>
      </template>
    </div>
  </div>
</template>
