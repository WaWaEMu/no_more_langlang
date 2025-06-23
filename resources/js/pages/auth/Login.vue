<template>
    <div>
        <h2>登入</h2>
        <div>
            <form @submit.prevent="login()">
                <div>
                    <label for="login-email">電子信箱</label>
                    <input type="text" id="login-email" v-model="form.email">
                </div>
                <div>
                    <label for="login-password">密碼</label>
                    <input type="password" id="login-password" v-model="form.password">
                </div>
                <div>
                    <button type="submit">登入</button>
                </div>
                <div>
                    <RouterLink to="/auth/register">
                        註冊新帳號
                    </RouterLink>
                    <RouterLink to="/auth/forgot">
                        忘記密碼
                    </RouterLink>
                </div>
            </form>
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
        password: ''
    })

    async function login() {
        try {
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/login', form)
            router.push('/welcome')
        }
        catch (error) {
            console.error('登入失敗:', error)
        }
    }
</script>
