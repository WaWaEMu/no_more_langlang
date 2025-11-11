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

<script setup lang="ts" name="Home">
    import Navbar from '@/components/Navbar.vue'
    import PetSearch from '@/components/search/PetSearch.vue'
    import Content from '@/components/Content.vue'
    import PetList from '@/components/adopt/PetList.vue'
    import { ref, onMounted, computed } from 'vue'
    import { PetInter } from '@/types/pet'
    import axios from 'axios'
    import { PetFiltersInter } from '@/types/option'

    const adopts = ref<PetInter[]>([])
    const activeType = ref('貓咪')

    const cats = computed(() => adopts.value.filter(pet => pet.type === '貓咪'))
    const dogs = computed(() => adopts.value.filter(pet => pet.type === '狗狗'))

    const activePets = computed(() => {
        if (activeType.value === '貓咪') return cats.value
        if (activeType.value === '狗狗') return dogs.value
        return adopts.value
    })

    onMounted(async () => {
        try {
            const res = await axios.get('/api/adopt')
            adopts.value = res.data
        } catch (error) {
          console.error('Failed to fetch adopts:', error)
        }
    })

    function handlePetTypeChange(petType: string) {
        activeType.value = petType
    }

    function handlePetFiltersChange(petFilters: PetFiltersInter) {
        console.log('From PetFilter.vue!!', petFilters)
    }
</script>

<style>
    .pet-search__wrapper {
        max-width: 900px;
    }
</style>
