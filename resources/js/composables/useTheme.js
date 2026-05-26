/**
 * useTheme.js - 主题管理 Composable
 *
 * 功能说明：
 * - 统一管理应用的深色/浅色主题状态（仅后台使用）
 * - 管理 Accent Color 主题（前台后台共享）
 * - 提供响应式的主题状态
 * - 支持 localStorage 持久化
 * - 提供便捷的主题相关工具函数
 *
 * 使用方式：
 * // 通过 options 传入数据（必须）
 * import { useTheme } from '../../composables/useTheme';
 * const { isDarkMode, themes } = useTheme({ themesData: props.themes });
 */
import { ref, computed, watch } from 'vue';

let globalThemesData = null;

const getActiveThemes = (themesData) => themesData.filter(t => t.is_active).sort((a, b) => a.sort_order - b.sort_order);
const getDefaultTheme = (themesData) => themesData.find(t => t.is_default) || themesData[0];

const isDarkMode = ref(true);
const themes = ref([]);
const currentTheme = ref(null);

export function useTheme(options = {}) {
  const themesData = options.themesData || [];

  if (!globalThemesData && themesData.length > 0) {
    globalThemesData = themesData;
    themes.value = getActiveThemes(globalThemesData);
    currentTheme.value = getDefaultTheme(globalThemesData);
  } else if (options.themesData && themesData.length > 0) {
    globalThemesData = themesData;
    themes.value = getActiveThemes(globalThemesData);
    const newDefault = getDefaultTheme(globalThemesData);
    if (newDefault) {
      currentTheme.value = newDefault;
    }
  }

  const updateAccentColor = () => {
    if (currentTheme.value) {
      document.documentElement.style.setProperty('--accent', currentTheme.value.color);
    }
  };

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

  const initTheme = () => {
    const savedTheme = localStorage.getItem('admin_theme');
    isDarkMode.value = savedTheme !== 'light';

    if (isDarkMode.value) {
      document.documentElement.classList.remove('light');
    } else {
      document.documentElement.classList.add('light');
    }

    initAccentTheme();
  };

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

  const setTheme = (theme) => {
    currentTheme.value = theme;
    localStorage.setItem('accent_theme', theme.name);
    updateAccentColor();
  };

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

  const getClass = (darkClass, lightClass) => {
    return isDarkMode.value ? darkClass : lightClass;
  };

  return {
    isDarkMode,
    toggleTheme,
    themeClass,
    getClass,
    initTheme,
    themes,
    currentTheme,
    setTheme,
    initAccentTheme,
  };
}