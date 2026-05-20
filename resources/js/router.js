/**
 * router.js - Vue Router 路由配置
 *
 * 功能说明：
 * - 定义应用的所有路由规则
 * - 配置路由元信息（页面标题）
 * - 实现导航守卫（页面标题更新、滚动行为、登录认证）
 *
 * 路由列表：
 * /                     - 首页
 * /blog                 - 博客列表
 * /blog/:id             - 文章详情
 * /videos               - 视频列表
 * /videos/:id           - 视频详情
 * /projects             - 项目列表
 * /projects/:id         - 项目详情
 * /resources             - 资源下载
 * /author/:id?          - 作者介绍
 * /admin/login          - 管理后台登录页
 * /admin/index          - 管理后台首页
 * /admin/posts          - 文章管理
 * /admin/videos         - 视频管理
 * /admin/projects       - 项目管理
 * /admin/resources      - 资源管理
 * /*                    - 404 错误页
 */
import { createRouter, createWebHistory } from 'vue-router';
import Home from './Pages/Front/Home.vue';
import Blog from './Pages/front/Blog.vue';
import PostDetail from './Pages/front/PostDetail.vue';
import Author from './Pages/front/Author.vue';
import Videos from './Pages/Front/Videos.vue';
import VideoDetail from './Pages/Front/VideoDetail.vue';
import Projects from './Pages/front/Projects.vue';
import ProjectDetail from './Pages/front/ProjectDetail.vue';
import Resources from './Pages/Front/Resources.vue';
import MaintenanceMode from './Pages/front/MaintenanceMode.vue';
import ErrorPage from './components/ErrorPage.vue';
import Index from './Pages/admin/Index.vue';
import Login from './Pages/admin/Login.vue';
import AdminPosts from './Pages/admin/Posts.vue';
import AdminVideos from './Pages/admin/Videos.vue';
import AdminProjects from './Pages/admin/Projects.vue';
import AdminResources from './Pages/admin/Resources.vue';
import AdminUsers from './Pages/admin/Users.vue';
import UserDetail from './Pages/admin/UserDetail.vue';
import AdminSettings from './Pages/admin/Settings.vue';
import AdminAbout from './Pages/admin/About.vue';
import AdminCategories from './Pages/admin/Categories.vue';
import AdminTags from './Pages/admin/Tags.vue';
import AdminComments from './Pages/admin/Comments.vue';
import AdminRoles from './Pages/admin/Roles.vue';
import AdminMedia from './Pages/admin/Media.vue';
import AdminLogs from './Pages/admin/Logs.vue';
import AdminBackup from './Pages/admin/Backup.vue';
import AdminRestore from './Pages/admin/Restore.vue';
import AdminAdvertisements from './Pages/admin/Advertisements.vue';
import SocialLinks from './Pages/admin/SocialLinks.vue';
import AdminFrontMenu from './Pages/admin/FrontMenu.vue';
import AdminEmailTemplates from './Pages/admin/EmailTemplates.vue';
import AdminUserLevels from './Pages/admin/UserLevels.vue';
import AdminMailConfig from './Pages/admin/MailConfig.vue';
import AdminJournals from './Pages/admin/Journals.vue';
import AdminSubscribers from './Pages/admin/Subscribers.vue';
import { isMaintenanceMode } from './data/site_config';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: { title: 'ARCHYX // Digital Architecture' },
  },
  {
    path: '/blog',
    name: 'blog',
    component: Blog,
    meta: { title: 'BLOG // Archive' },
  },
  {
    path: '/blog/:id',
    name: 'post-detail',
    component: PostDetail,
    meta: { title: 'POST // Article' },
  },
  {
    path: '/videos',
    name: 'videos',
    component: Videos,
    meta: { title: 'VIDEOS // Broadcasts' },
  },
  {
    path: '/videos/:id',
    name: 'video-detail',
    component: VideoDetail,
    meta: { title: 'VIDEO // Playback' },
  },
  {
    path: '/projects',
    name: 'projects',
    component: Projects,
    meta: { title: 'PROJECTS // Experiments' },
  },
  {
    path: '/projects/:id',
    name: 'project-detail',
    component: ProjectDetail,
    meta: { title: 'PROJECT // Detail' },
  },
  {
    path: '/resources',
    name: 'resources',
    component: Resources,
    meta: { title: 'RESOURCES // Library' },
  },
  {
    path: '/admin/login',
    name: 'admin-login',
    component: Login,
    meta: { title: 'ADMIN // Login', requiresAuth: false },
  },
  {
    path: '/admin/index',
    name: 'admin-index',
    component: Index,
    meta: { title: 'ADMIN // Control Panel', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/posts',
    name: 'admin-posts',
    component: AdminPosts,
    meta: { title: 'ADMIN // Posts', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/videos',
    name: 'admin-videos',
    component: AdminVideos,
    meta: { title: 'ADMIN // Videos', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/projects',
    name: 'admin-projects',
    component: AdminProjects,
    meta: { title: 'ADMIN // Projects', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/resources',
    name: 'admin-resources',
    component: AdminResources,
    meta: { title: 'ADMIN // Resources', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/users/:id',
    name: 'admin-user-detail',
    component: UserDetail,
    meta: { title: 'ADMIN // User Detail', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: AdminUsers,
    meta: { title: 'ADMIN // Users', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/user-levels',
    name: 'admin-user-levels',
    component: AdminUserLevels,
    meta: { title: 'ADMIN // User Levels', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/journals',
    name: 'admin-journals',
    component: AdminJournals,
    meta: { title: 'ADMIN // Journals', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/subscribers',
    name: 'admin-subscribers',
    component: AdminSubscribers,
    meta: { title: 'ADMIN // Subscribers', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: AdminSettings,
    meta: { title: 'ADMIN // Settings', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/mail-config',
    name: 'admin-mail-config',
    component: AdminMailConfig,
    meta: { title: 'ADMIN // Mail Config', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/about',
    name: 'admin-about',
    component: AdminAbout,
    meta: { title: 'ADMIN // About', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/categories',
    name: 'admin-categories',
    component: AdminCategories,
    meta: { title: 'ADMIN // Categories', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/tags',
    name: 'admin-tags',
    component: AdminTags,
    meta: { title: 'ADMIN // Tags', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/comments',
    name: 'admin-comments',
    component: AdminComments,
    meta: { title: 'ADMIN // Comments', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/roles',
    name: 'admin-roles',
    component: AdminRoles,
    meta: { title: 'ADMIN // Roles', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/media',
    name: 'admin-media',
    component: AdminMedia,
    meta: { title: 'ADMIN // Media', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/logs',
    name: 'admin-logs',
    component: AdminLogs,
    meta: { title: 'ADMIN // Logs', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/backup',
    name: 'admin-backup',
    component: AdminBackup,
    meta: { title: 'ADMIN // Backup', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/restore',
    name: 'admin-restore',
    component: AdminRestore,
    meta: { title: 'ADMIN // Restore', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/front-menu',
    name: 'admin-front-menu',
    component: AdminFrontMenu,
    meta: { title: 'ADMIN // Front Menu', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/email-templates',
    name: 'admin-email-templates',
    component: AdminEmailTemplates,
    meta: { title: 'ADMIN // Email Templates', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/advertisements',
    name: 'admin-advertisements',
    component: AdminAdvertisements,
    meta: { title: 'ADMIN // Advertisements', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin/social-links',
    name: 'admin-social-links',
    component: SocialLinks,
    meta: { title: 'ADMIN // Footer Manager', requiresAuth: true, layout: 'admin' },
  },
  {
    path: '/admin',
    name: 'admin',
    redirect: '/admin/index',
  },
  {
    path: '/maintenance',
    name: 'maintenance',
    component: MaintenanceMode,
    meta: { title: 'MAINTENANCE // System Upgrade' },
  },
  {
    path: '/author/:id?',
    name: 'author',
    component: Author,
    meta: { title: 'AUTHOR // Profile' },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: ErrorPage,
    meta: { title: 'ERROR // Not Found' },
    props: (route) => ({
      errorCode: parseInt(route.params.errorCode) || 404,
      errorPath: route.fullPath
    }),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0, behavior: 'smooth' };
    }
  },
});

const isLoggedIn = () => {
  return localStorage.getItem('admin_logged_in') === 'true';
};

router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title;
  } else {
    document.title = 'ARCHYX // Digital Architecture';
  }

  if (to.meta.requiresAuth && !isLoggedIn()) {
    next('/admin/login');
  } else if (to.path === '/admin/login' && isLoggedIn()) {
    next('/admin/index');
  } else if (isMaintenanceMode() && !to.path.startsWith('/admin') && to.path !== '/maintenance') {
    next('/maintenance');
  } else if (!isMaintenanceMode() && to.path === '/maintenance') {
    next('/');
  } else {
    next();
  }
});

router.afterEach(() => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

export default router;
