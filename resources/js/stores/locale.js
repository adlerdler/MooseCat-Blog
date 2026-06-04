/**
 * useLocaleStore - 语言偏好 Store
 *
 * 封装 vue-i18n 的语言切换逻辑，持久化到 localStorage，
 * 提供 DevTools 可视化的当前语言状态。
 */
import { defineStore } from 'pinia';
import { ref } from 'vue';
import { i18n, setLocale as i18nSetLocale, loadLocaleMessages } from '../i18n';

export const useLocaleStore = defineStore('locale', () => {
  // ── State ──
  const currentLocale = ref(i18n.global.locale.value);

  // ── Actions ──
  async function setLocale(code) {
    await i18nSetLocale(code);
    currentLocale.value = i18n.global.locale.value;
  }

  async function switchTo(code) {
    // 如果消息未加载，先加载
    if (!i18n.global.availableLocales.includes(code)) {
      await loadLocaleMessages(code);
    }
    i18n.global.locale.value = code;
    currentLocale.value = code;
    try {
      localStorage.setItem('locale', code);
    } catch { /* ignore */ }
  }

  // 同步 i18n locale 变化到 store
  function sync() {
    currentLocale.value = i18n.global.locale.value;
  }

  return {
    currentLocale,
    setLocale,
    switchTo,
    sync,
  };
});
