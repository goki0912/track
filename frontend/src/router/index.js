import { createRouter, createWebHistory } from 'vue-router'
import UserAuth from '../components/UserAuth.vue'
import HomePage from '../components/HomePage.vue' // ホームページ用のコンポーネントを作成します

const routes = [
    { path: '/', component: HomePage },
    { path: '/auth', component: UserAuth }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
