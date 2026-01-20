<template>
    <div class="welcome__container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 welcome__card">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h2 class="mb-3 fw-bold welcome__title">{{ $t('Login Success!') }}</h2>
                <p class="text-secondary mb-4">{{ $t('Welcome back preparing') }}</p>

                <div class="alert alert-light border mb-4">
                    {{ $t('Will redirect in') }} <span class="fw-bold text-primary">{{ countdown }}</span> {{ $t('seconds auto redirect') }}
                </div>

                <div class="d-grid gap-2">
                    <button @click="jumpNow()" class="btn welcome__btn btn-lg text-white fw-bold">
                        {{ $t('Click to redirect') }} <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const countdown = ref(3)
let timer: number | undefined

onMounted(() => {
    timer = setInterval(() => {
        countdown.value--
        if (countdown.value <= 0) {
            jumpNow()
        }
    }, 1000)
})

onUnmounted(() => {
    if (timer) clearInterval(timer)
})

function jumpNow() {
    if (timer) clearInterval(timer)

    // Check if there's a redirect query parameter
    const redirect = route.query.redirect as string | undefined

    if (redirect) {
        // Redirect to the original destination
        router.push(redirect)
    } else {
        // Default to /adopt
        router.push('/adopt')
    }
}
</script>

<style scoped>
.welcome__container {
    background-color: var(--color-fog-gray);
}

.welcome__card {
    width: 100%;
    max-width: 400px;
    border-radius: 1rem;
}

.welcome__title {
    color: var(--color-denim-blue);
}

.welcome__btn {
    background-color: var(--color-denim-blue);
    border: none;
    transition: background-color 0.2s;
}

.welcome__btn:hover {
    background-color: var(--color-denim-blue-dark);
}
</style>
