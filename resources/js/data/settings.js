/**
 * settings.js - 系统设置配置数据
 * 
 * 功能说明：
 * - 存储系统各项设置的默认配置
 * - 包含外观、通知、性能等设置
 * - 提供设置页签配置
 */

export const defaultSettings = {
  notifications: {
    emailNotifications: true,
    commentApproval: true,
    newUserAlert: true,
    weeklyReport: false,
    digestFrequency: 'weekly'
  },
  performance: {
    enableCache: true,
    cacheDuration: 3600,
    enableMinification: true,
    lazyLoadImages: true,
    enableCDN: false
  }
};

export const tabsConfig = [
  { id: 'site', label_key: 'admin_settings_site', icon_key: 'globe' },
  { id: 'appearance', label_key: 'admin_settings_appearance', icon_key: 'palette' },
  { id: 'notifications', label_key: 'admin_settings_notifications', icon_key: 'bell' },
  { id: 'seo', label_key: 'admin_settings_seo', icon_key: 'search' },
  { id: 'performance', label_key: 'admin_settings_performance', icon_key: 'zap' }
];
