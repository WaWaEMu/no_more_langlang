import router from '@/router'
import { isLoggedIn } from '@/utils/authStatus'
import getPageTitle from '@/utils/getPageTitle'
import Swal from 'sweetalert2'
import { trans } from 'laravel-vue-i18n'

// No redirect whitelist
const whiteList = [
    '/',
    '/auth/login',
    '/auth/register',
    '/auth/forgot',
    '/auth/reset',
    '/welcome',
    '/bye',
    '/404',
    '/401',
    '/500',
    '/adopt',
    '/about',
    '/terms',
    '/privacy'
]

router.beforeEach(async (to, from, next) => {
    // Set page title
    document.title = getPageTitle(to.meta.title as string | undefined)

    const loggedIn = await isLoggedIn();

    // If logged in and trying to access login page, redirect to home
    if (loggedIn && to.path === '/auth/login') {
        return next({ path: '/adopt' })
    }

    // Check if it's a 404 route (fall-through pathMatch)
    const isNotFound = to.matched.some(record => record.path.includes(':pathMatch'));

    // White list routes are always allowed
    const isWhitelisted = whiteList.includes(to.path) || /^\/adopt\/\d+$/.test(to.path) || isNotFound;
    if (isWhitelisted) {
        return next()
    }

    // For protected routes
    if (loggedIn) {
        return next()
    } else {
        // If not logged in, show a confirmation dialog
        const result = await Swal.fire({
            title: trans('Login Required'),
            text: trans('Login required message'),
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: trans('Go to Login'),
            cancelButtonText: trans('Cancel'),
            confirmButtonColor: '#2c5282',
            cancelButtonColor: '#6c757d',
        })

        if (result.isConfirmed) {
            // If not logged in, redirect to login page with redirect query
            if (to.query.token) {
                return next(`/auth/login?redirect=${encodeURIComponent(to.fullPath)}&token=${encodeURIComponent(to.query.token as string)}`)
            }
            return next(`/auth/login?redirect=${encodeURIComponent(to.fullPath)}`)
        } else {
            // Stay on the current page
            return next(false)
        }
    }
})
