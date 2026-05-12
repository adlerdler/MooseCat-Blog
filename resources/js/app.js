import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { i18n } from './i18n';
import { useTheme } from './composables/useTheme';
import router from './router';

// 页面组件
import Home from './Pages/Home.vue';
import Blog from './Pages/Blog.vue';
import Projects from './Pages/Projects.vue';
import Resources from './Pages/Resources.vue';
import Videos from './Pages/Videos.vue';

const { initTheme } = useTheme();
initTheme();

const app = createApp({
  template: '<router-view />',
});

// 注册全局组件
const components = import.meta.glob('./components/*.vue', { eager: true });
if (components && typeof components === 'object') {
    Object.entries(components).forEach(([path, definition]) => {
        const componentName = path.split('/').pop().replace(/\.\w+$/, '');
        if (definition && definition.default) {
            app.component(componentName, definition.default);
        }
    });
}

// 注册页面组件（支持 Blade 模板使用）
app.component('home-page', Home);
app.component('blog-page', Blog);
app.component('projects-page', Projects);
app.component('resources-page', Resources);
app.component('videos-page', Videos);

app.use(i18n);
app.use(router);

app.mount('#app');