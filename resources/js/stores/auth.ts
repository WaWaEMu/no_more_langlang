import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

interface User {
    id: number
    name: string
    email: string
    // Add other user properties as needed
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null)
    const loading = ref(false)
    const error = ref<string | null>(null)

    const isAuthenticated = computed(() => !!user.value)

    async function fetchUser() {
        if (user.value) return // Already loaded

        loading.value = true
        error.value = null
        try {
            const response = await axios.get('/api/user')
            user.value = response.data
        } catch (err: any) {
            // If 401, it just means not logged in, which is fine
            if (err.response?.status !== 401) {
                error.value = err.message
            }
            user.value = null
        } finally {
            loading.value = false
        }
    }

    function isOwner(resourceUserId: number): boolean {
        if (!user.value) return false
        return user.value.id === resourceUserId
    }

    return {
        user,
        loading,
        error,
        isAuthenticated,
        fetchUser,
        isOwner
    }
})
