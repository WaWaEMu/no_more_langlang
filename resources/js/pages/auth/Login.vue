<template>
    <div class="login__container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 login__card">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold login__title">{{ $t('Login Title') }}</h2>
                <form @submit.prevent="handleLogin">
                    <div class="mb-3">
                        <label for="login-email" class="form-label text-secondary">{{ $t('Email') }}</label>
                        <input type="text" id="login-email" v-model.trim="form.email" :disabled="isLoading"
                            class="form-control form-control-lg login__input"
                            :placeholder="$t('Enter Email')" :class="{ 'is-invalid': errors.email }"
                            @focus="delete errors.email" @input="delete errors.email">
                        <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                    </div>
                    <div class="mb-4">
                        <label for="login-password" class="form-label text-secondary">{{ $t('Password')
                            }}</label>
                        <input type="password" id="login-password" v-model="form.password" :disabled="isLoading"
                            class="form-control form-control-lg login__input"
                            :placeholder="$t('Enter Password')" :class="{ 'is-invalid': errors.password }"
                            @focus="delete errors.password" @input="delete errors.password">
                        <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
                    </div>
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn login__btn btn-lg text-white fw-bold" :disabled="isLoading">
                            <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                            {{ isLoading ? $t('Sending...') : $t('Login Title') }}
                        </button>
                        <a href="/auth/google/redirect"
                            class="btn btn-lg btn-outline-secondary fw-bold d-flex align-items-center justify-content-center"
                            :class="{ 'disabled': isLoading }">
                            <i class="bi bi-google me-2"></i> {{ $t('Login with Google') }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-sm mb-4">
                        <RouterLink to="/auth/register" class="text-decoration-none login__link">
                            {{ $t('Register New Account') }}
                        </RouterLink>
                        <RouterLink to="/auth/forgot" class="text-decoration-none text-secondary">
                            {{ $t('Forgot Password?') }}
                        </RouterLink>
                    </div>

                    <div class="text-center pt-3 border-top">
                        <RouterLink to="/adopt" class="text-decoration-none login__home-link">
                            <i class="bi bi-house-door-fill me-2"></i>{{ $t('Back to Home') }}
                        </RouterLink>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import axios, { AxiosError } from 'axios'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = reactive({
    email: '',
    password: ''
})

const errors = reactive<Record<string, string>>({})
const isLoading = ref(false)

import { onMounted } from 'vue'
import { trans } from 'laravel-vue-i18n'

onMounted(() => {
    const error = route.query.error as string
    if (error) {
        Swal.fire({
            icon: 'error',
            title: '登入失敗',
            text: trans(error),
        })
        // Clear the error from the URL
        router.replace({ query: { ...route.query, error: undefined } })
    }
})

// Validate form
function validateForm(): boolean {
    // Clear previous errors
    Object.keys(errors).forEach(key => delete errors[key])

    let isValid = true

    // Validate email
    if (!form.email) {
        errors.email = trans('The :attribute field is required.', { attribute: trans('Email') })
        isValid = false
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = trans('The :attribute must be a valid email address.', { attribute: trans('Email') })
        isValid = false
    }

    // Validate password
    if (!form.password) {
        errors.password = trans('The :attribute field is required.', { attribute: trans('Password') })
        isValid = false
    } else if (form.password.length < 8) {
        errors.password = trans('The :attribute must be at least :min characters.', { attribute: trans('Password'), min: '8' })
        isValid = false
    }

    return isValid
}

async function handleLogin() {
    // Validate form first
    if (!validateForm()) {
        return
    }

    isLoading.value = true

    try {
        await authStore.login(form)

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

        // Handle error messages
        let errorMessage = trans('These credentials do not match our records.')

        if (axios.isAxiosError(error)) {
            const axiosError = error as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
            const backendMessage = axiosError.response?.data?.message || ''

            if (axiosError.response?.status === 422) {
                const validationErrors = axiosError.response.data.errors
                if (validationErrors) {
                    const firstError = Object.values(validationErrors)[0]
                    const errorMsg = firstError ? firstError[0] : trans('Error')
                    // Translate specific backend error
                    if (errorMsg === 'These credentials do not match our records.') {
                        errorMessage = trans('These credentials do not match our records.')
                    } else {
                        errorMessage = trans(errorMsg)
                    }
                }
            } else if (axiosError.response?.status === 419) {
                errorMessage = trans('Please try again later')
            } else if (backendMessage) {
                errorMessage = trans(backendMessage)
            }
        }

        Swal.fire({
            icon: 'error',
            title: trans('Login Title') + trans('Delete failed').replace('刪除', ''), // Hacky way to get "失敗"
            text: errorMessage,
        })
    }
    finally {
        isLoading.value = false
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

.login__btn:disabled {
    background-color: var(--color-denim-blue);
    opacity: 0.7;
    cursor: not-allowed;
}

.login__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.25);
}

.login__home-link {
    color: var(--color-denim-blue);
    font-weight: 600;
    opacity: 0.7;
    transition: all 0.2s ease;

    &:hover {
        opacity: 1;
        color: var(--color-denim-blue-dark);
    }
}

@media (max-width: 768px) {
    .login__container {
        padding: 1rem;
    }

    .login__card {
        max-width: 100%;
    }

    .card-body {
        padding: 2rem !important;
    }
}
</style>
