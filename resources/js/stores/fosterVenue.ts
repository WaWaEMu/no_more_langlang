import { defineStore } from "pinia"
import { ref, reactive } from "vue"
import axios from 'axios'
import { FosterVenueInter, FosterVenueFiltersInter } from "@/types/fosterVenue"

export const useFosterVenueStore = defineStore('fosterVenue', () => {
    const venues = ref<FosterVenueInter[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)

    const filters = reactive<FosterVenueFiltersInter>({
        city: '',
        type: '',
        pet_type: ''
    })

    /**
     * Fetch all foster venues from API
     */
    async function fetchVenues() {
        loading.value = true
        error.value = null

        try {
            const params: any = {}
            if (filters.city) params.city = filters.city
            if (filters.type) params.type = filters.type
            if (filters.pet_type) params.pet_type = filters.pet_type

            const res = await axios.get('/api/foster-venues', { params })
            venues.value = res.data.data
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch foster venues'
            console.error('FetchVenues Error:', err)
        } finally {
            loading.value = false
        }
    }

    /**
     * Reset all filters to default
     */
    function resetFilters() {
        filters.city = ''
        filters.type = ''
        filters.pet_type = ''
        fetchVenues()
    }

    return {
        venues,
        loading,
        error,
        filters,
        fetchVenues,
        resetFilters
    }
})
