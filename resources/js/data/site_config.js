/**
 * site_config.js - 网站基本信息配置
 * 
 * 功能说明：
 * - 存储网站基本配置信息
 * - 包含站点名称、描述、URL、图标、语言、时区等设置
 * - 包含性能优化和CDN配置
 * 
 * 配置项说明：
 * - name: 网站名称
 * - description: 网站描述（用于SEO）
 * - siteUrl: 网站主URL
 * - copyright: 版权信息
 * - logo: Logo图片路径
 * - favicon: 网站图标路径
 * - timezone: 时区设置
 * - maintenanceMode: 是否开启维护模式（开启后前台显示维护页面）
 * - showAuthorBio: 是否显示作者简介
 * - showComments: 是否显示评论功能
 * - enableCache: 是否启用页面缓存
 * - cacheDuration: 缓存时长（秒）
 * - enableMinification: 是否启用代码压缩（CSS/JS/HTML）
 * - lazyLoadImages: 是否启用图片懒加载
 * - enableCDN: 是否启用CDN加速
 * - cdnUrl: CDN加速URL（如 https://cdn.example.com）
 */

export const siteConfig = {
  name: 'ARCHYX',
  description: 'Constructivist Digital Archive',
  siteUrl: 'https://archyx.com',
  copyright: '© 2024 ARCHYX. All rights reserved.',
  logo: '',
  favicon: '',
  timezone: 'UTC+8',
  maintenanceMode: false,
  showAuthorBio: false,
  showComments: true,
  enableCache: true,
  cacheDuration: 3600,
  enableMinification: true,
  lazyLoadImages: true,
  enableCDN: false,
  cdnUrl: ''
};
