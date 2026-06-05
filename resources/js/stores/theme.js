/**
 * useThemeStore - 主题 Pinia Store
 *
 * 替代 useTheme composable 的模块级全局状态，
 * 提供 DevTools 可视化和更清晰的 API。
 */
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

const safeGetItem = (key) => {
  try { return localStorage.getItem(key); } catch { return null; }
};

const safeSetItem = (key, value) => {
  try { localStorage.setItem(key, value); } catch { /* noop */ }
};

const getActiveThemes = (themesData) =>
  (themesData || []).filter(t => t.is_active).sort((a, b) => a.sort_order - b.sort_order);

const getDefaultTheme = (themesData) =>
  (themesData || []).find(t => t.is_default) || (themesData || [])[0];

export const useThemeStore = defineStore('theme', () => {
  // ── State ──
  const isDarkMode = ref(true);
  const themes = ref([]);
  const currentTheme = ref(null);
  const initialized = ref(false);

  // ── Getters ──
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

  // ── Actions ──
  function updateAccentColor() {
    if (currentTheme.value) {
      document.documentElement.style.setProperty('--accent', currentTheme.value.color);
    }
  }

  function setThemesData(themesData) {
    if (!themesData || themesData.length === 0) return;
    themes.value = getActiveThemes(themesData);

    // 尝试从 localStorage 恢复或设置默认
    const savedName = safeGetItem('accent_theme');
    if (savedName) {
      const found = themes.value.find(t => t.name === savedName);
      currentTheme.value = found || getDefaultTheme(themes.value);
    } else {
      currentTheme.value = getDefaultTheme(themes.value);
    }
    updateAccentColor();
  }

  function initAccentTheme() {
    const savedName = safeGetItem('accent_theme');
    const savedColor = safeGetItem('accent_color');
    if (savedName) {
      const found = themes.value.find(t => t.name === savedName);
      if (found) {
        currentTheme.value = found;
      } else if (savedColor) {
        currentTheme.value = { name: savedName, color: savedColor };
      }
    }
    updateAccentColor();
  }

  function initTheme() {
    const savedTheme = safeGetItem('admin_theme');
    isDarkMode.value = savedTheme !== 'light';

    document.documentElement.classList.toggle('dark', isDarkMode.value);

    initAccentTheme();
    initialized.value = true;
  }

  function toggleTheme() {
    isDarkMode.value = !isDarkMode.value;

    if (isDarkMode.value) {
      safeSetItem('admin_theme', 'dark');
    } else {
      safeSetItem('admin_theme', 'light');
    }

    document.documentElement.classList.toggle('dark', isDarkMode.value);
  }

  function setTheme(theme) {
    currentTheme.value = theme;
    safeSetItem('accent_theme', theme.name);
    safeSetItem('accent_color', theme.color);
    updateAccentColor();
  }

  function getClass(darkClass, lightClass) {
    return isDarkMode.value ? darkClass : lightClass;
  }

  return {
    // state
    isDarkMode,
    themes,
    currentTheme,
    initialized,
    // getters
    themeClass,
    // actions
    setThemesData,
    initTheme,
    initAccentTheme,
    toggleTheme,
    setTheme,
    getClass,
  };
});
