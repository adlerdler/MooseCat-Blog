/**
 * useTheme.js - 主题管理 Composable
 * 
 * 功能说明：
 * - 统一管理应用的深色/浅色主题状态
 * - 提供响应式的主题状态
 * - 支持 localStorage 持久化
 * - 提供便捷的主题相关工具函数
 */
import { ref, computed, watch } from 'vue';

const isDarkMode = ref(true);

export function useTheme() {
  // 初始化主题状态
  const initTheme = () => {
    const savedTheme = localStorage.getItem('admin_theme');
    isDarkMode.value = savedTheme !== 'light';
    
    // 同步 DOM class
    if (isDarkMode.value) {
      document.documentElement.classList.remove('light');
    } else {
      document.documentElement.classList.add('light');
    }
  };

  // 切换主题
  const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    
    if (isDarkMode.value) {
      localStorage.setItem('admin_theme', 'dark');
      document.documentElement.classList.remove('light');
    } else {
      localStorage.setItem('admin_theme', 'light');
      document.documentElement.classList.add('light');
    }
  };

  // 获取主题类名
  const themeClass = computed(() => ({
    'bg-gray-900': isDarkMode.value,
    'bg-gray-800': isDarkMode.value,
    'bg-gray-700': isDarkMode.value,
    'bg-gray-600': isDarkMode.value,
    'bg-white': !isDarkMode.value,
    'bg-gray-50': !isDarkMode.value,
    'bg-gray-100': !isDarkMode.value,
    'bg-gray-200': !isDarkMode.value,
    'text-white': isDarkMode.value,
    'text-gray-900': !isDarkMode.value,
    'text-gray-400': isDarkMode.value,
    'text-gray-500': !isDarkMode.value,
    'border-gray-700': isDarkMode.value,
    'border-gray-600': isDarkMode.value,
    'border-gray-200': !isDarkMode.value,
    'border-gray-300': !isDarkMode.value,
  }));

  // 根据主题返回类名
  const getClass = (darkClass, lightClass) => {
    return isDarkMode.value ? darkClass : lightClass;
  };

  // 初始化
  initTheme();

  return {
    isDarkMode,
    toggleTheme,
    themeClass,
    getClass,
    initTheme
  };
}