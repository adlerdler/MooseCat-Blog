/**
 * app.js - Vue 应用入口文件（Inertia.js 版本）
 * 
 * 功能说明：
 * - 使用 Inertia.js 创建 Vue 应用
 * - 注册全局组件和插件
 * - 初始化主题和国际化
 * - 使用持久化布局统一管理页面布局
 */
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { i18n } from './i18n';
import { useTheme } from './composables/useTheme';
import AdminLayout from './Pages/admin/Layout.vue';

const { initTheme } = useTheme();
initTheme();

createInertiaApp({
    resolve: (name) => {
        console.log('[DEBUG] Inertia resolving page:', name);
        try {
            const page = resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue')
            );
            
            // 使用持久化布局：所有 admin/ 开头的页面使用 AdminLayout，但排除登录页面
            page.then((module) => {
                if (name.startsWith('admin/') && name !== 'admin/Login') {
                    module.default.layout = AdminLayout;
                }
            });
            
            console.log('[DEBUG] Inertia resolved page:', page);
            return page;
        } catch (error) {
            console.error('[ERROR] Failed to resolve page:', name, error);
            throw error;
        }
    },
    setup({ el, App, props, plugin }) {
        console.log('[DEBUG] Inertia setup, props:', props);
        console.log('[DEBUG] Inertia setup, el:', el);
        try {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                .use(i18n)
                .mount(el);
        } catch (error) {
            console.error('[ERROR] Failed to setup Inertia app:', error);
            throw error;
        }
    },
});