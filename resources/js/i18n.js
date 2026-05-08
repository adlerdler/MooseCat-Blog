import { createI18n } from 'vue-i18n'

const messages = {
  en: {
    nav_title: 'ARCHYX',
    nav_home: 'HOME',
    nav_blog: 'BLOG',
    nav_videos: 'VIDEOS',
    nav_projects: 'PROJECTS',
    nav_resources: 'RESOURCES',
    nav_about: 'AUTHOR',
    hero_subtitle: 'PERSONAL BLOG',
    interface_lang: 'INTERFACE LANGUAGE',
    accent_theme: 'ACCENT THEME',
    subscribe_title: 'ESTABLISH CONNECTION //',
    subscribe_desc: 'Subscribe to the transmission for the latest research and thoughts.',
    subscribe_input: 'ENTER EMAIL ADDRESS',
    subscribe_btn: 'TRANSMIT',
    footer_desc: 'Exploring the intersection of architecture and technology.',
    footer_sections: 'SECTIONS',
    footer_data: 'DATA',
    copyright: '© 2026 BLDG_SYSTEM',
    version: 'Archyx v0.1',
    settings: 'SETTINGS',
    data_corruption: 'DATA CORRUPTION',
    not_found_subtitle: 'REQUESTED NODE NOT FOUND',
    not_found_desc: 'The link you followed may be broken, or the page may have been moved to another sector of the archive.',
    access_denied: 'ACCESS DENIED',
    forbidden_subtitle: 'AUTHORIZATION REQUIRED',
    forbidden_desc: 'You do not have the necessary permissions to access this sector. Please verify your credentials.',
    system_error: 'SYSTEM ERROR',
    server_error_subtitle: 'INTERNAL SYSTEM FAILURE',
    server_error_desc: 'An unexpected error has occurred. The system is working to resolve the issue. Please try again later.',
    return_home: 'RETURN TO HOME',
  },
  zh: {
    nav_title: 'ARCHYX',
    nav_home: '首页',
    nav_blog: '博客',
    nav_videos: '视频',
    nav_projects: '项目',
    nav_resources: '资源',
    nav_about: '作者',
    hero_subtitle: '个人博客',
    interface_lang: '界面语言',
    accent_theme: '主题颜色',
    subscribe_title: '建立连接 //',
    subscribe_desc: '订阅以获取最新的开发日志与技术洞察。',
    subscribe_input: '输入邮箱地址',
    subscribe_btn: '发送',
    footer_desc: '探索建筑与技术的边界，构建未来的数字空间体验',
    footer_sections: '板块',
    footer_data: '数据',
    copyright: '© 2026 BLDG_SYSTEM',
    version: 'Archyx v0.1',
    settings: '设置',
    data_corruption: '数据损坏',
    not_found_subtitle: '请求的节点不存在',
    not_found_desc: '您访问的链接可能已断开，或者该页面已被移动到档案库的其他区域。',
    access_denied: '访问拒绝',
    forbidden_subtitle: '需要授权',
    forbidden_desc: '您没有访问此区域的必要权限，请验证您的凭证。',
    system_error: '系统错误',
    server_error_subtitle: '内部系统故障',
    server_error_desc: '发生了意外错误，系统正在努力解决问题，请稍后重试。',
    return_home: '返回首页',
  },
  'zh-TW': {
    nav_title: 'ARCHYX',
    nav_home: '首頁',
    nav_blog: '部落格',
    nav_videos: '影片',
    nav_projects: '專案',
    nav_resources: '資源',
    nav_about: '作者',
    hero_subtitle: '個人部落格',
    interface_lang: '介面語言',
    accent_theme: '主題顏色',
    subscribe_title: '建立連線 //',
    subscribe_desc: '訂閱以獲取最新的開發日誌與技術洞察。',
    subscribe_input: '輸入信箱地址',
    subscribe_btn: '發送',
    footer_desc: '探索建築與技術的邊界，構建未來的數位空間體驗',
    footer_sections: '板塊',
    footer_data: '數據',
    copyright: '© 2026 BLDG_SYSTEM',
    version: 'Archyx v0.1',
    settings: '設置',
    data_corruption: '數據損壞',
    not_found_subtitle: '請求的節點不存在',
    not_found_desc: '您訪問的鏈接可能已斷開，或者該頁面已被移動到檔案庫的其他區域。',
    access_denied: '訪問拒絕',
    forbidden_subtitle: '需要授權',
    forbidden_desc: '您沒有訪問此區域的必要權限，請驗證您的憑證。',
    system_error: '系統錯誤',
    server_error_subtitle: '內部系統故障',
    server_error_desc: '發生了意外錯誤，系統正在努力解決問題，請稍後重試。',
    return_home: '返回首頁',
  },
}

function getLocale() {
  const saved = localStorage.getItem('lang')
  if (saved && messages[saved]) {
    return saved
  }
  const browserLang = navigator.language.toLowerCase()
  if (browserLang.startsWith('zh-tw') || browserLang.startsWith('zh-hk')) {
    return 'zh-TW'
  } else if (browserLang.startsWith('zh')) {
    return 'zh'
  }
  return 'en'
}

export const i18n = createI18n({
  legacy: false,
  locale: getLocale(),
  fallbackLocale: 'en',
  messages,
})

export function setLocale(locale) {
  if (messages[locale]) {
    i18n.global.locale.value = locale
    localStorage.setItem('lang', locale)
  }
}
