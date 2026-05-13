import { createI18n } from 'vue-i18n'

import en from '/public/locales/en.json'
import zh from '/public/locales/zh.json'
import zhTW from '/public/locales/zh-TW.json'

const messages = {
  en,
  zh,
  'zh-TW': zhTW
}

const SUPPORTED_LOCALES = ['en', 'zh', 'zh-TW']

function getLocale() {
  const storedLocale = localStorage.getItem('locale')
  if (storedLocale && SUPPORTED_LOCALES.includes(storedLocale)) {
    return storedLocale
  }

  const browserLocale = navigator.language || navigator.userLanguage

  if (browserLocale && SUPPORTED_LOCALES.includes(browserLocale)) {
    return browserLocale
  }

  if (browserLocale) {
    const baseLocale = browserLocale.split('-')[0]
    if (baseLocale === 'zh') {
      if (browserLocale === 'zh-TW' || browserLocale === 'zh-HK' || browserLocale === 'zh-MO') {
        return 'zh-TW'
      }
      return 'zh'
    }

    if (SUPPORTED_LOCALES.includes(baseLocale)) {
      return baseLocale
    }
  }

  return 'en'
}

const i18n = createI18n({
  legacy: false,
  locale: getLocale(),
  fallbackLocale: 'en',
  messages
})

export function setLocale(locale) {
  if (!SUPPORTED_LOCALES.includes(locale)) {
    console.warn(`Locale ${locale} is not supported`)
    return
  }
  i18n.global.locale.value = locale
  localStorage.setItem('locale', locale)
}

export function getSupportedLocales() {
  return [...SUPPORTED_LOCALES]
}

export { i18n }
export default i18n