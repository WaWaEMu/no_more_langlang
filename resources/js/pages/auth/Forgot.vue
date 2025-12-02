<template>
  <div class="forgot__container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg border-0 forgot__card">
      <div class="card-body p-5">
        <h2 class="text-center mb-4 fw-bold forgot__title">重設密碼</h2>
        <form @submit.prevent="sendResetEmail()">
          <div class="mb-4">
            <label for="forgot-email" class="form-label text-secondary">電子信箱</label>
            <input type="email" id="forgot-email" v-model="email" required
              class="form-control form-control-lg forgot__input" placeholder="請輸入您的電子信箱">
          </div>
          <div class="d-grid gap-2 mb-4">
            <button type="submit" class="btn forgot__btn btn-lg text-white fw-bold">寄送重設密碼信</button>
          </div>
          <div class="d-flex justify-content-center align-items-center text-sm">
            <RouterLink to="/auth/login" class="text-decoration-none forgot__link">
              <i class="bi bi-arrow-left me-1"></i> 返回登入
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

const email = ref('')

async function sendResetEmail() {
  try {
    await axios.get('/sanctum/csrf-cookie')
    await axios.post('/forgot-password', { email: email.value })

    await Swal.fire({
      icon: 'success',
      title: '已寄出重設密碼信',
      text: '請至信箱點擊連結以重設密碼',
      confirmButtonText: '好的'
    })
  }
  catch (error) {
    console.error('寄送失敗', error)
    Swal.fire({
      icon: 'error',
      title: '寄送失敗',
      text: '請確認您的電子信箱是否正確',
    })
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

.forgot__input:focus {
  border-color: var(--color-denim-blue);
  box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.25);
}
</style>
