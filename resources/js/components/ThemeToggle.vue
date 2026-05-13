<script setup>
/**
 * ThemeToggle.vue - 主题切换组件
 * 
 * 功能说明：
 * - 提供明暗主题切换功能
 * - 自动检测并应用之前保存的主题偏好
 * - 状态持久化到 localStorage
 * - 支持国际化提示
 */
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Sun, Moon } from 'lucide-vue-next';
import { useTheme } from '../composables/useTheme';

const { t } = useI18n();
const { isDarkMode, toggleTheme } = useTheme();

const tooltipText = computed(() => {
  return isDarkMode.value ? t('admin_light_mode') : t('admin_dark_mode');
});
</script>

<template>
  <button
    @click="toggleTheme"
    :class="[
      'p-2 transition-colors rounded-lg',
      isDarkMode 
        ? 'text-gray-400 hover:text-white hover:bg-gray-700' 
        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'
    ]"
    :title="tooltipText"
  >
    <Sun v-if="isDarkMode" size="20" />
    <Moon v-else size="20" />
  </button>
</template>
