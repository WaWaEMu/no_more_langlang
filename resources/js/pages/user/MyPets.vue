<template>
    <Navbar />
    <Content title="我的送養">
        <template v-slot:content>
            <div class="my-pets__container mx-auto">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">我的送養清單</h2>
                        <p class="text-muted mb-0" v-if="!authLoading">
                            共 {{ pets.length }} 筆送養資料
                        </p>
                    </div>
                    <RouterLink to="/adopt/new" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-plus-lg"></i>
                        <span>新增送養</span>
                    </RouterLink>
                </div>

                <!-- Loading State -->
                <div v-if="authLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">載入資料中...</p>
                </div>

                <!-- Content State -->
                <div v-else>
                    <div v-if="pets.length > 0">
                        <PetList :pet-list="pets" />
                    </div>

                    <!-- Empty State -->
                    <div v-else class="my-pets__empty-state text-center py-5 bg-light rounded-3">
                        <div class="mb-3">
                            <i class="bi bi-box2-heart display-1 text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-bold text-secondary mb-2">目前沒有送養中的寵物</h4>
                        <p class="text-muted mb-4">如果您有需要送養的毛小孩，歡迎新增送養資料。</p>
                        <RouterLink to="/adopt/new" class="btn btn-outline-primary">
                            立即新增送養
                        </RouterLink>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="MyPets">
import { onMounted, ref } from 'vue'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import PetList from '@/components/adopt/PetList.vue'
import { useAdoptStore } from '@/stores/adopt'
import axios from 'axios'
import { PetInter } from '@/types/pet'

const user = ref<{ id: number; name: string; email: string } | null>(null)
const pets = ref<PetInter[]>([])

const adoptStore = useAdoptStore()
const { getMyPets } = adoptStore
const { fetchPets } = adoptStore

const authLoading = ref(true)

onMounted(async () => {
    authLoading.value = true
    try {
        // Ensure pets are fetched first
        await fetchPets()

        const response = await axios.get('/api/user')
        user.value = response.data
        pets.value = getMyPets(user.value?.id as number)
    } catch (error) {
        console.error('Failed to load user pets:', error)
        user.value = null
    } finally {
        authLoading.value = false
    }
})

</script>

<style scoped>
.my-pets__container {
    max-width: 1200px;
    padding: 0 1rem;
}

.my-pets__empty-state {
    border: 1px dashed #dee2e6;
}
</style>
