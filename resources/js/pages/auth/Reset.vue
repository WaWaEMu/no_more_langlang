<template>
    <div>
        <h2>{{ $t('Reset Password') }}</h2>
        <p>{{ $t('Resetting password for', { email: form.email }) }}</p>
        <form @submit.prevent="resetPassword()">
            <div>
                <label for="new-password">{{ $t('New Password') }}</label>
                <input type="password" id="new-password" v-model="form.password">
            </div>
            <div>
                <label for="confirm-new-password">{{ $t('Confirm New Password') }}</label>
                <input type="password" id="confirm-new-password" v-model="form.password_confirmation">
            </div>
            <button>{{ $t('Reset Password') }}</button>
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
        console.error('Reset Password Failed', error)
    }
}
</script>
