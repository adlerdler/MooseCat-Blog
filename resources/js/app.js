import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';

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

app.mount('#app');