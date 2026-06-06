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

// ============================================================
// Console Welcome Message
// ============================================================
console.log(
    '%c\n' +
    '  ╔══════════════════════════════════════════════════╗\n' +
    '  ║                                                  ║\n' +
    '  ║          🚀  Archyx Blog System                 ║\n' +
    '  ║                                                  ║\n' +
    '  ║     A modern, AI-driven content platform        ║\n' +
    '  ║     for developers.                             ║\n' +
    '  ║                                                  ║\n' +
    '  ║     Laravel 11  •  Vue 3  •  Inertia.js         ║\n' +
    '  ║                                                  ║\n' +
    '  ║     📦  github.com/adlerdler/                   ║\n' +
    '  ║         Archyx-Blog-System                      ║\n' +
    '  ║                                                  ║\n' +
    '  ║     © 2026 Archyx  |  Open Source               ║\n' +
    '  ║                                                  ║\n' +
    '  ╚══════════════════════════════════════════════════╝\n',
    'color: #e2e8f0; background: #1e293b; font-size: 12px; font-family: "Courier New", monospace; padding: 12px; border-radius: 8px; line-height: 1.6;'
);

console.log(
    '%c🔗 Click to visit: %chttps://github.com/adlerdler/Archyx-Blog-System',
    'color: #94a3b8; font-size: 11px; font-family: monospace;',
    'color: #60a5fa; font-size: 11px; font-weight: bold; font-family: monospace; text-decoration: underline; cursor: pointer;'
);

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import { i18n, syncLocalesFromBackend, reapplyBrowserLocale } from './i18n';
import { useThemeStore } from './stores/theme';
import AdminLayout from './Pages/admin/Layout.vue';
import ErrorPage from './components/ErrorPage.vue';
import ForbiddenPage from './components/Forbidden.vue';

const pinia = createPinia();

const getPageKeyFromRoute = (url) => {
    if (!url || url === '/' || url === '') return 'home';
    const segments = url.split('/').filter(Boolean);
    if (segments.length === 0) return 'home';
    
    if (segments[0] === 'blog' && segments[1]) return 'post_detail';
    if (segments[0] === 'projects' && segments[1]) return 'project_detail';
    if (segments[0] === 'videos' && segments[1]) return 'video_detail';
    
    return segments[0];
};

/**
 * 动态设置站点 Favicon
 * 如果后台设置了 siteConfig.favicon 则使用之，否则保持默认
 */
const setFavicon = (pageProps) => {
    const favicon = pageProps?.siteConfig?.favicon;
    if (favicon) {
        let link = document.getElementById('dynamic-favicon');
        if (!link) {
            link = document.createElement('link');
            link.id = 'dynamic-favicon';
            link.rel = 'icon';
            document.head.appendChild(link);
        }
        link.href = favicon;
    }
};

router.on('navigate', (event) => {
    setTimeout(() => {
        const pageProps = event.detail.page.props;
        const pageSeo = pageProps.pageSeo || [];
        const url = event.detail.page.url;
        const pageKey = getPageKeyFromRoute(url);
        
        setFavicon(pageProps);

        if (pageKey === 'post_detail' && pageProps.post?.title) {
            document.title = pageProps.post.meta_title || `${pageProps.post.title} - ARCHYX`;
        } else if (pageKey === 'project_detail' && pageProps.project?.title) {
            document.title = pageProps.project.title;
        } else if (pageKey === 'video_detail' && pageProps.video?.title) {
            document.title = pageProps.video.title;
        } else {
            const seoData = pageSeo.find(s => s.pageKey === pageKey);
            if (seoData && seoData.title) {
                document.title = seoData.title;
            } else {
                document.title = 'Archyx Blog';
            }
        }
    }, 50);
});

// ============================================================
// 跨标签页登录状态自动同步
// 当用户切回当前标签页时，自动从服务端刷新 auth 状态，
// 解决在另一个标签页登录/登出后，当前页导航栏不更新的问题。
// ============================================================
let lastVisibilityChange = 0;
let authReloadPending = false;

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        const now = Date.now();
        // 防抖：3秒内不重复刷新
        if (now - lastVisibilityChange < 3000) return;
        lastVisibilityChange = now;

        // 避免并发请求
        if (authReloadPending) return;
        authReloadPending = true;

        // 仅刷新 auth 数据，不重载整个页面
        router.reload({
            only: ['auth'],
            onFinish: () => { authReloadPending = false; },
            onError: () => { authReloadPending = false; },
        });
    }
});

createInertiaApp({
    title: (title) => title || 'Archyx Blog',
    resolve: (name) => {

        // components/ 中的独立组件直接返回，不需要 Pages/ 包装器
        if (name === 'ErrorPage') {
            return Promise.resolve({ default: ErrorPage });
        }
        if (name === 'Forbidden') {
            return Promise.resolve({ default: ForbiddenPage, layout: AdminLayout });
        }

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
            
            return page;
        } catch (error) {
            console.error('[ERROR] Failed to resolve page:', name, error);
            throw error;
        }
    },
    setup({ el, App, props, plugin }) {
        try {
            // 初始加载时设置 favicon
            if (props.initialPage?.props) {
                setFavicon(props.initialPage.props);
            }

            // 从 Inertia 初始页面 props 同步语言列表，并匹配浏览器语言
            if (props.initialPage?.props?.languages) {
                syncLocalesFromBackend(props.initialPage.props.languages);
                reapplyBrowserLocale();
            }

            const app = createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                .use(pinia)
                .use(i18n)
                .component('ErrorPage', ErrorPage);

            // Pinia 已激活，初始化主题（DOM class/css 变量恢复）
            useThemeStore().initTheme();

            return app.mount(el);
        } catch (error) {
            console.error('[ERROR] Failed to setup Inertia app:', error);
            throw error;
        }
    },
});
