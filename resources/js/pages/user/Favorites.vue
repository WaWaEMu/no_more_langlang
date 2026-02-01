<template>
    <Navbar />
    <Content title="收藏清單">
        <template v-slot:content>
            <div class="favorites__container mx-auto">
                <div v-if="favoritePets.length > 0">
                    <PetList :pet-list="favoritePets" />
                </div>
                <div v-else class="favorites__empty-state text-center py-5 bg-light rounded-3">
                    <div class="mb-3">
                        <i class="bi bi-bookmark-heart display-1 text-muted opacity-50"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-2">目前沒有收藏的寵物</h4>
                    <p class="text-muted mb-4">看到喜歡的毛小孩，點擊愛心即可加入收藏清單。</p>
                    <RouterLink to="/adopt" class="btn btn-primary">
                        前往認養專區
                    </RouterLink>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Favorites">
import { onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import PetList from '@/components/adopt/PetList.vue'
import { useAdoptStore } from '@/stores/adopt'
import { storeToRefs } from 'pinia'

const adoptStore = useAdoptStore()
const { favoritePets } = storeToRefs(adoptStore)
const { fetchFavorites } = adoptStore

onMounted(() => {
    // Fetch user's favorite pets from the dedicated endpoint
    fetchFavorites()
})
</script>

<style scoped>
.favorites__container {
    max-width: 1200px;
    padding: 0 1rem;
}

.favorites__empty-state {
    border: 1px dashed #dee2e6;
}
</style>