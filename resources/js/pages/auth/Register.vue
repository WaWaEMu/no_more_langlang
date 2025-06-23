<template>
    <div>
        <h2>註冊</h2>
        <div>
            <form @submit.prevent="register()">
                <div>
                    <label for="register-email">電子信箱</label>
                    <input type="text" id="register-email" v-model="form.email" required>
                </div>
                <div>
                    <label for="register-name">用戶暱稱</label>
                    <input type="text" id="register-name" v-model="form.name" required>
                </div>
                <div>
                    <label for="register-password">密碼</label>
                    <input type="password" id="register-password" v-model="form.password" required>
                </div>
                <div>
                    <label for="confirm-password">確認密碼</label>
                    <input type="password" id="confirm-password" v-model="form.password_confirmation" required>
                </div>
                <div>
                    <button type="submit">註冊</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { reactive } from 'vue'
    import { useRouter  } from 'vue-router'
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
