export const adminMenuItems = [
  { id: 'dashboard', labelKey: 'admin_dashboard', iconKey: 'layoutDashboard', route: '/admin/index' },
  {
    id: 'content',
    labelKey: 'admin_content',
    iconKey: 'fileText',
    children: [
      { id: 'posts', labelKey: 'admin_posts', iconKey: 'fileText', route: '/admin/posts' },
      { id: 'videos', labelKey: 'admin_videos', iconKey: 'play', route: '/admin/videos' },
      { id: 'projects', labelKey: 'admin_projects', iconKey: 'folderKanban', route: '/admin/projects' },
      { id: 'resources', labelKey: 'admin_resources', iconKey: 'bookOpen', route: '/admin/resources' },
    ]
  },
  { id: 'categories', labelKey: 'admin_categories', iconKey: 'folder', route: '/admin/categories' },
  { id: 'tags', labelKey: 'admin_tags', iconKey: 'tag', route: '/admin/tags' },
  { id: 'comments', labelKey: 'admin_comments', iconKey: 'messageSquare', route: '/admin/comments' },
  { id: 'users', labelKey: 'admin_users', iconKey: 'users', route: '/admin/users' },
  { id: 'roles', labelKey: 'admin_roles', iconKey: 'shield', route: '/admin/roles' },
  { id: 'media', labelKey: 'admin_media', iconKey: 'hardDrive', route: '/admin/media' },
  {
    id: 'system',
    labelKey: 'admin_system',
    iconKey: 'settings',
    children: [
      { id: 'settings', labelKey: 'admin_settings', iconKey: 'settings', route: '/admin/settings' },
      { id: 'about', labelKey: 'admin_about', iconKey: 'info', route: '/admin/about' },
    ]
  },
];