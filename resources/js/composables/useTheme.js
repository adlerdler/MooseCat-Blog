/**
 * useTheme.js - 主题管理 Composable
 * 
 * 功能说明：
 * - 统一管理应用的深色/浅色主题状态（仅后台使用）
 * - 管理 Accent Color 主题（前台后台共享）
 * - 提供响应式的主题状态
 * - 支持 localStorage 持久化
 * - 提供便捷的主题相关工具函数
 */
import { ref, computed, watch } from 'vue';
import { themes as themesData } from '../data/themes';

const getActiveThemes = () => themesData.filter(t => t.is_active).sort((a, b) => a.sort_order - b.sort_order);
const getDefaultTheme = () => themesData.find(t => t.is_default) || themesData[0];

// 深色/浅色模式（仅后台使用）
const isDarkMode = ref(true);

// Accent Color 主题列表（前台后台共享，从 themes.js 读取）
const themes = ref(getActiveThemes());

// 当前 Accent Color 主题（前台后台共享）
const defaultTheme = getDefaultTheme();
const currentTheme = ref(defaultTheme);

export function useTheme() {
  // 更新 Accent Color CSS 变量
  const updateAccentColor = () => {
    document.documentElement.style.setProperty('--accent', currentTheme.value.color);
  };

  // 初始化 Accent Color（前台使用）
  const initAccentTheme = () => {
    const savedAccentTheme = localStorage.getItem('accent_theme');
    if (savedAccentTheme) {
      const found = themes.value.find(t => t.name === savedAccentTheme);
      if (found) {
        currentTheme.value = found;
      }
    }
    updateAccentColor();
  };

  // 初始化完整主题（后台使用，包含深色/浅色模式）
  const initTheme = () => {
    // 初始化深色/浅色模式
    const savedTheme = localStorage.getItem('admin_theme');
    isDarkMode.value = savedTheme !== 'light';
    
    // 同步 DOM class（仅后台使用）
    if (isDarkMode.value) {
      document.documentElement.classList.remove('light');
    } else {
      document.documentElement.classList.add('light');
    }

    // 初始化 Accent Color 主题
    initAccentTheme();
  };

  // 切换深色/浅色模式（仅后台使用）
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

  // 设置 Accent Color 主题（前台后台共享）
  const setTheme = (theme) => {
    currentTheme.value = theme;
    localStorage.setItem('accent_theme', theme.name);
    updateAccentColor();
  };

  // 获取主题类名（仅后台使用）
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

  // 根据主题返回类名（仅后台使用）
  const getClass = (darkClass, lightClass) => {
    return isDarkMode.value ? darkClass : lightClass;
  };

  return {
    // 后台专用
    isDarkMode,
    toggleTheme,
    themeClass,
    getClass,
    initTheme,
    // 前台后台共享
    themes,
    currentTheme,
    setTheme,
    initAccentTheme,
  };
}