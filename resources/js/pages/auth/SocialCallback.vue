<template>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="ms-3">Processing login...</div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

onMounted(async () => {
    const token = route.query.token;

    if (token) {
        localStorage.setItem('auth_token', token);
        // We need to fetch the user to populate the store
        try {
            await authStore.fetchUser();
            router.push({
                path: '/welcome',
                query: { token }
            });
        } catch (error) {
            console.error('Failed to fetch user:', error);
            router.push('/login?error=Login failed');
        }
    } else {
        router.push('/login?error=No token received');
    }
});
</script>
