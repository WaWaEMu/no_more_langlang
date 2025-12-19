import router from '@/router'
import { isLoggedIn } from '@/utils/authStatus'
import getPageTitle from '@/utils/getPageTitle'

// No redirect whitelist
const whiteList = [
    '/auth/login',
    '/auth/register',
    '/auth/forgot',
    '/auth/reset',
    '/404',
    '/401',
    '/500',
    '/adopt'
]

router.beforeEach(async (to, from, next) => {
    // Set page title
    document.title = getPageTitle(to.meta.title as string | undefined)

    const loggedIn = await isLoggedIn();

    // If logged in and trying to access login page, redirect to home
    if (loggedIn && to.path === '/auth/login') {
        return next({ path: '/adopt' })
    }

    // White list routes are always allowed (for non-logged-in users or other auth routes)
    const isWhitelisted = whiteList.includes(to.path) || /^\/adopt\/\d+$/.test(to.path);
    if (isWhitelisted) {
        return next()
    }

    // For protected routes
    if (loggedIn) {
        return next()
    } else {
        // If not logged in, redirect to login page with redirect query
        if (to.query.token) {
            return next(`/auth/login?redirect=${encodeURIComponent(to.fullPath)}&token=${encodeURIComponent(to.query.token as string)}`)
        }
        return next(`/auth/login?redirect=${encodeURIComponent(to.fullPath)}`)
    }
})
