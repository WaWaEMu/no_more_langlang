import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

interface User {
    id: number
    name: string
    email: string
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null)
    const loading = ref(false)
    const error = ref<string | null>(null)

    const isAuthenticated = computed(() => !!user.value)

    const fetchUser = async () => {
        if (user.value) return

        loading.value = true
        error.value = null
        try {
            const response = await axios.get('/api/user')
            user.value = response.data
        } catch (err: any) {
            if (err.response?.status !== 401) {
                error.value = err.message
            }
            user.value = null
        } finally {
            loading.value = false
        }
    }

    const isOwner = (resourceUserId: number): boolean => {
        if (!user.value) return false
        return user.value.id === resourceUserId
    }

    const logout = async () => {
        try {
            await axios.post('/logout')
        } finally {
            user.value = null
        }
    }

    const login = async (credentials: any) => {
        loading.value = true
        error.value = null
        try {
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/login', credentials)
            await fetchUser()
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message
            throw err
        } finally {
            loading.value = false
        }
    }

    const register = async (data: any) => {
        loading.value = true
        error.value = null
        try {
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/register', data)
            await fetchUser()
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message
            throw err
        } finally {
            loading.value = false
        }
    }

    const updateProfile = async (data: { name: string }) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.put('/api/user/profile', data)
            user.value = response.data
            return response.data
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message
            throw err
        } finally {
            loading.value = false
        }
    }

    const updatePassword = async (data: any) => {
        loading.value = true
        error.value = null
        try {
            await axios.put('/api/user/password', data)
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message
            throw err
        } finally {
            loading.value = false
        }
    }

    return {
        user,
        loading,
        error,
        isAuthenticated,
        fetchUser,
        isOwner,
        logout,
        login,
        register,
        updateProfile,
        updatePassword
    }
})
