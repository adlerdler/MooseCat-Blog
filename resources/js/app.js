import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';

// 1. 创建 Vue 实例
const app = createApp({});

// 2. 自动注册 resources/js/components 下的所有组件
// 这样你以后在 Pages 页面里可以直接用 <navbar />, <blog-card /> 等
const components = import.meta.glob('./components/*.vue', { eager: true });
Object.entries(components).forEach(([path, definition]) => {
    const componentName = path.split('/').pop().replace(/\.\w+$/, '');
    app.component(componentName, definition.default);
});

// 3. 动态加载当前页面 (暂定以 Welcome 为首页)
// 以后我们可以根据 Blade 传参来切换这里的页面
import WelcomePage from './Pages/Welcome.vue';
app.component('WelcomePage', WelcomePage);

app.mount('#app');
