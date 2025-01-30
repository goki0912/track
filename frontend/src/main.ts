import { createApp } from "vue";
import App from "./App.vue";
import "./assets/tailwind.css";
import router from "./router";
import axios from "axios";
import { createPinia } from "pinia";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import ToastPlugin from "vue-toast-notification";
import "vue-toast-notification/dist/theme-bootstrap.css";

axios.defaults.baseURL = `${process.env.VUE_APP_API_BASE_URL}`;
axios.defaults.withCredentials = true; // Cookieを使用する場合

const pinia = createPinia();
const app = createApp(App);
// Cookieから特定の値を取得する関数
const getCookie = (name: string) => {
  const value = `; ${document.cookie}`; // 全てのCookieを文字列として取得し、前にセミコロンとスペースを追加
  const parts: string[] = value.split(`; ${name}=`); // 指定された名前のCookieを探すために分割
  if (parts.length === 2) {
    return parts.pop()?.split(";").shift(); // Cookieの値を取得して返す
  }
};
// CSRFトークンを取得して設定
axios.get("/csrf-token").then(response => {
  const csrfToken = response.data.csrf_token; // トークンを取得
  axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken; // トークンをヘッダーに設定
});
// Cookieから'token'という名前の値を取得
const token = getCookie("token");
if (token) {
  axios.defaults.headers.common.Authorization = `Bearer ${token}`; // トークンが存在する場合、'Authorization'ヘッダーに設定
}

window.Pusher = Pusher;

// Laravel EchoのReverb設定
window.Echo = new Echo({
  broadcaster: "reverb",
  key: process.env.VUE_APP_REVERB_APP_KEY, // .envの設定を使う
  wsHost: process.env.VUE_APP_REVERB_HOST || "localhost", // Reverbホスト
  wsPort: process.env.VUE_APP_REVERB_PORT || 6001, // WebSocketポートを指定
  wssPort: process.env.VUE_APP_REVERB_PORT || 6001,
  forceTLS: false,
  disableStats: true,
  enabledTransports: ["ws", "wss"], // WebSocketプロトコルを使用
});

app.use(router);
app.use(pinia);
app.use(ToastPlugin);
app.mount("#app");
