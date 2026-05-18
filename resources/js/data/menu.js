/**
 * menu.js - 菜单配置数据（数据库形式模拟）
 * 
 * 功能说明：
 * - 模拟数据库菜单表结构
 * - 统一管理前台和后台菜单
 * - 支持多级菜单（通过parentId关联）
 * 
 * 字段说明：
 * - id: 菜单唯一标识
 * - type: 菜单类型（front/admin）
 * - parentId: 父级菜单ID，null表示一级菜单
 * - labelKey: 国际化翻译key
 * - iconName: 图标名称（仅后台菜单）
 * - path: 路由路径
 * - sortOrder: 排序顺序
 * - isActive: 是否启用
 */

export const menus = [
  // 前台菜单
  { id: 1, type: 'front', parentId: null, labelKey: 'nav_home', iconName: null, path: '/', sortOrder: 1, isActive: true },
  { id: 2, type: 'front', parentId: null, labelKey: 'nav_blog', iconName: null, path: '/blog', sortOrder: 2, isActive: true },
  { id: 3, type: 'front', parentId: null, labelKey: 'nav_videos', iconName: null, path: '/videos', sortOrder: 3, isActive: true },
  { id: 4, type: 'front', parentId: null, labelKey: 'nav_projects', iconName: null, path: '/projects', sortOrder: 4, isActive: true },
  { id: 5, type: 'front', parentId: null, labelKey: 'nav_resources', iconName: null, path: '/resources', sortOrder: 5, isActive: true },
  { id: 6, type: 'front', parentId: null, labelKey: 'nav_author', iconName: null, path: '/author', sortOrder: 6, isActive: true },
  
  // 后台菜单 - 一级
  { id: 10, type: 'admin', parentId: null, labelKey: 'admin_dashboard', iconName: 'LayoutDashboard', path: '/admin/index', sortOrder: 1, isActive: true },
  { id: 11, type: 'admin', parentId: null, labelKey: 'admin_content', iconName: 'FileText', path: null, sortOrder: 2, isActive: true },
  { id: 12, type: 'admin', parentId: null, labelKey: 'admin_categories', iconName: 'Folder', path: '/admin/categories', sortOrder: 3, isActive: true },
  { id: 13, type: 'admin', parentId: null, labelKey: 'admin_tags', iconName: 'Tag', path: '/admin/tags', sortOrder: 4, isActive: true },
  { id: 14, type: 'admin', parentId: null, labelKey: 'admin_comments', iconName: 'MessageSquare', path: '/admin/comments', sortOrder: 5, isActive: true },
  { id: 15, type: 'admin', parentId: null, labelKey: 'admin_advertisements', iconName: 'Zap', path: '/admin/advertisements', sortOrder: 6, isActive: true },
  { id: 16, type: 'admin', parentId: null, labelKey: 'admin_social_links', iconName: 'Link', path: '/admin/social-links', sortOrder: 7, isActive: true },
  { id: 18, type: 'admin', parentId: null, labelKey: 'admin_media', iconName: 'HardDrive', path: '/admin/media', sortOrder: 8, isActive: true },
  { id: 19, type: 'admin', parentId: null, labelKey: 'admin_system', iconName: 'Settings', path: null, sortOrder: 9, isActive: true },
  
  // 后台菜单 - 二级（内容管理）
  { id: 111, type: 'admin', parentId: 11, labelKey: 'admin_blogs', iconName: 'Book', path: '/admin/posts', sortOrder: 1, isActive: true },
  { id: 112, type: 'admin', parentId: 11, labelKey: 'admin_video_management', iconName: 'Play', path: '/admin/videos', sortOrder: 2, isActive: true },
  { id: 113, type: 'admin', parentId: 11, labelKey: 'admin_project_management', iconName: 'FolderKanban', path: '/admin/projects', sortOrder: 3, isActive: true },
  { id: 114, type: 'admin', parentId: 11, labelKey: 'admin_resource_management', iconName: 'BookOpen', path: '/admin/resources', sortOrder: 4, isActive: true },
  
  // 后台菜单 - 二级（系统管理）
  { id: 191, type: 'admin', parentId: 19, labelKey: 'admin_basic_settings', iconName: 'SlidersHorizontal', path: '/admin/settings', sortOrder: 1, isActive: true },
  { id: 198, type: 'admin', parentId: 19, labelKey: 'admin_users', iconName: 'Users', path: '/admin/users', sortOrder: 2, isActive: true },
  { id: 192, type: 'admin', parentId: 19, labelKey: 'admin_node_management', iconName: 'Menu', path: '/admin/front-menu', sortOrder: 3, isActive: true },
  { id: 193, type: 'admin', parentId: 19, labelKey: 'admin_roles', iconName: 'Shield', path: '/admin/roles', sortOrder: 4, isActive: true },
  { id: 194, type: 'admin', parentId: 19, labelKey: 'admin_logs', iconName: 'FileText', path: '/admin/logs', sortOrder: 5, isActive: true },
  { id: 195, type: 'admin', parentId: 19, labelKey: 'admin_backup', iconName: 'Archive', path: '/admin/backup', sortOrder: 6, isActive: true },
  { id: 196, type: 'admin', parentId: 19, labelKey: 'admin_restore', iconName: 'RotateCcw', path: '/admin/restore', sortOrder: 7, isActive: true },
  { id: 197, type: 'admin', parentId: 19, labelKey: 'admin_about', iconName: 'Info', path: '/admin/about', sortOrder: 8, isActive: true },
];

