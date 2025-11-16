<template>
    <Navbar />
    <Content title="動物認養">
        <template #content>
            <div class="pet-search__wrapper mx-auto">
                <PetSearch @change:pet-type="handlePetTypeChange" @change:pet-filters="handlePetFiltersChange"/>
                <PetList :pet-list="activePets" />
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="PetBrowse">
    import Navbar from '@/components/Navbar.vue'
    import PetSearch from '@/components/search/PetSearch.vue'
    import Content from '@/components/Content.vue'
    import PetList from '@/components/adopt/PetList.vue'
    import { ref, reactive, onMounted, computed } from 'vue'
    import { PetFiltersInter } from '@/types/option'
    import { usePetStore } from '@/stores/petBrowse'

    const petStore = usePetStore()
    const { fetchPets } = petStore

    const activeType = ref('貓咪')
    const defaultFilterValue = { label: '', value: '' }
    const petFilters = reactive<PetFiltersInter>({
        city: { ...defaultFilterValue },
        color: { ...defaultFilterValue },
        fur_type: { ...defaultFilterValue },
        gender: { ...defaultFilterValue },
        age: { ...defaultFilterValue },
        is_neuter: { ...defaultFilterValue },
    })

    const activePets = computed(() => {
        let list = petStore.pets
        if (activeType.value === '貓咪') list = petStore.pets.filter(pet => pet.type === '貓咪')
        else if (activeType.value === '狗狗') list = petStore.pets.filter(pet => pet.type === '狗狗')

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

    onMounted(async () => {
        await fetchPets()
    })

    function handlePetTypeChange(petType: string) {
        activeType.value = petType
    }

    function handlePetFiltersChange(filters: PetFiltersInter) {
        Object.assign(petFilters, filters)
    }
</script>

<style>
    .pet-search__wrapper {
        max-width: 900px;
    }
</style>
