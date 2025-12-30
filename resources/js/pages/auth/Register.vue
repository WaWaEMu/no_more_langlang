<template>
    <div class="register__container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 register__card">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold register__title">註冊</h2>
                <form @submit.prevent="handleRegister">
                    <div class="mb-3">
                        <label for="register-email" class="form-label text-secondary">電子信箱</label>
                        <input type="text" id="register-email" v-model="form.email" required
                            class="form-control form-control-lg register__input" placeholder="請輸入電子信箱"
                            :class="{ 'is-invalid': errors.email }" @focus="delete errors.email"
                            @input="delete errors.email">
                        <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="register-name" class="form-label text-secondary">用戶暱稱</label>
                        <input type="text" id="register-name" v-model="form.name" required
                            class="form-control form-control-lg register__input" placeholder="請輸入用戶暱稱"
                            :class="{ 'is-invalid': errors.name }" @focus="delete errors.name"
                            @input="delete errors.name">
                        <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="register-password" class="form-label text-secondary">密碼</label>
                        <input type="password" id="register-password" v-model="form.password" required
                            class="form-control form-control-lg register__input" placeholder="請輸入密碼"
                            :class="{ 'is-invalid': errors.password }" @focus="delete errors.password"
                            @input="delete errors.password">
                        <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
                        <div v-else class="form-text register__hint">密碼長度至少需為 8 個字元</div>
                    </div>
                    <div class="mb-4">
                        <label for="confirm-password" class="form-label text-secondary">確認密碼</label>
                        <input type="password" id="confirm-password" v-model="form.password_confirmation" required
                            class="form-control form-control-lg register__input" placeholder="請再次輸入密碼"
                            :class="{ 'is-invalid': errors.password_confirmation }"
                            @focus="delete errors.password_confirmation" @input="delete errors.password_confirmation">
                        <div v-if="errors.password_confirmation" class="invalid-feedback">{{
                            errors.password_confirmation }}</div>
                    </div>
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" :disabled="isLoading" class="btn register__btn btn-lg text-white fw-bold">
                            <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                            註冊
                        </button>
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
import { reactive, ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    email: '',
    name: '',
    password: '',
    password_confirmation: ''
})

const errors = reactive<Record<string, string>>({})
const isLoading = ref(false)

function validateForm(): boolean {
    // Clear previous errors
    Object.keys(errors).forEach(key => delete errors[key])

    let isValid = true

    if (!form.email) {
        errors.email = '請輸入電子信箱'
        isValid = false
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = '請輸入有效的電子信箱格式'
        isValid = false
    }

    if (!form.name) {
        errors.name = '請輸入用戶暱稱'
        isValid = false
    }

    if (!form.password) {
        errors.password = '請輸入密碼'
        isValid = false
    } else if (form.password.length < 8) {
        errors.password = '密碼長度至少需為 8 個字元'
        isValid = false
    }

    if (form.password !== form.password_confirmation) {
        errors.password_confirmation = '兩次輸入的密碼不一致'
        isValid = false
    }

    return isValid
}

async function handleRegister() {
    if (!validateForm()) {
        return
    }

    isLoading.value = true
    try {
        await authStore.register(form)

        // Show success message
        await Swal.fire({
            icon: 'success',
            title: '註冊成功！',
            text: '即將前往登入頁面...',
            timer: 1500,
            showConfirmButton: false
        })

        // Redirect to login page after register successfully
        router.push('/auth/login')
    }
    catch (error: any) {
        console.error('註冊失敗:', error)

        // Handle Laravel validation errors
        if (error.response?.status === 422) {
            const backendErrors = error.response.data.errors
            Object.keys(backendErrors).forEach(key => {
                errors[key] = backendErrors[key][0]
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: '註冊失敗',
                text: error.response?.data?.message || '請檢查您的網路連線或稍後再試',
                confirmButtonColor: '#2c5282'
            })
        }
    } finally {
        isLoading.value = false
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

.register__hint {
    font-size: 0.8rem;
    color: #718096;
    margin-top: 0.25rem;
}
</style>
