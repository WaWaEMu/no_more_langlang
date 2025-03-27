<template>
    <div class="fw-semibold justify-content-start navbar">
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
</template>

<script setup lang="ts" name="Navbar">
    import { ref } from 'vue'
    import { useRoute, RouterLink } from 'vue-router'

    const route = useRoute();

    const navList = ref<{id: string; label: string; path: string}[]>([
        { id: '00', label: '首頁', path: '/' },
        { id: '01', label: '動物認養', path: '/adopt' },
        { id: '02', label: '免費API', path: '/freeApi' },
        { id: '03', label: '網站理念', path: '/about' }
    ])

    function isActive(path: string) {
        return route.path === path
    }
</script>

<style scoped>
    .navbar {
        color: #fff2cc;
        background: #3d3d5c;
    }

    .navbar__title {
        margin: 0 200px;
    }

    .navbar__isActive {
        color: #ffd966;
        font-size: large;
        border-bottom: 2px solid #fff2cc;
    }

    .navbar__link {
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s ease;
        display: inline-block;

        &:hover {
            color: #ffd966;
            transform: translateY(-5px);
        }
    }
</style>