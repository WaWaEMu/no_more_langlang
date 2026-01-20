<template>
    <div class="register__container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 register__card">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold register__title">{{ $t('Register Title') }}</h2>
                <form @submit.prevent="handleRegister">
                    <div class="mb-3">
                        <label for="register-email" class="form-label text-secondary">{{ $t('Email')
                        }}</label>
                        <input type="text" id="register-email" v-model="form.email" required
                            class="form-control form-control-lg register__input"
                            :placeholder="$t('Enter Email')" :class="{ 'is-invalid': errors.email }"
                            @focus="delete errors.email" @input="delete errors.email">
                        <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="register-name" class="form-label text-secondary">{{ $t('Nickname') }}</label>
                        <input type="text" id="register-name" v-model="form.name" required
                            class="form-control form-control-lg register__input"
                            :placeholder="$t('auth.name_placeholder')" :class="{ 'is-invalid': errors.name }"
                            @focus="delete errors.name" @input="delete errors.name">
                        <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="register-password" class="form-label text-secondary">{{ $t('Password')
                        }}</label>
                        <input type="password" id="register-password" v-model="form.password" required
                            class="form-control form-control-lg register__input"
                            :placeholder="$t('Enter Password')" :class="{ 'is-invalid': errors.password }"
                            @focus="delete errors.password" @input="delete errors.password">
                        <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
                        <div v-else class="form-text register__hint">{{ $t('The :attribute must be at least :min characters.', {
                            attribute:
                                $t('Password'), min: '8'
                        }) }}</div>
                    </div>
                    <div class="mb-4">
                        <label for="confirm-password" class="form-label text-secondary">{{
                            $t('Confirm Password') }}</label>
                        <input type="password" id="confirm-password" v-model="form.password_confirmation" required
                            class="form-control form-control-lg register__input"
                            :placeholder="$t('auth.confirm_password_placeholder')"
                            :class="{ 'is-invalid': errors.password_confirmation }"
                            @focus="delete errors.password_confirmation" @input="delete errors.password_confirmation">
                        <div v-if="errors.password_confirmation" class="invalid-feedback">{{
                            errors.password_confirmation }}</div>
                    </div>
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" :disabled="isLoading" class="btn register__btn btn-lg text-white fw-bold">
                            <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                            {{ $t('Register Title') }}
                        </button>
                        <a href="/auth/google/redirect"
                            class="btn btn-lg btn-outline-secondary fw-bold d-flex align-items-center justify-content-center"
                            :class="{ 'disabled': isLoading }">
                            <i class="bi bi-google me-2"></i> {{ $t('Login with Google').replace('登入', '註冊') }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-center align-items-center text-sm mb-4">
                        <span class="text-secondary me-2">{{ $t('Already have an account?') }}</span>
                        <RouterLink to="/auth/login" class="text-decoration-none register__link">
                            {{ $t('Login Title') }}
                        </RouterLink>
                    </div>

                    <div class="text-center pt-3 border-top">
                        <RouterLink to="/adopt" class="text-decoration-none register__home-link">
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
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'
import { trans } from 'laravel-vue-i18n'

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
        errors.email = trans('The :attribute field is required.', { attribute: trans('Email') })
        isValid = false
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = trans('The :attribute must be a valid email address.', { attribute: trans('Email') })
        isValid = false
    }

    if (!form.name) {
        errors.name = trans('The :attribute field is required.', { attribute: trans('Nickname') })
        isValid = false
    }

    if (!form.password) {
        errors.password = trans('The :attribute field is required.', { attribute: trans('Password') })
        isValid = false
    } else if (form.password.length < 8) {
        errors.password = trans('The :attribute must be at least :min characters.', { attribute: trans('Password'), min: '8' })
        isValid = false
    }

    if (form.password !== form.password_confirmation) {
        errors.password_confirmation = trans('The :attribute confirmation does not match.', { attribute: trans('Password') })
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
            title: trans('Registration Successful!'),
            text: trans('Redirecting to login page...'),
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
                errors[key] = trans(backendErrors[key][0])
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: trans('Register Title') + trans('Delete failed').replace('刪除', ''),
                text: error.response?.data?.message ? trans(error.response.data.message) : trans('Please try again later'),
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

.register__home-link {
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
    .register__container {
        padding: 1rem;
    }

    .register__card {
        max-width: 100%;
    }

    .card-body {
        padding: 2rem !important;
    }
}
</style>
