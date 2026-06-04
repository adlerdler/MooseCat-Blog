/**
 * useTheme.js - 主题管理 Composable
 *
 * 向后兼容的包装层，实际逻辑委托给 useThemeStore（Pinia）。
 * 使用 writable computed（get/set）保持响应式，与 Pinia 3.x proxy 完全兼容。
 * 现有 69 处引用无需修改。
 */
import { computed } from 'vue';
import { useThemeStore } from '../stores/theme';

export function useTheme(options = {}) {
  const store = useThemeStore();

  // 首次调用时注入后端主题数据（幂等）
  if (options.themesData?.length > 0) {
    store.setThemesData(options.themesData);
  }

  return {
    // 响应式 state + getters（writable computed，通过 Pinia proxy 读写）
    isDarkMode: computed({ get: () => store.isDarkMode, set: (v) => { store.isDarkMode = v; } }),
    themes: computed({ get: () => store.themes, set: (v) => { store.themes = v; } }),
    currentTheme: computed({ get: () => store.currentTheme, set: (v) => { store.currentTheme = v; } }),
    themeClass: computed(() => store.themeClass),
    initialized: computed(() => store.initialized),
    // Actions（直接引用）
    toggleTheme: store.toggleTheme,
    getClass: store.getClass,
    initTheme: store.initTheme,
    setTheme: store.setTheme,
    initAccentTheme: store.initAccentTheme,
    setThemesData: store.setThemesData,
  };
}
