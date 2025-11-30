import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import App from './App.vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap';
import router from '@/router'
import './permission';
import axios from 'axios';
import { createPinia } from 'pinia'

axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://localhost:8000';

const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);

app.mount('#app');
