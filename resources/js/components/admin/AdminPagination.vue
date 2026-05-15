<script setup>
/**
 * AdminPagination.vue - 后台通用分页组件
 * 
 * 功能说明：
 * - 提供统一的分页导航功能
 * - 支持深色/浅色模式
 * - 显示当前页、总页数和总记录数
 * - 支持国际化翻译
 * - 支持自定义每页显示条数
 * - 支持页码导航按钮（智能省略号处理）
 * - 支持首页/末页快速跳转
 * - 支持输入页码直接跳转
 */
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight, ChevronDown, Check } from 'lucide-vue-next';

const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  },
  totalItems: {
    type: Number,
    required: true
  },
  itemsPerPage: {
    type: Number,
    default: 6
  },
  label: {
    type: String,
    default: 'items'
  },
  pageSizeOptions: {
    type: Array,
    default: () => [6, 10, 20, 50]
  },
  maxVisiblePages: {
    type: Number,
    default: 5
  }
});

const emit = defineEmits(['update:currentPage', 'update:itemsPerPage']);

const { t } = useI18n();
const { isDarkMode } = useTheme();

const jumpInput = ref('');
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const selectPageSize = (size) => {
  emit('update:itemsPerPage', size);
  emit('update:currentPage', 1);
  isDropdownOpen.value = false;
};

const closeDropdown = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isDropdownOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', closeDropdown);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', closeDropdown);
});

const showPagination = computed(() => {
  return props.totalItems >= 6;
});

const startItem = computed(() => {
  if (props.totalItems === 0) return 0;
  return ((props.currentPage - 1) * props.itemsPerPage) + 1;
});

const endItem = computed(() => {
  return Math.min(props.currentPage * props.itemsPerPage, props.totalItems);
});

const visiblePages = computed(() => {
  const pages = [];
  const { currentPage, totalPages, maxVisiblePages } = props;
  
  if (totalPages <= maxVisiblePages + 2) {
    for (let i = 1; i <= totalPages; i++) {
      pages.push(i);
    }
    return pages;
  }
  
  const halfVisible = Math.floor(maxVisiblePages / 2);
  let start = Math.max(2, currentPage - halfVisible);
  let end = Math.min(totalPages - 1, currentPage + halfVisible);
  
  if (currentPage - halfVisible <= 2) {
    end = maxVisiblePages;
  }
  
  if (currentPage + halfVisible >= totalPages - 1) {
    start = totalPages - maxVisiblePages + 1;
  }
  
  pages.push(1);
  
  if (start > 2) {
    pages.push('...');
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  if (end < totalPages - 1) {
    pages.push('...');
  }
  
  if (totalPages > 1) {
    pages.push(totalPages);
  }
  
  return pages;
});

const prevPage = () => {
  if (props.currentPage > 1) {
    emit('update:currentPage', props.currentPage - 1);
  }
};

const nextPage = () => {
  if (props.currentPage < props.totalPages) {
    emit('update:currentPage', props.currentPage + 1);
  }
};

const goToPage = (page) => {
  if (page === '...') return;
  const pageNum = typeof page === 'string' ? parseInt(page) : page;
  if (pageNum >= 1 && pageNum <= props.totalPages) {
    emit('update:currentPage', pageNum);
  }
};

const goToFirst = () => {
  emit('update:currentPage', 1);
};

const goToLast = () => {
  emit('update:currentPage', props.totalPages);
};

const handleJumpInput = () => {
  const pageNum = parseInt(jumpInput.value);
  if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= props.totalPages) {
    emit('update:currentPage', pageNum);
    jumpInput.value = '';
  }
};

const handleInputKeydown = (event) => {
  if (event.key === 'Enter') {
    handleJumpInput();
  }
};

watch(() => props.itemsPerPage, () => {
  jumpInput.value = '';
});
</script>

<template>
  <div v-if="showPagination" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8">
    <div class="flex flex-col sm:flex-row items-center gap-4">
      <div :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        {{ t('admin_showing') }} {{ startItem }} - {{ endItem }} {{ t('admin_of') }} {{ totalItems }} {{ t(`admin_${label}`) || label }}
      </div>
      
      <div class="flex items-center gap-2">
        <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_per_page') }}:</span>
        <div ref="dropdownRef" class="relative">
          <button
            @click="toggleDropdown"
            :class="[
              'flex items-center justify-between gap-2 min-w-[60px] px-3 py-1.5 text-sm border rounded-lg transition-all cursor-pointer',
              isDropdownOpen 
                ? 'border-construct-red ring-1 ring-construct-red' 
                : (isDarkMode ? 'border-gray-600 hover:border-gray-500' : 'border-gray-300 hover:border-gray-400'),
              isDarkMode ? 'bg-gray-700 text-white' : 'bg-white text-gray-900'
            ]"
          >
            <span>{{ itemsPerPage }}</span>
            <ChevronDown 
              :size="14" 
              :class="[
                'transition-transform duration-200',
                isDropdownOpen ? 'rotate-180' : '',
                isDarkMode ? 'text-gray-400' : 'text-gray-500'
              ]" 
            />
          </button>
          
          <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
          >
            <div
              v-if="isDropdownOpen"
              :class="[
                'absolute bottom-full left-0 mb-2 min-w-full py-1 rounded-lg shadow-lg border z-50 overflow-hidden',
                isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
              ]"
            >
              <button
                v-for="size in pageSizeOptions"
                :key="size"
                @click="selectPageSize(size)"
                :class="[
                  'w-full flex items-center justify-between px-3 py-2 text-sm transition-colors',
                  itemsPerPage === size
                    ? (isDarkMode ? 'bg-gray-700 text-construct-red' : 'bg-red-50 text-construct-red')
                    : (isDarkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-50')
                ]"
              >
                <span>{{ size }}</span>
                <Check v-if="itemsPerPage === size" :size="14" />
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </div>
    
    <div class="flex items-center gap-2">
      <button
        @click="goToFirst"
        :disabled="currentPage === 1"
        :class="[
          'flex items-center justify-center w-10 h-10 rounded-lg font-bold transition-colors',
          currentPage === 1
            ? (isDarkMode ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed')
            : (isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50')
        ]"
        :title="t('admin_first_page')"
      >
        <ChevronsLeft size="18" />
      </button>
      
      <button
        @click="prevPage"
        :disabled="currentPage === 1"
        :class="[
          'flex items-center justify-center w-10 h-10 rounded-lg font-bold transition-colors',
          currentPage === 1
            ? (isDarkMode ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed')
            : (isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50')
        ]"
        :title="t('admin_prev')"
      >
        <ChevronLeft size="18" />
      </button>
      
      <div class="flex items-center gap-1">
        <template v-for="(page, index) in visiblePages" :key="index">
          <span
            v-if="page === '...'"
            :class="['px-2 py-2 text-sm', isDarkMode ? 'text-gray-500' : 'text-gray-400']"
          >
            ...
          </span>
          <button
            v-else
            @click="goToPage(page)"
            :class="[
              'flex items-center justify-center min-w-[40px] h-10 rounded-lg font-bold transition-colors text-sm',
              page === currentPage
                ? 'bg-construct-red text-white'
                : (isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50')
            ]"
          >
            {{ page }}
          </button>
        </template>
      </div>
      
      <button
        @click="nextPage"
        :disabled="currentPage === totalPages"
        :class="[
          'flex items-center justify-center w-10 h-10 rounded-lg font-bold transition-colors',
          currentPage === totalPages
            ? (isDarkMode ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed')
            : (isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50')
        ]"
        :title="t('admin_next')"
      >
        <ChevronRight size="18" />
      </button>
      
      <button
        @click="goToLast"
        :disabled="currentPage === totalPages"
        :class="[
          'flex items-center justify-center w-10 h-10 rounded-lg font-bold transition-colors',
          currentPage === totalPages
            ? (isDarkMode ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed')
            : (isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50')
        ]"
        :title="t('admin_last_page')"
      >
        <ChevronsRight size="18" />
      </button>
      
      <div class="flex items-center gap-2 ml-2">
        <input
          v-model="jumpInput"
          @keydown="handleInputKeydown"
          type="text"
          inputmode="numeric"
          :class="[
            'w-16 px-2 py-1.5 text-sm text-center border rounded-lg focus:outline-none focus:border-construct-red',
            isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          :placeholder="t('admin_jump_to')"
        />
        <button
          @click="handleJumpInput"
          :class="[
            'px-3 py-1.5 text-sm rounded-lg font-bold transition-colors',
            isDarkMode 
              ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' 
              : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
          ]"
        >
          {{ t('admin_go') }}
        </button>
      </div>
    </div>
  </div>
</template>