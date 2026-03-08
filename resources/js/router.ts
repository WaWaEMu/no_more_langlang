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
        meta: { title: t('Adopt') }
    },
    {
        path: '/adopt/new',
        component: () => import('@/pages/adopt/Create.vue'),
        meta: { title: t('New Adoption') }
    },
    {
        path: '/adopt/:id(\\d+)/apply',
        component: () => import('@/pages/adopt/Application.vue'),
        meta: { title: t('Apply Adoption') }
    },
    {
        path: '/adopt/:id(\\d+)',
        component: () => import('@/pages/adopt/Detail.vue'),
        meta: { title: t('Pet Details') }
    },
    {
        path: '/auth/login',
        component: () => import('@/pages/auth/Login.vue'),
        meta: { title: t('Login') }
    },
    {
        path: '/auth/register',
        component: () => import('@/pages/auth/Register.vue'),
        meta: { title: t('Register') }
    },
    {
        path: '/auth/callback',
        component: () => import('@/pages/auth/SocialCallback.vue'),
        meta: { title: 'Processing Login...' }
    },
    {
        path: '/auth/forgot',
        component: () => import('@/pages/auth/Forgot.vue'),
        meta: { title: t('Forgot Password') }
    },
    {
        path: '/auth/reset',
        component: () => import('@/pages/auth/Reset.vue'),
        meta: { title: t('Reset Password') }
    },
    {
        path: '/welcome',
        component: () => import('@/pages/auth/Welcome.vue'),
        meta: { title: t('Welcome') }
    },
    {
        path: '/bye',
        component: () => import('@/pages/auth/Bye.vue'),
        meta: { title: t('Goodbye') }
    },
    {
        path: '/user/profile/:id',
        component: () => import('@/pages/user/Profile.vue'),
        meta: { title: t('Profile') }
    },
    {
        path: '/user/adoptions',
        component: () => import('@/pages/user/Adoptions.vue'),
        meta: { title: t('Adoption Management') }
    },
    {
        path: '/user/adopted',
        component: () => import('@/pages/user/Adopted.vue'),
        meta: { title: t('Adopt Management') }
    },
    {
        path: '/user/favorites',
        component: () => import('@/pages/user/Favorites.vue'),
        meta: { title: t('Favorites') }
    },
    {
        path: '/user/settings',
        component: () => import('@/pages/user/Settings.vue'),
        meta: { title: t('Settings') }
    },
    {
        path: '/user/applications',
        component: () => import('@/pages/user/Applications.vue'),
        meta: { title: t('Applications') }
    },
    {
        path: '/user/adoption-templates',
        component: () => import('@/pages/user/AdoptionTemplateManager.vue'),
        meta: { title: t('Adoption Templates') }
    },
    {
        path: '/about',
        component: () => import('@/pages/About.vue'),
        meta: { title: t('Website Concept') }
    },
    {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/NotFound.vue'),
        meta: { title: t('Page Not Found') }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
