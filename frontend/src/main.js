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
    const value = `; ${document.cookie}`;  // 全てのCookieを文字列として取得し、前にセミコロンとスペースを追加
    const parts = value.split(`; ${name}=`);  // 指定された名前のCookieを探すために分割
    if (parts.length === 2) {
        return parts.pop().split(';').shift();  // Cookieの値を取得して返す
    }
};
// Cookieから'token'という名前の値を取得
const token = getCookie('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;  // トークンが存在する場合、'Authorization'ヘッダーに設定
}

app.use(router)
app.use(pinia)
app.mount('#app')