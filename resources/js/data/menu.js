/**
 * menu.js - 菜单配置数据（数据库形式模拟）
 * 
 * 功能说明：
 * - 模拟数据库菜单表结构
 * - 统一管理前台和后台菜单
 * - 支持多级菜单（通过parent_id关联）
 * 
 * 字段说明：
 * - id: 菜单唯一标识
 * - type: 菜单类型（front/admin）
 * - parent_id: 父级菜单ID，null表示一级菜单
 * - label_key: 国际化翻译key
 * - icon_name: 图标名称（仅后台菜单）
 * - path: 路由路径
 * - sort_order: 排序顺序
 * - is_active: 是否启用
 */

export const menus = [
  // 前台菜单
  { id: 1, type: 'front', parent_id: null, label_key: 'nav_home', icon_name: null, path: '/', sort_order: 1, is_active: true },
  { id: 2, type: 'front', parent_id: null, label_key: 'nav_blog', icon_name: null, path: '/blog', sort_order: 2, is_active: true },
  { id: 3, type: 'front', parent_id: null, label_key: 'nav_videos', icon_name: null, path: '/videos', sort_order: 3, is_active: true },
  { id: 4, type: 'front', parent_id: null, label_key: 'nav_projects', icon_name: null, path: '/projects', sort_order: 4, is_active: true },
  { id: 5, type: 'front', parent_id: null, label_key: 'nav_resources', icon_name: null, path: '/resources', sort_order: 5, is_active: true },
  { id: 6, type: 'front', parent_id: null, label_key: 'nav_author', icon_name: null, path: '/author', sort_order: 6, is_active: true },
  
  // 后台菜单 - 一级
  { id: 10, type: 'admin', parent_id: null, label_key: 'admin_dashboard', icon_name: 'LayoutDashboard', path: '/admin/index', sort_order: 1, is_active: true },
  { id: 11, type: 'admin', parent_id: null, label_key: 'admin_content', icon_name: 'FileText', path: null, sort_order: 2, is_active: true },
  { id: 12, type: 'admin', parent_id: null, label_key: 'admin_categories', icon_name: 'Folder', path: '/admin/categories', sort_order: 3, is_active: true },
  { id: 13, type: 'admin', parent_id: null, label_key: 'admin_tags', icon_name: 'Tag', path: '/admin/tags', sort_order: 4, is_active: true },
  { id: 14, type: 'admin', parent_id: null, label_key: 'admin_comments', icon_name: 'MessageSquare', path: '/admin/comments', sort_order: 5, is_active: true },
  { id: 15, type: 'admin', parent_id: null, label_key: 'admin_advertisements', icon_name: 'Zap', path: '/admin/advertisements', sort_order: 6, is_active: true },
  { id: 21, type: 'admin', parent_id: null, label_key: 'admin_general_options', icon_name: 'SlidersHorizontal', path: null, sort_order: 7, is_active: true },
  { id: 20, type: 'admin', parent_id: null, label_key: 'admin_user_management', icon_name: 'Users', path: null, sort_order: 8, is_active: true },
  { id: 19, type: 'admin', parent_id: null, label_key: 'admin_system', icon_name: 'Settings', path: null, sort_order: 9, is_active: true },
  
  // 后台菜单 - 二级（常规选项）
  { id: 211, type: 'admin', parent_id: 21, label_key: 'admin_social_links', icon_name: 'Link', path: '/admin/social-links', sort_order: 1, is_active: true },
  { id: 212, type: 'admin', parent_id: 21, label_key: 'admin_media', icon_name: 'HardDrive', path: '/admin/media', sort_order: 2, is_active: true },
  { id: 213, type: 'admin', parent_id: 21, label_key: 'admin_basic_settings', icon_name: 'Settings', path: '/admin/settings', sort_order: 3, is_active: true },
  { id: 214, type: 'admin', parent_id: 21, label_key: 'admin_email_templates', icon_name: 'Mail', path: '/admin/email-templates', sort_order: 4, is_active: true },

  // 后台菜单 - 二级（内容管理）
  { id: 111, type: 'admin', parent_id: 11, label_key: 'admin_blogs', icon_name: 'Book', path: '/admin/posts', sort_order: 1, is_active: true },
  { id: 112, type: 'admin', parent_id: 11, label_key: 'admin_video_management', icon_name: 'Play', path: '/admin/videos', sort_order: 2, is_active: true },
  { id: 113, type: 'admin', parent_id: 11, label_key: 'admin_project_management', icon_name: 'FolderKanban', path: '/admin/projects', sort_order: 3, is_active: true },
  { id: 114, type: 'admin', parent_id: 11, label_key: 'admin_resource_management', icon_name: 'BookOpen', path: '/admin/resources', sort_order: 4, is_active: true },
  
  // 后台菜单 - 二级（系统管理）
  { id: 192, type: 'admin', parent_id: 19, label_key: 'admin_node_management', icon_name: 'Menu', path: '/admin/front-menu', sort_order: 1, is_active: true },
  { id: 193, type: 'admin', parent_id: 19, label_key: 'admin_roles', icon_name: 'Shield', path: '/admin/roles', sort_order: 2, is_active: true },
  { id: 198, type: 'admin', parent_id: 19, label_key: 'admin_mail_config', icon_name: 'Mail', path: '/admin/mail-config', sort_order: 3, is_active: true },
  { id: 194, type: 'admin', parent_id: 19, label_key: 'admin_logs', icon_name: 'FileText', path: '/admin/logs', sort_order: 4, is_active: true },
  { id: 195, type: 'admin', parent_id: 19, label_key: 'admin_backup', icon_name: 'Archive', path: '/admin/backup', sort_order: 5, is_active: true },
  { id: 196, type: 'admin', parent_id: 19, label_key: 'admin_restore', icon_name: 'RotateCcw', path: '/admin/restore', sort_order: 6, is_active: true },
  { id: 197, type: 'admin', parent_id: 19, label_key: 'admin_about', icon_name: 'Info', path: '/admin/about', sort_order: 7, is_active: true },

  // 后台菜单 - 二级（用户管理）
  { id: 201, type: 'admin', parent_id: 20, label_key: 'admin_users', icon_name: 'Users', path: '/admin/users', sort_order: 1, is_active: true },
  { id: 203, type: 'admin', parent_id: 20, label_key: 'admin_user_levels', icon_name: 'Crown', path: '/admin/user-levels', sort_order: 2, is_active: true },
];
