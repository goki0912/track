import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from "axios";

axios.defaults.baseURL = 'http://localhost/api';
axios.defaults.withCredentials = true; // Cookieを使用する場合


const app = createApp(App)

app.use(router)

app.mount('#app')
