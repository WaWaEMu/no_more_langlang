<template>
    <div>
        <h2>重設密碼</h2>
        <p>您正在為帳號 <strong>{{ form.email }}</strong> 重設密碼</p>
        <form @submit.prevent="resetPassword()">
            <div>
                <label for="new-password">新密碼</label>
                <input type="password" id="new-password" v-model="form.password">
            </div>
            <div>
                <label for="confirm-new-password">確認新密碼</label>
                <input type="password" id="confirm-new-password" v-model="form.password_confirmation">
            </div>
            <button>重設密碼</button>
        </form>
    </div>
</template>

<script setup lang="ts">
    import { ref, reactive, onMounted } from 'vue'
    import axios from 'axios'
    import { useRouter } from 'vue-router'

    const router = useRouter()

    const email = ref('')
    const verify = ref('')

    const form = reactive({
        email: '',
        token: '',
        password: '',
        password_confirmation: ''
    })

    onMounted(async () => {
        const res = await axios.get('/api/password-reset/session')
        form.token = res.data.token,
        form.email = res.data.email
    })

    async function resetPassword() {
        try {
            await axios.get('/sanctum/csrf-cookie')
            // TodoList: handle status 422 (Token has expired or is invalid)
            const res = await axios.post('/reset-password', form)
            router.push('/login')
        }
        catch (error) {
            console.error('重設密碼失敗', error)
        }
    }
</script>
