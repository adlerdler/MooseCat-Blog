/**
 * useUIStore - 全局 UI 状态管理
 *
 * 管理侧边栏、搜索面板、设置面板、Footer 等全局 UI 开关，
 * 替代组件内的局部 ref，实现跨组件零耦合通信。
 */
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUIStore = defineStore('ui', () => {
  // ── State ──
  const isMenuOpen = ref(false);
  const isSearchOpen = ref(false);
  const isSettingsOpen = ref(false);
  const footerVisible = ref(true);

  // ── Actions ──
  function toggleMenu() {
    isMenuOpen.value = !isMenuOpen.value;
    if (isMenuOpen.value) {
      isSettingsOpen.value = false;
      isSearchOpen.value = false;
    }
  }

  function closeMenu() {
    isMenuOpen.value = false;
  }

  function toggleSearch() {
    isSearchOpen.value = !isSearchOpen.value;
  }

  function closeSearch() {
    isSearchOpen.value = false;
  }

  function toggleSettings() {
    isSettingsOpen.value = !isSettingsOpen.value;
  }

  function toggleFooter() {
    footerVisible.value = !footerVisible.value;
  }

  return {
    isMenuOpen,
    isSearchOpen,
    isSettingsOpen,
    footerVisible,
    toggleMenu,
    closeMenu,
    toggleSearch,
    closeSearch,
    toggleSettings,
    toggleFooter,
  };
});
