import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import App from './App.vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        { path: '/', component: () => import('@/pages/Home.vue') },
        { path: '/adopt', component: () => import('@/pages/AdoptIndex.vue') },
        { path: '/adopt/new', component: () => import('@/pages/AdoptCreate.vue') },
    ],
});

const app = createApp(App);
app.use(router);

app.mount('#app');
