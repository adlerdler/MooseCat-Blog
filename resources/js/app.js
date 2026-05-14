/**
 * app.js - Vue 应用入口文件
 * 
 * 功能说明：
 * - 创建并配置 Vue 应用实例
 * - 注册全局组件和插件
 * - 初始化主题和国际化
 * 
 * 初始化流程：
 * 1. 初始化主题系统
 * 2. 创建 Vue 应用实例
 * 3. 注册全局组件（通过 glob 批量导入）
 * 4. 注册页面组件（支持 Blade 模板使用）
 * 5. 使用插件（i18n、router）
 * 6. 挂载应用到 DOM
 */
import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { i18n } from './i18n';
import { useTheme } from './composables/useTheme';
import router from './router';
import App from './App.vue';

// 页面组件
import Home from './Pages/Front/Home.vue';
import Blog from './Pages/front/Blog.vue';  
import Projects from './Pages/Front/Projects.vue';
import Resources from './Pages/Front/Resources.vue';
import Videos from './Pages/Front/Videos.vue';

const { initTheme } = useTheme();
initTheme();

const app = createApp(App);

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