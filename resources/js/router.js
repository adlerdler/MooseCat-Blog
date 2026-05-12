import { createRouter, createWebHistory } from 'vue-router';
import Home from './Pages/Home.vue';
import Blog from './Pages/Blog.vue';
import PostDetail from './Pages/PostDetail.vue';
import Author from './Pages/Author.vue';
import Videos from './Pages/Videos.vue';
import VideoDetail from './Pages/VideoDetail.vue';
import Projects from './Pages/Projects.vue';
import ProjectDetail from './Pages/ProjectDetail.vue';
import Resources from './Pages/Resources.vue';
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
    path: '/blog/:id',
    name: 'post-detail',
    component: PostDetail,
  },
  {
    path: '/videos',
    name: 'videos',
    component: Videos,
  },
  {
    path: '/videos/:id',
    name: 'video-detail',
    component: VideoDetail,
  },
  {
    path: '/projects',
    name: 'projects',
    component: Projects,
  },
  {
    path: '/projects/:id',
    name: 'project-detail',
    component: ProjectDetail,
  },
  {
    path: '/resources',
    name: 'resources',
    component: Resources,
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