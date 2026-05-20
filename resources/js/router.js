/**
 * router.js - Vue Router 路由配置
 *
 * 功能说明：
 * - 定义应用的所有路由规则
 * - 配置路由元信息（页面标题）
 * - 实现导航守卫（页面标题更新、滚动行为、登录认证）
 * - 支持从 menu.js 动态注册前后台路由
 *
 * 新增页面方法：
 * 1. 在 menu.js 中添加菜单项（设置 path, component_name, type）
 * 2. 创建对应的组件文件
 * 3. 无需修改 router.js
 */
import { createRouter, createWebHistory } from 'vue-router';
import PostDetail from './Pages/front/PostDetail.vue';
import VideoDetail from './Pages/Front/VideoDetail.vue';
import ProjectDetail from './Pages/front/ProjectDetail.vue';
import MaintenanceMode from './Pages/front/MaintenanceMode.vue';
import ErrorPage from './components/ErrorPage.vue';
import Login from './Pages/admin/Login.vue';
import UserDetail from './Pages/admin/UserDetail.vue';
import { useSiteConfig } from './composables/useSiteConfig';
import { menus } from './data/menu';
import { componentRegistry } from './componentRegistry';

const getFrontMenusWithRoutes = () => menus.filter(m => m.type === 'front' && m.path && m.component_name);
const getAdminMenusWithRoutes = () => menus.filter(m => m.type === 'admin' && m.path && m.component_name);

const baseRoutes = [
  {
    path: '/blog/:id',
    name: 'post-detail',
    component: PostDetail,
    meta: { title: 'POST // Article' },
  },
  {
    path: '/videos/:id',
    name: 'video-detail',
    component: VideoDetail,
    meta: { title: 'VIDEO // Playback' },
  },
  {
    path: '/projects/:id',
    name: 'project-detail',
    component: ProjectDetail,
    meta: { title: 'PROJECT // Detail' },
  },
  {
    path: '/admin/login',
    name: 'admin-login',
    component: Login,
    meta: { title: 'ADMIN // Login', requiresAuth: false },
  },
  {
    path: '/admin/users/:id',
    name: 'admin-user-detail',
    component: UserDetail,
    meta: { title: 'ADMIN // User Detail', requiresAuth: true, layout: 'admin' },
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
  routes: baseRoutes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0, behavior: 'smooth' };
    }
  },
});

function generateRouteName(path) {
  return path
    .replace(/^\/admin\//, 'admin-')
    .replace(/^\//, '')
    .replace(/\//g, '-');
}

function registerFrontRoutes() {
  const frontMenus = getFrontMenusWithRoutes();
  let count = 0;

  frontMenus.forEach(menu => {
    const routeName = generateRouteName(menu.path) || 'home';
    const titleKey = menu.label_key.replace('nav_', '').replace(/_/g, ' ').toUpperCase();

    router.addRoute({
      path: menu.path,
      name: routeName,
      component: componentRegistry.getComponent(menu.component_name, 'front'),
      meta: {
        title: titleKey ? `${titleKey} // ARCHYX` : 'ARCHYX // Digital Architecture'
      }
    });
    count++;
  });

  // console.log(`[Router] Dynamically registered ${count} front routes`);
}

function registerAdminRoutes() {
  const adminMenus = getAdminMenusWithRoutes();
  let count = 0;

  adminMenus.forEach(menu => {
    const routeName = generateRouteName(menu.path);
    const titleKey = menu.label_key.replace('admin_', '').replace(/_/g, ' ').toUpperCase();

    router.addRoute('admin', {
      path: menu.path,
      name: routeName,
      component: componentRegistry.getComponent(menu.component_name, 'admin'),
      meta: {
        title: `ADMIN // ${titleKey}`,
        requiresAuth: true,
        layout: 'admin'
      }
    });
    count++;
  });

  // console.log(`[Router] Dynamically registered ${count} admin routes`);
}

registerFrontRoutes();
registerAdminRoutes();

const isLoggedIn = () => {
  return localStorage.getItem('admin_logged_in') === 'true';
};

router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title;
  } else {
    document.title = 'ARCHYX // Digital Architecture';
  }

  const { isMaintenanceMode } = useSiteConfig();

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

export { menus };
export default router;
