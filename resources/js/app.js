import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { i18n } from './i18n';

const app = createApp({});

const components = import.meta.glob('./components/*.vue', { eager: true });
Object.entries(components).forEach(([path, definition]) => {
    const componentName = path.split('/').pop().replace(/\.\w+$/, '');
    app.component(componentName, definition.default);
});

import WelcomePage from './Pages/Welcome.vue';
import Home from './Pages/Home.vue';

app.component('WelcomePage', WelcomePage);
app.component('Home', Home);

app.use(i18n);

app.mount('#app');
