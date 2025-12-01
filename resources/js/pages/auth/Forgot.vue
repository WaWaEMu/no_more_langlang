<template>
  <div>
    <h2>請輸入您的電子信箱</h2>
    <input type="email" v-model="email" />
    <button @click="sendResetEmail()">寄送重設密碼信</button>
  </div>
</template>

<script setup lang="ts">
    import { ref } from 'vue'
    import axios from 'axios'

    const email = ref('')

    async function sendResetEmail() {
        try {
            await axios.post('/forgot-password', { email: email.value})
            alert('已寄出重設密碼信，請至信箱點擊連結')
        }
        catch (error) {
            console.error('寄送失敗', error)
        }
    }
</script>
