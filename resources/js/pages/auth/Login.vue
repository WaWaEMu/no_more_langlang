<template>
    <div class="login__container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 login__card">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold login__title">登入</h2>
                <form @submit.prevent="login()">
                    <div class="mb-3">
                        <label for="login-email" class="form-label text-secondary">帳號(Email)</label>
                        <input type="text" id="login-email" v-model="form.email"
                            class="form-control form-control-lg login__input" placeholder="請輸入帳號">
                    </div>
                    <div class="mb-4">
                        <label for="login-password" class="form-label text-secondary">密碼</label>
                        <input type="password" id="login-password" v-model="form.password"
                            class="form-control form-control-lg login__input" placeholder="請輸入密碼">
                    </div>
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn login__btn btn-lg text-white fw-bold">登入</button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-sm">
                        <RouterLink to="/auth/register" class="text-decoration-none login__link">
                            註冊新帳號
                        </RouterLink>
                        <RouterLink to="/auth/forgot" class="text-decoration-none text-secondary">
                            忘記密碼
                        </RouterLink>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()

const form = reactive({
    email: '',
    password: ''
})

async function login() {
    try {
        await axios.get('/sanctum/csrf-cookie')
        await axios.post('/login', form)

        // Pass redirect and token query params to Welcome page
        const redirect = route.query.redirect as string | undefined
        const token = route.query.token as string | undefined

        router.push({
            path: '/welcome',
            query: {
                ...(redirect && { redirect }),
                ...(token && { token })
            }
        })
    }
    catch (error) {
        console.error('登入失敗:', error)
    }
}
</script>

<style scoped>
.login__container {
    background-color: var(--color-fog-gray);
}

.login__card {
    width: 100%;
    max-width: 400px;
    border-radius: 1rem;
}

.login__title {
    color: var(--color-denim-blue);
}

.login__link {
    color: var(--color-denim-blue);
    opacity: 0.8;
    transition: opacity 0.2s;
}

.login__link:hover {
    opacity: 1;
}

.login__btn {
    background-color: var(--color-denim-blue);
    border: none;
    transition: background-color 0.2s;
}

.login__btn:hover {
    background-color: var(--color-denim-blue-dark);
}

.login__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.25);
}
</style>
