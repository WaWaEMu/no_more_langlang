import { createRouter, createWebHistory } from 'vue-router';
import { t } from '@/lang'

const routes = [
    {
        path: '/',
        redirect: '/adopt'
    },
    {
        path: '/adopt',
        component: () => import('@/pages/adopt/Index.vue'),
        meta: { title: t('router.adopt') }
    },
    {
        path: '/adopt/new',
        component: () => import('@/pages/adopt/Create.vue'),
        meta: { title: t('router.new_adoption') }
    },
    {
        path: '/adopt/:id/apply',
        component: () => import('@/pages/adopt/Application.vue'),
        meta: { title: t('router.apply_adoption') }
    },
    {
        path: '/adopt/:id',
        component: () => import('@/pages/adopt/Detail.vue'),
        meta: { title: t('router.pet_details') }
    },
    {
        path: '/auth/login',
        component: () => import('@/pages/auth/Login.vue'),
        meta: { title: t('router.login') }
    },
    {
        path: '/auth/register',
        component: () => import('@/pages/auth/Register.vue'),
        meta: { title: t('router.register') }
    },
    {
        path: '/auth/callback',
        component: () => import('@/pages/auth/SocialCallback.vue'),
        meta: { title: 'Processing Login...' }
    },
    {
        path: '/auth/forgot',
        component: () => import('@/pages/auth/Forgot.vue'),
        meta: { title: t('router.forgot_password') }
    },
    {
        path: '/auth/reset',
        component: () => import('@/pages/auth/Reset.vue'),
        meta: { title: t('router.reset_password') }
    },
    {
        path: '/welcome',
        component: () => import('@/pages/auth/Welcome.vue'),
        meta: { title: t('router.welcome') }
    },
    {
        path: '/bye',
        component: () => import('@/pages/auth/Bye.vue'),
        meta: { title: t('router.goodbye') }
    },
    {
        path: '/user/profile/:id',
        component: () => import('@/pages/user/Profile.vue'),
        meta: { title: t('router.profile') }
    },
    {
        path: '/user/my-pets',
        component: () => import('@/pages/user/MyPets.vue'),
        meta: { title: t('router.my_pets') }
    },
    {
        path: '/user/favorites',
        component: () => import('@/pages/user/Favorites.vue'),
        meta: { title: t('router.favorites') }
    },
    {
        path: '/user/settings',
        component: () => import('@/pages/user/Settings.vue'),
        meta: { title: t('router.settings') }
    },
    {
        path: '/user/applications',
        component: () => import('@/pages/user/Applications.vue'),
        meta: { title: t('router.applications') }
    },
    {
        path: '/about',
        component: () => import('@/pages/About.vue'),
        meta: { title: t('router.about') }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
