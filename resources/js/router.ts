import { createRouter, createWebHashHistory } from 'vue-router';

const routes = [
    {
        path: '/adopt',
        component: () => import('@/pages/adopt/Index.vue'),
        meta: { title: 'Adopt List' }
    },
    {
        path: '/adopt/new',
        component: () => import('@/pages/adopt/Create.vue'),
        meta: { title: 'New Adoption' }
    },
    {
        path: '/adopt/:id/apply',
        component: () => import('@/pages/adopt/Application.vue'),
        meta: { title: 'Apply for Adoption' }
    },
    {
        path: '/adopt/:id',
        component: () => import('@/pages/adopt/Detail.vue'),
        meta: { title: 'Pet Details' }
    },
    {
        path: '/auth/login',
        component: () => import('@/pages/auth/Login.vue'),
        meta: { title: 'Login' }
    },
    {
        path: '/auth/register',
        component: () => import('@/pages/auth/Register.vue'),
        meta: { title: 'Register' }
    },
    {
        path: '/auth/forgot',
        component: () => import('@/pages/auth/Forgot.vue'),
        meta: { title: 'Forgot Password' }
    },
    {
        path: '/auth/reset',
        component: () => import('@/pages/auth/Reset.vue'),
        meta: { title: 'Reset Password' }
    },
    {
        path: '/welcome',
        component: () => import('@/pages/auth/Welcome.vue'),
        meta: { title: 'Welcome' }
    },
    {
        path: '/bye',
        component: () => import('@/pages/auth/Bye.vue'),
        meta: { title: 'Goodbye' }
    },
    {
        path: '/user/profile/:id',
        component: () => import('@/pages/user/Profile.vue'),
        meta: { title: 'Profile' }
    },
    {
        path: '/user/my-pets',
        component: () => import('@/pages/user/MyPets.vue'),
        meta: { title: 'My Pets' }
    },
    {
        path: '/user/favorites',
        component: () => import('@/pages/user/Favorites.vue'),
        meta: { title: 'Favorites' }
    },
    {
        path: '/user/settings',
        component: () => import('@/pages/user/Settings.vue'),
        meta: { title: 'Settings' }
    }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes
})

export default router
