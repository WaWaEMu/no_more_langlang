<template>
    <div class="reset__container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 reset__card">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold reset__title">{{ $t('Reset Password') }}</h2>
                <p class="text-center text-secondary mb-4">{{ $t('Resetting password for', { email: form.email }) }}</p>

                <form @submit.prevent="resetPassword()">
                    <div class="mb-3">
                        <label for="new-password" class="form-label text-secondary">{{ $t('New Password') }}</label>
                        <input type="password" id="new-password" v-model="form.password" required
                            class="form-control form-control-lg reset__input" :placeholder="$t('Enter Password')">
                    </div>

                    <div class="mb-4">
                        <label for="confirm-new-password" class="form-label text-secondary">
                            {{ $t('Confirm Password') }}
                        </label>
                        <input type="password" id="confirm-new-password" v-model="form.password_confirmation" required
                            class="form-control form-control-lg reset__input"
                            :placeholder="$t('Confirm Password Placeholder')">
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn reset__btn btn-lg text-white fw-bold" :disabled="loading">
                            {{ loading ? $t('Loading...') : $t('Reset Password') }}
                        </button>
                    </div>

                    <div class="d-flex justify-content-center align-items-center text-sm">
                        <RouterLink to="/auth/login" class="text-decoration-none reset__link">
                            <i class="bi bi-arrow-left me-1"></i> {{ $t('Back to Login') }}
                        </RouterLink>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import { useRouter, RouterLink } from 'vue-router'
import Swal from 'sweetalert2'
import { trans } from 'laravel-vue-i18n'

const router = useRouter()
const loading = ref(false)

const form = reactive({
    email: '',
    token: '',
    password: '',
    password_confirmation: ''
})

onMounted(async () => {
    try {
        const res = await axios.get('/api/password-reset/session')
        form.token = res.data.token
        form.email = res.data.email

        if (!form.token || !form.email) {
            Swal.fire({
                icon: 'error',
                title: trans('Error'),
                text: trans('Invalid or expired reset link'),
            }).then(() => {
                router.push('/auth/forgot')
            })
        }
    } catch (error) {
        console.error('Failed to fetch session', error)
    }
})

async function resetPassword() {
    loading.value = true
    try {
        await axios.get('/sanctum/csrf-cookie')
        await axios.post('/reset-password', form)

        await Swal.fire({
            icon: 'success',
            title: trans('Update Successful'),
            text: trans('Password has been reset successfully'),
            confirmButtonText: trans('Got it')
        }).then(() => {
            router.push('/auth/login')
        })
    }
    catch (error: any) {
        console.error('Reset Password Failed', error)

        // Handle validation errors (422)
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const errors = error.response.data.errors
            // Get the first error message from password or email fields
            const firstError = errors.password?.[0] || errors.email?.[0] || errors.token?.[0]

            if (firstError) {
                Swal.fire({
                    icon: 'error',
                    title: trans('Error'),
                    text: firstError,
                })
                return
            }
        }

        const message = error.response?.data?.message || trans('Reset Password Failed')

        Swal.fire({
            icon: 'error',
            title: trans('Error'),
            text: message,
        })
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.reset__container {
    background-color: var(--color-fog-gray);
}

.reset__card {
    width: 100%;
    max-width: 400px;
    border-radius: 1rem;
}

.reset__title {
    color: var(--color-denim-blue);
}

.reset__link {
    color: var(--color-denim-blue);
    opacity: 0.8;
    transition: opacity 0.2s;
}

.reset__link:hover {
    opacity: 1;
}

.reset__btn {
    background-color: var(--color-denim-blue);
    border: none;
    transition: background-color 0.2s;
}

.reset__btn:hover {
    background-color: var(--color-denim-blue-dark);
}

.reset__btn:disabled {
    background-color: var(--color-denim-blue);
    opacity: 0.7;
    cursor: not-allowed;
}

.reset__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.25);
}
</style>
