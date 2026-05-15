export const adminMenuItems = [
  { id: 'dashboard', labelKey: 'admin_dashboard', iconKey: 'layoutDashboard', route: '/admin/index' },
  {
    id: 'content',
    labelKey: 'admin_content',
    iconKey: 'fileText',
    children: [
      { id: 'posts', labelKey: 'admin_blogs', iconKey: 'books', route: '/admin/posts' },
      { id: 'videos', labelKey: 'admin_video_management', iconKey: 'play', route: '/admin/videos' },
      { id: 'projects', labelKey: 'admin_project_management', iconKey: 'folderKanban', route: '/admin/projects' },
      { id: 'resources', labelKey: 'admin_resource_management', iconKey: 'bookOpen', route: '/admin/resources' },
    ]
  },
  { id: 'categories', labelKey: 'admin_categories', iconKey: 'folder', route: '/admin/categories' },
  { id: 'tags', labelKey: 'admin_tags', iconKey: 'tag', route: '/admin/tags' },
  { id: 'comments', labelKey: 'admin_comments', iconKey: 'messageSquare', route: '/admin/comments' },
  { id: 'users', labelKey: 'admin_users', iconKey: 'users', route: '/admin/users' },
  { id: 'media', labelKey: 'admin_media', iconKey: 'hardDrive', route: '/admin/media' },
  {
    id: 'system',
    labelKey: 'admin_system',
    iconKey: 'settings',
    children: [
      { id: 'settings', labelKey: 'admin_basic_settings', iconKey: 'sliders', route: '/admin/settings' },
      { id: 'roles', labelKey: 'admin_roles', iconKey: 'shield', route: '/admin/roles' },
      { id: 'logs', labelKey: 'admin_logs', iconKey: 'fileText', route: '/admin/logs' },
      { id: 'backup', labelKey: 'admin_backup', iconKey: 'archive', route: '/admin/backup' },
      { id: 'about', labelKey: 'admin_about', iconKey: 'info', route: '/admin/about' },
    ]
  },
];