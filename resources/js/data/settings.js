/**
 * settings.js - 系统设置配置数据
 * 
 * 功能说明：
 * - 存储系统各项设置的默认配置
 * - 包含站点信息、外观、通知、SEO、性能、邮件等设置
 * - 提供设置页签配置
 */

export const defaultSettings = {
  site: {
    name: 'ARCHYX',
    description: 'Constructivist Digital Archive',
    logo: '',
    favicon: '',
    defaultLanguage: 'en',
    timezone: 'UTC+8',
    maintenanceMode: false
  },
  appearance: {
    theme: 'dark',
    accentColor: '#E53E3E',
    font: 'system',
    showAuthorBio: true,
    showComments: true,
    showRelatedPosts: true
  },
  notifications: {
    emailNotifications: true,
    commentApproval: true,
    newUserAlert: true,
    weeklyReport: false,
    digestFrequency: 'weekly'
  },
  seo: {
    metaTitle: 'ARCHYX - Constructivist Digital Archive',
    metaDescription: 'Exploring digital constructivism through articles, videos, and projects',
    googleAnalytics: '',
    enableSitemap: true,
    enableRobots: true,
    canonicalUrl: 'https://archyx.com'
  },
  performance: {
    enableCache: true,
    cacheDuration: 3600,
    enableMinification: true,
    lazyLoadImages: true,
    enableCDN: false
  },
  mail: {
    host: 'smtp.archyx.com',
    port: 587,
    username: 'admin@archyx.com',
    password: '****************',
    encryption: 'tls',
    fromAddress: 'no-reply@archyx.com',
    fromName: 'Archyx System'
  }
};

export const tabsConfig = [
  { id: 'site', label_key: 'admin_settings_site', icon_key: 'globe' },
  { id: 'appearance', label_key: 'admin_settings_appearance', icon_key: 'palette' },
  { id: 'notifications', label_key: 'admin_settings_notifications', icon_key: 'bell' },
  { id: 'seo', label_key: 'admin_settings_seo', icon_key: 'search' },
  { id: 'mail', label_key: 'admin_settings_mail', icon_key: 'mail' },
  { id: 'performance', label_key: 'admin_settings_performance', icon_key: 'zap' }
];
