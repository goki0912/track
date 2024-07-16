import { createApp } from 'vue'
import App from './App.vue'
import './assets/tailwind.css'
import router from './router'
import axios from "axios"
import { createPinia } from 'pinia';

axios.defaults.baseURL = 'http://localhost/api';
axios.defaults.withCredentials = true; // Cookieを使用する場合

const pinia = createPinia();
const app = createApp(App)
// Cookieからトークンを読み込んで設定
const getCookie = (name) => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
};
const token = getCookie('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

app.use(router)
app.use(pinia)
app.mount('#app')