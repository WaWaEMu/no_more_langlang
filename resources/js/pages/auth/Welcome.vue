<template>
    LOGIN Successfully!!!
    <!-- Logout -->
    <div>
        <button @click="logout()">Logout</button>
    </div>
</template>

<script setup lang="ts">
    import axios from 'axios'
    import { useRouter } from 'vue-router'

    const router = useRouter()

    async function logout() {
        try {
            await axios.get('/sanctum/csrf-cookie') // Make sure the CSRF token is set
            await axios.post('/logout')
            router.push('/bye')
        }
        catch (error) {
            console.error('登入失敗:', error)
        }
    }
</script>
