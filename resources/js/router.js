import { createRouter, createWebHistory } from 'vue-router';
import WelcomePage from './Pages/Welcome.vue';
import Home from './Pages/Home.vue';
import Blog from './Pages/Blog.vue';

const routes = [
  {
    path: '/',
    name: 'welcome',
    component: WelcomePage,
  },
  {
    path: '/home',
    name: 'home',
    component: Home,
  },
  {
    path: '/blog',
    name: 'blog',
    component: Blog,
  },
  {
    path: '/posts',
    name: 'posts',
    component: Home,
  },
  {
    path: '/videos',
    name: 'videos',
    component: Home,
  },
  {
    path: '/projects',
    name: 'projects',
    component: Home,
  },
  {
    path: '/resources',
    name: 'resources',
    component: Home,
  },
  {
    path: '/author',
    name: 'author',
    component: Home,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;