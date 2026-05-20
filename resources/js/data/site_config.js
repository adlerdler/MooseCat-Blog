/**
 * site_config.js - 网站基本信息配置
 * 
 * 功能说明：
 * - 存储网站基本配置信息
 * - 包含站点名称、描述、URL、图标、语言、时区等设置
 * - 提供获取网站配置的辅助函数
 */

export const siteConfig = {
  name: 'ARCHYX',
  description: 'Constructivist Digital Archive',
  siteUrl: 'https://archyx.com',
  copyright: '© 2024 ARCHYX. All rights reserved.',
  logo: '',
  favicon: '',
  defaultLanguage: 'en',
  timezone: 'UTC+8',
  maintenanceMode: false,
  showAuthorBio: false,
  showComments: true
};

/**
 * 获取网站基本配置
 * @returns {Object} 网站配置对象
 */
export function getSiteConfig() {
  return { ...siteConfig };
}

/**
 * 获取网站名称
 * @returns {string} 网站名称
 */
export function getSiteName() {
  return siteConfig.name;
}

/**
 * 获取网站地址
 * @returns {string} 网站URL
 */
export function getSiteUrl() {
  return siteConfig.siteUrl;
}

/**
 * 获取网站描述
 * @returns {string} 网站描述
 */
export function getSiteDescription() {
  return siteConfig.description;
}

/**
 * 获取版权信息
 * @returns {string} 版权信息
 */
export function getSiteCopyright() {
  return siteConfig.copyright;
}

/**
 * 检查是否处于维护模式
 * @returns {boolean} 维护模式状态
 */
export function isMaintenanceMode() {
  return siteConfig.maintenanceMode;
}

/**
 * 检查是否显示作者简介
 * @returns {boolean} 作者简介显示状态
 */
export function isAuthorBioVisible() {
  return siteConfig.showAuthorBio;
}

/**
 * 检查是否显示评论
 * @returns {boolean} 评论显示状态
 */
export function isCommentsVisible() {
  return siteConfig.showComments;
}
