/**
 * router.js - Vue Router 路由配置
 * 
 * 功能说明：
 * - 定义应用的所有路由规则
 * - 配置路由元信息（页面标题）
 * - 实现导航守卫（页面标题更新、滚动行为）
 * 
 * 路由列表：
 * /                   - 首页
 * /blog               - 博客列表
 * /blog/:id           - 文章详情
 * /videos             - 视频列表
 * /videos/:id         - 视频详情
 * /projects            - 项目列表
 * /projects/:id        - 项目详情
 * /resources           - 资源下载
 * /author/:id?         - 作者介绍
 * /admin               - 管理后台
 * /*                   - 404 错误页
 */
import { createRouter, createWebHistory } from 'vue-router';
import Home from './Pages/Front/Home.vue';
import Blog from './Pages/Front/Blog.vue';
import PostDetail from './Pages/Front/PostDetail.vue';
import Author from './Pages/Front/Author.vue';
import Videos from './Pages/Front/Videos.vue';
import VideoDetail from './Pages/Front/VideoDetail.vue';
import Projects from './Pages/Front/Projects.vue';
import ProjectDetail from './Pages/Front/ProjectDetail.vue';
import Resources from './Pages/Front/Resources.vue';
import ErrorPage from './components/ErrorPage.vue';
import Index from './Pages/Admin/Index.vue';

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
    path: '/admin',
    name: 'admin',
    component: Index,
    meta: { title: 'ADMIN // Control Panel' },
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

// 导航守卫：动态更新页面标题
router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title;
  } else {
    document.title = 'ARCHYX // Digital Architecture';
  }
  
  next();
});

// 导航完成后滚动到顶部
router.afterEach(() => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

export default router;