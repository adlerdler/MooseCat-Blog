import { createRouter, createWebHistory } from 'vue-router';
import Home from './Pages/Home.vue';
import Blog from './Pages/Blog.vue';
import Author from './Pages/Author.vue';
import ErrorPage from './components/ErrorPage.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/blog',
    name: 'blog',
    component: Blog,
  },
  {
    path: '/author/:id?',
    name: 'author',
    component: Author,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: ErrorPage,
    props: (route) => ({
      errorCode: parseInt(route.params.errorCode) || 404,
      errorPath: route.fullPath
    }),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;