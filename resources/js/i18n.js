import { createI18n } from 'vue-i18n'

// 静态导入默认语言文件（构建时可用）
import en from '/public/locales/en.json'
import zh from '/public/locales/zh-CN.json'
import zhTW from '/public/locales/zh-TW.json'

const messages = {
  en,
  zh,
  'zh-TW': zhTW
}

const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages
})

// ─── 动态语言加载 ─────────────────────────────────────────────
let supportedLocales = ['en', 'zh', 'zh-TW']

/**
 * 从后端 languages 数组中提取代码列表并合并到支持的语言中
 */
export function syncLocalesFromBackend(languages = []) {
  if (!Array.isArray(languages) || languages.length === 0) return

  const codes = languages
    .filter(l => l.is_active)
    .map(l => l.code)

  // 合并去重
  supportedLocales = [...new Set([...supportedLocales, ...codes])]
}

/**
 * 动态加载指定语言的翻译文件
 */
export async function loadLocaleMessages(code) {
  // 已加载则跳过
  if (i18n.global.availableLocales.includes(code)) return true

  // 尝试多种文件名变体
  const variants = [code]
  if (code !== code.toLowerCase()) variants.push(code.toLowerCase())

  for (const v of variants) {
    try {
      const response = await fetch(`/locales/${v}.json`)
      if (response.ok) {
        const data = await response.json()
        i18n.global.setLocaleMessage(code, data)
        return true
      }
    } catch (e) {
      // 尝试下一个变体
    }
  }

  console.warn(`Failed to load locale messages for "${code}" (tried: ${variants.map(v => v + '.json').join(', ')})`)
  return false
}

/**
 * 设置当前语言（支持动态加载未知语言）
 */
export async function setLocale(locale) {
  // 同步后端数据
  if (!supportedLocales.includes(locale)) {
    // 检查是否可以从 window.__languages 加载
    trySyncFromPage()
  }

  if (!supportedLocales.includes(locale)) {
    console.warn(`Locale "${locale}" is not supported`)
    return
  }

  // 如果消息未加载，先加载
  if (!i18n.global.availableLocales.includes(locale)) {
    const loaded = await loadLocaleMessages(locale)
    if (!loaded) {
      console.warn(`Failed to load messages for "${locale}", falling back to en`)
      i18n.global.locale.value = 'en'
      return
    }
  }

  i18n.global.locale.value = locale
  try {
    localStorage.setItem('locale', locale)
  } catch (e) {
    // localStorage unavailable
  }
}

/**
 * 尝试从 Inertia page props 同步支持的语言
 */
function trySyncFromPage() {
  try {
    // 通过 DOM 获取 Inertia props（页面初始化时可用）
    const pageEl = document.getElementById('app')
    if (pageEl) {
      const dataPage = pageEl.getAttribute('data-page')
      if (dataPage) {
        const parsed = JSON.parse(dataPage)
        if (parsed.props?.languages) {
          syncLocalesFromBackend(parsed.props.languages)
        }
      }
    }
  } catch (e) {
    // 静默失败
  }
}

/**
 * 获取当前支持的语言代码列表
 */
export function getSupportedLocales() {
  trySyncFromPage()
  return [...supportedLocales]
}

// ─── 初始化：检测浏览器语言 ───────────────────────────────────
function getLocale() {
  try {
    const storedLocale = localStorage.getItem('locale')
    if (storedLocale && supportedLocales.includes(storedLocale)) {
      return storedLocale
    }
  } catch (e) {
    // localStorage unavailable
  }

  const browserLocale = navigator.language || navigator.userLanguage

  if (browserLocale && supportedLocales.includes(browserLocale)) {
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

    if (supportedLocales.includes(baseLocale)) {
      return baseLocale
    }
  }

  return 'en'
}

// 设置初始语言
const initialLocale = getLocale()
if (initialLocale !== 'en') {
  i18n.global.locale.value = initialLocale
}

/**
 * 同步后端语言后重新检测浏览器语言（用于新添加语言自动匹配）
 * 仅在用户未手动选择语言时生效（无 localStorage 记录）
 */
export async function reapplyBrowserLocale() {
  try {
    const stored = localStorage.getItem('locale')
    if (stored && supportedLocales.includes(stored)) return // 用户手动选过，不覆盖
  } catch (e) { /* ignore */ }

  const browserLocale = navigator.language || navigator.userLanguage
  let detected = null

  if (browserLocale && supportedLocales.includes(browserLocale)) {
    detected = browserLocale
  } else if (browserLocale) {
    const base = browserLocale.split('-')[0]
    if (base === 'zh') {
      detected = (browserLocale === 'zh-TW' || browserLocale === 'zh-HK') ? 'zh-TW' : 'zh'
    } else if (supportedLocales.includes(base)) {
      detected = base
    }
  }

  if (detected && detected !== i18n.global.locale.value) {
    // 尝试加载翻译文件
    if (!i18n.global.availableLocales.includes(detected)) {
      await loadLocaleMessages(detected)
    }
    i18n.global.locale.value = detected
    try { localStorage.setItem('locale', detected) } catch (e) { /* ignore */ }
  }
}

export { i18n }
export default i18n