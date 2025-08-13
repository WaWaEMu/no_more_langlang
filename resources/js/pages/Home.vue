<template>
    <Navbar />
</template>

<script setup lang="ts" name="Home">
    import Navbar from '@/components/Navbar.vue'
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
