<template>
    <Navbar />
    <Content title="動物認養">
        <template #content>
            <div class="pet-search__wrapper mx-auto">
                <PetSearch />
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Home">
    import Navbar from '@/components/Navbar.vue'
    import PetSearch from '@/components/search/PetSearch.vue'
    import Content from '@/components/Content.vue'
    import { ref, onMounted } from 'vue'
    import { PetInter } from '@/types/pet'
    import axios from 'axios'

    const adopts = ref<PetInter[]>([])

    onMounted(async () => {
        try {
            const res = await axios.get('/api/adopt')
            adopts.value = res.data
        } catch (error) {
          console.error('Failed to fetch adopts:', error)
        }
    })
</script>

<style>
    .pet-search__wrapper {
        max-width: 900px;
    }
</style>
