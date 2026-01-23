<template>
  <div class="forgot__container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg border-0 forgot__card">
      <div class="card-body p-5">
        <h2 class="text-center mb-4 fw-bold forgot__title">{{ $t('Reset Password') }}</h2>
        <form @submit.prevent="sendResetEmail()">
          <div class="mb-4">
            <label for="forgot-email" class="form-label text-secondary">{{ $t('Email') }}</label>
            <input type="email" id="forgot-email" v-model="email" required
              class="form-control form-control-lg forgot__input" :placeholder="$t('Enter Email')">
          </div>
          <div class="d-grid gap-2 mb-4">
            <button type="submit" class="btn forgot__btn btn-lg text-white fw-bold" :disabled="loading">
              {{ loading ? $t('Sending...') : $t('Send Reset Password Email') }}
            </button>
          </div>
          <div class="d-flex justify-content-center align-items-center text-sm">
            <RouterLink to="/auth/login" class="text-decoration-none forgot__link">
              <i class="bi bi-arrow-left me-1"></i> {{ $t('Back to Login') }}
            </RouterLink>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import { trans } from 'laravel-vue-i18n'

const email = ref('')
const loading = ref(false)

async function sendResetEmail() {
  loading.value = true
  try {
    await axios.get('/sanctum/csrf-cookie')
    await axios.post('/forgot-password', { email: email.value })

    await Swal.fire({
      icon: 'success',
      title: trans('Reset Password Mail Sent'),
      text: trans('Check email to reset password', { email: email.value }),
      confirmButtonText: trans('Got it')
    })
  }
  catch (error: any) {
    console.error('Send Failed', error)

    // Check if it's a validation error (422) with specific field errors
    if (error.response?.status === 422 && error.response?.data?.errors?.email) {
      const emailError = error.response.data.errors.email[0]
      Swal.fire({
        icon: 'error',
        title: trans('Send Failed'),
        text: emailError,
      })
    } else {
      // Log the detailed error for developers to see in the console
      console.error('Server Error:', error.response?.data || error.message);

      Swal.fire({
        icon: 'error',
        title: trans('Send Failed'),
        text: trans('Please try again later'),
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.forgot__container {
  background-color: var(--color-fog-gray);
}

.forgot__card {
  width: 100%;
  max-width: 400px;
  border-radius: 1rem;
}

.forgot__title {
  color: var(--color-denim-blue);
}

.forgot__link {
  color: var(--color-denim-blue);
  opacity: 0.8;
  transition: opacity 0.2s;
}

.forgot__link:hover {
  opacity: 1;
}

.forgot__btn {
  background-color: var(--color-denim-blue);
  border: none;
  transition: background-color 0.2s;
}

.forgot__btn:hover {
  background-color: var(--color-denim-blue-dark);
}

.forgot__btn:disabled {
  background-color: var(--color-denim-blue);
  opacity: 0.7;
  cursor: not-allowed;
}

.forgot__input:focus {
  border-color: var(--color-denim-blue);
  box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.25);
}
</style>
