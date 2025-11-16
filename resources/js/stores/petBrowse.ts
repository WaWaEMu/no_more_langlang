import { defineStore } from "pinia"
import { ref, computed } from "vue"
import axios from 'axios'
import { PetInter } from '@/types/pet'

export const usePetStore = defineStore('browse', () => {
    const pets = ref<PetInter[]>([])
    const activeType = ref('貓咪')

    const loading = ref(false)
    const error = ref<string | null>(null)

    const activePets = computed(() => {
        let list = pets.value

        if (activeType.value === '貓咪') list = pets.value.filter(pet => pet.type === '貓咪')
        else if (activeType.value === '狗狗') list = pets.value.filter(pet => pet.type === '狗狗')

        return list
    })

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

    function changeType(type: string) {
        activeType.value = type
    }

    return {
        pets,
        loading,
        error,
        fetchPets,
        changeType,
        activeType,
        activePets
    }
})