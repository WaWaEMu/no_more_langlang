<template>
    <div class="register__container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 register__card">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold register__title">註冊</h2>
                <form @submit.prevent="register()">
                    <div class="mb-3">
                        <label for="register-email" class="form-label text-secondary">電子信箱</label>
                        <input type="text" id="register-email" v-model="form.email" required
                            class="form-control form-control-lg register__input" placeholder="請輸入電子信箱">
                    </div>
                    <div class="mb-3">
                        <label for="register-name" class="form-label text-secondary">用戶暱稱</label>
                        <input type="text" id="register-name" v-model="form.name" required
                            class="form-control form-control-lg register__input" placeholder="請輸入用戶暱稱">
                    </div>
                    <div class="mb-3">
                        <label for="register-password" class="form-label text-secondary">密碼</label>
                        <input type="password" id="register-password" v-model="form.password" required
                            class="form-control form-control-lg register__input" placeholder="請輸入密碼">
                    </div>
                    <div class="mb-4">
                        <label for="confirm-password" class="form-label text-secondary">確認密碼</label>
                        <input type="password" id="confirm-password" v-model="form.password_confirmation" required
                            class="form-control form-control-lg register__input" placeholder="請再次輸入密碼">
                    </div>
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn register__btn btn-lg text-white fw-bold">註冊</button>
                    </div>
                    <div class="d-flex justify-content-center align-items-center text-sm">
                        <span class="text-secondary me-2">已經有帳號了嗎？</span>
                        <RouterLink to="/auth/login" class="text-decoration-none register__link">
                            登入
                        </RouterLink>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const form = reactive({
    email: '',
    name: '',
    password: '',
    password_confirmation: ''
})

async function register() {
    try {
        await axios.get('/sanctum/csrf-cookie')
        await axios.post('/register', form)
        // Redirect to login page after register successfully
        router.push('/auth/login')
    }
    catch (error) {
        console.error('註冊失敗:', error)
    }
}
</script>

<style scoped>
.register__container {
    background-color: var(--color-fog-gray);
}

.register__card {
    width: 100%;
    max-width: 400px;
    border-radius: 1rem;
}

.register__title {
    color: var(--color-denim-blue);
}

.register__link {
    color: var(--color-denim-blue);
    opacity: 0.8;
    transition: opacity 0.2s;
}

.register__link:hover {
    opacity: 1;
}

.register__btn {
    background-color: var(--color-denim-blue);
    border: none;
    transition: background-color 0.2s;
}

.register__btn:hover {
    background-color: var(--color-denim-blue-dark);
}

.register__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.25);
}
</style>
