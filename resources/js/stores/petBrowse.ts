import { defineStore } from "pinia"
import { ref } from "vue"
import axios from 'axios'
import { PetInter } from '@/types/pet'

export const usePetStore = defineStore('browse', () => {
    const pets = ref<PetInter[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)

    async function fetchPets() {
        loading.value = true
        error.value = null

        try {
            const res = await axios.get('/api/adopt')
            pets.value = res.data
        } catch (err: any) {
            error.value = err.message ?? 'Failed to fetch adopts'
        } finally {
            loading.value = false
        }
    }

    return {
        pets,
        loading,
        error,
        fetchPets,
    }
})