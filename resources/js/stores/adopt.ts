import { defineStore } from "pinia"
import { ref, reactive, computed } from "vue"
import axios from 'axios'
import { PetInter } from '@/types/pet'
import { OptionItem, PetFiltersInter } from "@/types/option"

export const useAdoptStore = defineStore('adopt', () => {
    const pets = ref<PetInter[]>([])
    const activeType = ref('貓咪')

    const loading = ref(false)
    const error = ref<string | null>(null)

    const emptyFilters: PetFiltersInter = {
        city: { label: '', value: '' },
        color: { label: '', value: '' },
        fur_type: { label: '', value: '' },
        gender: { label: '', value: '' },
        age: { label: '', value: '' },
        is_neuter: { label: '', value: '' },
    }

    const petFilters = reactive<PetFiltersInter>({ ...emptyFilters })

    const activePets = computed(() => {
        let list = pets.value

        if (activeType.value === '貓咪') list = pets.value.filter(pet => pet.type === '貓咪')
        else if (activeType.value === '狗狗') list = pets.value.filter(pet => pet.type === '狗狗')

        return list.filter(pet => {
            const f = petFilters
            if (f.city['value'] && pet.city !== f.city['value']) return false
            if (f.color['value'] && pet.color !== f.color['value']) return false
            if (f.fur_type['value'] && pet.fur_type !== f.fur_type['value']) return false
            if (f.gender['value'] && pet.gender !== f.gender['value']) return false
            if (f.age['value'] && pet.age !== f.age['value']) return false
            if (f.is_neuter['value'] !== '' && pet.is_neuter !== (f.is_neuter.value ? 1 : 0)) return false
            return true
        })
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

    function updateFilters(key: keyof PetFiltersInter, item: OptionItem) {
        petFilters[key] = item
    }

    async function fetchPetById(id: string | number): Promise<PetInter> {
        loading.value = true
        error.value = null

        try {
            const res = await axios.get(`/api/adopt/${id}`)
            return res.data
        } catch (err: any) {
            error.value = err.message ?? 'Failed to fetch pet details'
            throw err
        } finally {
            loading.value = false
        }
    }

    function resetFilters() {
        Object.assign(petFilters, emptyFilters)
    }

    function getMyPets(userid: number) {
        return pets.value.filter(pet => pet.user.id === userid)
    }

    // Favorites Logic
    async function toggleFavorite(petId: number) {
        // Optimistic update
        const pet = pets.value.find(p => p.id === petId)
        if (pet) {
            pet.is_favorite = !pet.is_favorite
        }

        try {
            const res = await axios.post(`/api/adopt/${petId}/favorite`)
            // Sync with server response
            if (pet) {
                pet.is_favorite = res.data.is_favorite
            }
        } catch (err) {
            // Revert on error
            if (pet) {
                pet.is_favorite = !pet.is_favorite
            }
            console.error('Failed to toggle favorite', err)
        }
    }

    function isFavorite(petId: number): boolean {
        const pet = pets.value.find(p => p.id === petId)
        return !!pet?.is_favorite
    }

    const favoritePets = computed(() => {
        return pets.value.filter(pet => pet.is_favorite)
    })

    return {
        pets,
        loading,
        error,
        fetchPets,
        fetchPetById,
        changeType,
        activeType,
        activePets,
        petFilters,
        updateFilters,
        resetFilters,
        getMyPets,
        // Favorites exports
        toggleFavorite,
        isFavorite,
        favoritePets
    }
})