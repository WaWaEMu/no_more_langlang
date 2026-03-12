<template>
    <div class="fw-semibold justify-content-between navbar__wrap pe-4 d-flex align-items-center">
        <div class="d-flex align-items-center navbar__container">
            <RouterLink to="/" class="navbar__brand" aria-label="Home">
                {{ $t('App Name') }}
            </RouterLink>

            <!-- Mobile Toggle Button -->
            <button class="navbar__toggler d-md-none" @click="toggleMenu" aria-label="Toggle navigation">
                <i class="bi" :class="isMenuOpen ? 'bi-x-lg' : 'bi-list'"></i>
            </button>

            <!-- Navigation Menu -->
            <ul class="d-flex list-unstyled align-items-center mb-0 navbar__menu"
                :class="{ 'navbar__menu--open': isMenuOpen }">
                <li v-for="navItem in navList" :key="navItem.id" class="me-5 navbar__item">
                    <RouterLink :to="navItem.path"
                        active-class="text-decoration-none d-inline-block pb-2 navbar__isActive"
                        :class="!isActive(navItem.path) && 'navbar__link'" @click.prevent="handleNavClick(navItem)">
                        {{ $t(navItem.label) }}
                    </RouterLink>
                </li>
            </ul>
        </div>

        <!-- Notification & User Menu -->
        <div class="d-flex align-items-center gap-3">
            <NotificationDropdown v-if="authStore.isAuthenticated" />
            <UserMenu />
        </div>
    </div>
</template>

<script setup lang="ts" name="Navbar">
import { ref } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import UserMenu from '@/components/user/UserMenu.vue'
import NotificationDropdown from '@/components/user/NotificationDropdown.vue'
import { useAuthStore } from '@/stores/auth'
import { trans } from 'laravel-vue-i18n'

const authStore = useAuthStore()
const route = useRoute();
const router = useRouter();
const isMenuOpen = ref(false);

const navList = ref<{ id: string; label: string; path: string }[]>([
    { id: '10', label: 'landing.Home', path: '/' },
    { id: '00', label: 'Adopt', path: '/adopt' },
    { id: '01', label: 'New Adoption', path: '/adopt/new' },
    { id: '02', label: 'Create Case', path: '/case/new' },
    { id: '03', label: 'Website Concept', path: '/about' }
])

function isActive(path: string) {
    return route.path === path
}

function toggleMenu() {
    isMenuOpen.value = !isMenuOpen.value;
}

function closeMenu() {
    isMenuOpen.value = false;
}

async function handleNavClick(navItem: any) {
    closeMenu()
    const authRequiredPaths = ['/adopt/new', '/case/new']
    if (authRequiredPaths.includes(navItem.path)) {
        if (await authStore.checkAuth(trans('Login to post adoption'))) {
            router.push(navItem.path)
        }
    } else {
        router.push(navItem.path)
    }
}
</script>

<style scoped>
.navbar__wrap {
    background: linear-gradient(135deg, #d1d9e6 0%, #b8c5d6 100%);
    color: #4a5568;
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.25), 0 2px 4px rgba(107, 114, 128, 0.15);
    position: relative;
    z-index: 1000;
}

.navbar__brand {
    text-decoration: none;
    margin: 0 200px;
    font-size: 2.25rem;
    font-weight: 950;
    color: var(--color-denim-blue-dark);
    letter-spacing: -0.01em;
    padding: 1.75rem 0;
    line-height: 1.1;
    position: relative;
    display: inline-flex;
    align-items: center;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}

.navbar__brand::before {
    content: '';
    position: absolute;
    left: -8px;
    right: -8px;
    top: 65%;
    transform: translateY(-50%) scaleX(0);
    height: 20%;
    background-color: var(--color-denim-blue);
    opacity: 0.15;
    z-index: -1;
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}

.navbar__brand:hover {
    color: var(--color-denim-blue);
    transform: translateY(-2px);
}

.navbar__brand:hover::before {
    transform: translateY(-50%) scaleX(1);
    transform-origin: left;
}

.navbar__isActive {
    color: #2c5282;
    font-size: large;
    border-bottom: 2px solid #3182ce;
    font-weight: 600;
}

.navbar__link {
    text-decoration: none;
    color: #4a5568;
    transition: all 0.2s ease;
    display: inline-block;

    &:hover {
        color: #3182ce;
        transform: translateY(-5px);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .navbar__wrap {
        padding: 0 1rem;
    }

    .navbar__brand {
        margin: 0 1rem 0 0;
        font-size: 1.5rem;
    }

    .navbar__toggler {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #2c5282;
        padding: 0.5rem;
        cursor: pointer;
        margin-left: auto;
        /* Ensure it's visible */
        display: block;
    }

    .navbar__menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: linear-gradient(135deg, #d1d9e6 0%, #b8c5d6 100%);
        flex-direction: column;
        align-items: flex-start !important;
        padding: 1rem 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);

        /* Hidden by default */
        display: none !important;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .navbar__menu--open {
        display: flex !important;
        opacity: 1;
        transform: translateY(0);
    }

    .navbar__item {
        margin: 0 !important;
        width: 100%;
    }

    .navbar__link,
    .navbar__isActive {
        display: block;
        padding: 0.75rem 2rem;
        width: 100%;
        border-bottom: none;
        /* Remove border for active state in mobile if preferred, or keep it */
    }

    .navbar__isActive {
        background: rgba(255, 255, 255, 0.2);
    }

    .navbar__link:hover {
        transform: none;
        background: rgba(255, 255, 255, 0.1);
        padding-left: 2.5rem;
        /* Indent on hover */
    }
}
</style>