export const adminMenuItems = [
  { id: 'dashboard', labelKey: 'admin_dashboard', iconName: 'LayoutDashboard', route: '/admin/index' },
  {
    id: 'content',
    labelKey: 'admin_content',
    iconName: 'FileText',
    children: [
      { id: 'posts', labelKey: 'admin_blogs', iconName: 'Book', route: '/admin/posts' },
      { id: 'videos', labelKey: 'admin_video_management', iconName: 'Play', route: '/admin/videos' },
      { id: 'projects', labelKey: 'admin_project_management', iconName: 'FolderKanban', route: '/admin/projects' },
      { id: 'resources', labelKey: 'admin_resource_management', iconName: 'BookOpen', route: '/admin/resources' },
    ]
  },
  { id: 'categories', labelKey: 'admin_categories', iconName: 'Folder', route: '/admin/categories' },
  { id: 'tags', labelKey: 'admin_tags', iconName: 'Tag', route: '/admin/tags' },
  { id: 'comments', labelKey: 'admin_comments', iconName: 'MessageSquare', route: '/admin/comments' },
  { id: 'advertisements', labelKey: 'admin_advertisements', iconName: 'Zap', route: '/admin/advertisements' },
  { id: 'social-links', labelKey: 'admin_social_links', iconName: 'Link', route: '/admin/social-links' },
  { id: 'users', labelKey: 'admin_users', iconName: 'Users', route: '/admin/users' },
  { id: 'media', labelKey: 'admin_media', iconName: 'HardDrive', route: '/admin/media' },
  {
    id: 'system',
    labelKey: 'admin_system',
    iconName: 'Settings',
    children: [
      { id: 'settings', labelKey: 'admin_basic_settings', iconName: 'SlidersHorizontal', route: '/admin/settings' },
      { id: 'front_menu', labelKey: 'admin_node_management', iconName: 'Menu', route: '/admin/front-menu' },
      { id: 'roles', labelKey: 'admin_roles', iconName: 'Shield', route: '/admin/roles' },
      { id: 'logs', labelKey: 'admin_logs', iconName: 'FileText', route: '/admin/logs' },
      { id: 'backup', labelKey: 'admin_backup', iconName: 'Archive', route: '/admin/backup' },
      { id: 'restore', labelKey: 'admin_restore', iconName: 'RotateCcw', route: '/admin/restore' },
      { id: 'about', labelKey: 'admin_about', iconName: 'Info', route: '/admin/about' },
    ]
  },
];