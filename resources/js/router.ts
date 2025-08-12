import { createRouter, createWebHashHistory } from 'vue-router';

const routes = [
    { path: '/', component: () => import('@/pages/Home.vue') },
    { path: '/adopt', component: () => import('@/pages/AdoptIndex.vue') },
    { path: '/adopt/new', component: () => import('@/pages/AdoptCreate.vue') },
    { path: '/auth/login', component: () => import('@/pages/auth/Login.vue') },
    { path: '/auth/register', component: () => import('@/pages/auth/Register.vue') },
    { path: '/auth/forgot', component: () => import('@/pages/auth/ForgotPassword.vue') },
    { path: '/auth/reset', component: () => import('@/pages/auth/ResetPassword.vue') },
    { path: '/welcome', component: () => import('@/pages/auth/Welcome.vue') },
    { path: '/bye', component: () => import('@/pages/auth/Bye.vue') }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes
})

export default router
