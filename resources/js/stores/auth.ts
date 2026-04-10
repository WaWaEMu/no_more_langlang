import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

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

    /**
     * Check if user is authenticated. If not, show a confirmation dialog.
     */
    const checkAuth = async (message: string = '此功能需要登入後才能使用，是否前往登入頁面？'): Promise<boolean> => {
        if (isAuthenticated.value) return true

        const result = await Swal.fire({
            title: '需要登入',
            text: message,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: '前往登入',
            cancelButtonText: '取消',
            confirmButtonColor: '#2c5282',
            cancelButtonColor: '#6c757d',
        })

        if (result.isConfirmed) {
            window.location.href = `/auth/login?redirect=${encodeURIComponent(window.location.pathname)}`
            return false
        }

        return false
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
        updatePassword,
        checkAuth
    }
})
