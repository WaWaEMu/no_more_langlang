<template>
    <Navbar />
    <Content title="動物認養">
        <template #content>
            <div class="pet-search__wrapper mx-auto">
                <PetSearch />
                <PetList :pet-list="activePets" />
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Adopt">
    import Navbar from '@/components/Navbar.vue'
    import PetSearch from '@/components/search/PetSearch.vue'
    import Content from '@/components/Content.vue'
    import PetList from '@/components/adopt/PetList.vue'
    import { onMounted } from 'vue'
    import { usePetStore } from '@/stores/adopt'
    import { storeToRefs } from 'pinia'

    const petStore = usePetStore()
    const { activePets } = storeToRefs(petStore)
    const { fetchPets } = petStore

    onMounted(async () => {
        await fetchPets()
    })
</script>

<style>
    .pet-search__wrapper {
        max-width: 900px;
    }
</style>
