import './bootstrap';
// 引入 Tailwind CSS
import '../css/app.css';
//引入vue
import app from './components/BlogPost.vue'
import { createApp } from 'vue';



createApp(app).mount('#app');
