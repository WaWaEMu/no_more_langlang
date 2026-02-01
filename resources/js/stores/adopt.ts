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

    // Pagination state
    const currentPage = ref(1)
    const lastPage = ref(1)
    const total = ref(0)
    const perPage = ref(12)

    const emptyFilters: PetFiltersInter = {
        city: { label: '', value: '' },
        color: { label: '', value: '' },
        fur_type: { label: '', value: '' },
        gender: { label: '', value: '' },
        age: { label: '', value: '' },
        is_neuter: { label: '', value: '' },
        status: { label: '', value: '' },
    }

    const petFilters = reactive<PetFiltersInter>({ ...emptyFilters })
    const keyword = ref('')

    const activePets = computed(() => pets.value)

    async function fetchPets(page = 1) {
        loading.value = true
        error.value = null
        currentPage.value = page

        try {
            const params: any = {
                page,
                type: activeType.value,
            }

            // Add filters to params
            if (petFilters.city.value) params.city = petFilters.city.value
            if (petFilters.color.value) params.color = petFilters.color.value
            if (petFilters.fur_type.value) params.fur_type = petFilters.fur_type.value
            if (petFilters.gender.value) params.gender = petFilters.gender.value
            if (petFilters.age.value) params.age = petFilters.age.value
            if (petFilters.is_neuter.value !== '') {
                params.is_neuter = petFilters.is_neuter.value
            }
            if (petFilters.status.value) params.status = petFilters.status.value
            if (keyword.value) params.keyword = keyword.value

            const res = await axios.get('/api/adopt', { params })

            // Laravel paginated response
            pets.value = res.data.data
            currentPage.value = res.data.current_page
            lastPage.value = res.data.last_page
            total.value = res.data.total
        } catch (err: any) {
            error.value = err.message ?? 'Failed to fetch adopts'
        } finally {
            loading.value = false
        }
    }

    function changeType(type: string) {
        activeType.value = type
        fetchPets(1) // Reset to page 1 when type changes
    }

    function updateFilters(key: keyof PetFiltersInter, item: OptionItem) {
        petFilters[key] = item
        fetchPets(1) // Reset to page 1 when filters change
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
        fetchPets(1)
    }

    async function fetchUserPets(): Promise<PetInter[]> {
        loading.value = true
        error.value = null

        try {
            const res = await axios.get('/api/user/pets')
            return res.data
        } catch (err: any) {
            error.value = err.message ?? 'Failed to fetch user pets'
            throw err
        } finally {
            loading.value = false
        }
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
        currentPage,
        lastPage,
        total,
        perPage,
        fetchPets,
        fetchPetById,
        changeType,
        activeType,
        activePets,
        petFilters,
        keyword,
        updateFilters,
        resetFilters,
        fetchUserPets,
        // Favorites exports
        toggleFavorite,
        isFavorite,
        favoritePets
    }
})