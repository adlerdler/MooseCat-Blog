/**
 * site_config.js - 网站基本信息配置
 * 
 * ⚠️ 字段变更备注（2026-05-24）：
 * - maintenance_mode → maintenance
 * - show_author_bio → author_bio
 * - show_comments → comments
 * - allow_registration → registration
 * - require_comment_approval → comment_approval
 * - enable_newsletter → newsletter
 * - enable_social_login → social_login
 * - enable_search → search
 * - enable_cache → cache
 * - enable_minification → minification
 * - lazy_load_images → lazy_load
 * - enable_cdn → cdn
 * - allowed_file_types → file_types
 * 
 * 功能说明：
 * - 存储网站基本配置信息
 * - 包含站点名称、描述、URL、图标、语言、时区等设置
 * - 包含性能优化和CDN配置
 * - 包含功能开关配置
 * 
 * 配置项说明：
 * - name: 网站名称
 * - description: 网站描述（用于SEO）
 * - siteUrl: 网站主URL
 * - copyright: 版权信息
 * - logo: Logo图片路径
 * - favicon: 网站图标路径
 * - timezone: 时区设置
 * - maintenance: 是否开启维护模式（原 maintenance_mode）
 * - author_bio: 是否显示作者简介（原 show_author_bio）
 * - comments: 是否显示评论功能（原 show_comments）
 * - cache: 是否启用页面缓存（原 enable_cache）
 * - cacheDuration: 缓存时长（秒）
 * - minification: 是否启用代码压缩（原 enable_minification）
 * - lazy_load: 是否启用图片懒加载（原 lazy_load_images）
 * - cdn: 是否启用CDN加速（原 enable_cdn）
 * - cdnUrl: CDN加速URL（如 https://cdn.example.com）
 * - registration: 是否允许用户注册（原 allow_registration）
 * - comment_approval: 评论是否需要审核（原 require_comment_approval）
 * - maxUploadSize: 文件上传限制（MB）
 * - file_types: 允许上传的文件类型（原 allowed_file_types）
 * - newsletter: 是否启用邮件订阅功能（原 enable_newsletter）
 * - social_login: 是否启用社交登录（原 enable_social_login）
 * - search: 是否启用搜索功能（原 enable_search）
 */

export const siteConfig = {
  name: 'ARCHYX',
  description: 'Constructivist Digital Archive',
  siteUrl: 'https://archyx.com',
  copyright: '© 2024 ARCHYX. All rights reserved.',
  logo: '',
  favicon: '',
  timezone: 'UTC+8',
  maintenance: false,
  author_bio: false,
  comments: true,
  cache: true,
  cacheDuration: 3600,
  minification: true,
  lazy_load: true,
  cdn: false,
  cdnUrl: '',
  
  // 功能开关
  registration: true,
  comment_approval: false,
  newsletter: true,
  social_login: false,
  search: true,
  
  // 文件上传限制
  maxUploadSize: 10,
  file_types: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx']
};
