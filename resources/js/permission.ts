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
    '/500'
]

router.beforeEach(async (to, from, next) => {
    // Set page title
    document.title = getPageTitle(to.meta.title)

    // White list routes are always allowed
    if (whiteList.includes(to.path)) {
        return next()
    }

    const loggedIn = await isLoggedIn();

    if (loggedIn) {
        if (to.path === '/auth/login') {
            // If is logged in, redirect to the home page
            return next({ path: '/' })
        } else {
            return next()
        }
    } else {
        // If not logged in, redirect to login page with redirect query
        if (to.query.token) {
            return next(`/auth/login?redirect=${encodeURIComponent(to.fullPath)}&token=${encodeURIComponent(to.query.token as string)}`)
        }
        return next(`/auth/login?redirect=${encodeURIComponent(to.fullPath)}`)
    }
})
