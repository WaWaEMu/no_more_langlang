<template>
    <div class="d-flex flex-column justify-content-center align-items-center vh-100 social-callback">
        <div class="spinner-border social-callback__spinner mb-4" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="social-callback__text">
            {{ $t('Login Processing') }}
        </div>
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

<style scoped>
.social-callback {
    background-color: var(--color-fog-gray);
}

.social-callback__spinner {
    width: 3.5rem;
    height: 3.5rem;
    border-width: 0.35rem;
    color: var(--color-denim-blue);
}

.social-callback__text {
    font-size: 1.75rem;
    font-weight: 950;
    color: var(--color-denim-blue-dark);
    letter-spacing: -0.01em;
    animation: pulse 2s infinite ease-in-out;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.7;
        transform: scale(0.98);
    }
}
</style>
