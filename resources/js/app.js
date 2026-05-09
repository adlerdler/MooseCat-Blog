import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { i18n } from './i18n';
import { useTheme } from './composables/useTheme';
import router from './router';

const { initTheme } = useTheme();
initTheme();

const app = createApp({
  template: '<router-view />',
});

const components = import.meta.glob('./components/*.vue', { eager: true });
if (components && typeof components === 'object') {
    Object.entries(components).forEach(([path, definition]) => {
        const componentName = path.split('/').pop().replace(/\.\w+$/, '');
        if (definition && definition.default) {
            app.component(componentName, definition.default);
        }
    });
}

app.use(i18n);
app.use(router);

app.mount('#app');