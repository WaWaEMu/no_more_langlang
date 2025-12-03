<template>
    <div class="fw-semibold justify-content-between navbar pe-4">
        <div class="d-flex align-items-center">
            <h1 class="my-4 navbar__title">諾摸浪浪</h1>
            <ul class="d-flex list-unstyled align-items-center mb-0">
                <li v-for="navItem in navList" :key="navItem.id" class="me-5">
                    <RouterLink :to="navItem.path"
                        active-class="text-decoration-none d-inline-block pb-2 navbar__isActive"
                        :class="!isActive(navItem.path) && 'navbar__link'">
                        {{ navItem.label }}
                    </RouterLink>
                </li>
            </ul>
        </div>

        <!-- User Menu -->
        <UserMenu />
    </div>
</template>

<script setup lang="ts" name="Navbar">
import { ref } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import UserMenu from '@/components/user/UserMenu.vue'

const route = useRoute();

const navList = ref<{ id: string; label: string; path: string }[]>([
    { id: '00', label: '動物認養', path: '/adopt' },
    { id: '01', label: '登記送養', path: '/adopt/new' },
    { id: '02', label: '免費API', path: '/freeApi' },
    { id: '03', label: '網站理念', path: '/about' }
])

function isActive(path: string) {
    return route.path === path
}
</script>

<style scoped>
.navbar {
    background: linear-gradient(135deg, #d1d9e6 0%, #b8c5d6 100%);
    color: #4a5568;
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.25), 0 2px 4px rgba(107, 114, 128, 0.15);
}

.navbar__title {
    margin: 0 200px;
    color: #2c5282;
    font-weight: 700;
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
    .navbar {
        padding: 0 1rem;
    }

    .navbar__title {
        margin: 0 50px;
        font-size: 1.25rem;
    }
}
</style>