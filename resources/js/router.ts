import { createRouter, createWebHashHistory } from 'vue-router';

const routes = [
    {
        path: '/adopt',
        component: () => import('@/pages/Adopt.vue'),
        meta: { title: 'Adopt List' }
    },
    {
        path: '/adopt/new',
        component: () => import('@/pages/AdoptCreate.vue'),
        meta: { title: 'New Adoption' }
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
        component: () => import('@/pages/auth/ForgotPassword.vue'),
        meta: { title: 'Forgot Password' }
    },
    {
        path: '/auth/reset',
        component: () => import('@/pages/auth/ResetPassword.vue'),
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
    }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes
})

export default router
