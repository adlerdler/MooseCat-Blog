<script setup>
/**
 * EmptyState.vue - 后台通用空状态组件
 * 
 * 功能说明：
 * - 用于在列表、表格或搜索结果为空时显示提示
 * - 遵循 Extreme Minimalism 和 Glassmorphism 风格
 * - 采用虚线边框设计
 */
import { useTheme } from '../../composables/useTheme';
import { useI18n } from 'vue-i18n';
import { Inbox } from 'lucide-vue-next';

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  icon: {
    type: [Object, Function],
    default: Inbox
  }
});

const { isDarkMode } = useTheme();
const { t } = useI18n();
</script>

<template>
  <div :class="[
    'w-full flex flex-col items-center justify-center p-16 rounded-3xl border-2 border-dashed transition-all duration-500',
    isDarkMode 
      ? 'bg-gray-900/20 border-gray-800 text-gray-500' 
      : 'bg-gray-50/30 border-gray-200 text-gray-400 shadow-inner'
  ]">
    <div :class="[
      'mb-6 p-6 rounded-full border-2 border-dashed',
      isDarkMode ? 'border-gray-800 bg-gray-900/40' : 'border-gray-100 bg-white/50'
    ]">
      <component :is="icon" :size="48" class="opacity-40" />
    </div>
    
    <h3 :class="['text-xl font-display font-bold tracking-tight mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
      {{ title || t('admin_no_data') || 'No Data Found' }}
    </h3>
    
    <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50 text-center max-w-xs', isDarkMode ? 'text-gray-500' : 'text-gray-500']">
      {{ description || t('admin_no_data_description') || 'Try adjusting your filters or adding new content' }}
    </p>
  </div>
</template>
