<template>
    <Navbar />
    <Content :title="$t('Adoption Management')">
        <template v-slot:content>
            <div class="adoptions__container mx-auto">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">{{ $t('Loading data...') }}</p>
                </div>

                <!-- Content State -->
                <div v-else>
                    <!-- Sub Tabs -->
                    <div class="d-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs border-bottom-0 mb-3 adoptions__sub-tabs">
                            <li class="nav-item">
                                <button class="nav-link bg-transparent border-0 pb-2 px-1 me-4 position-relative"
                                    :class="{ active: activeTab === 'active' }" @click="activeTab = 'active'">
                                    <span class="fw-semibold">{{ $t('In Progress') }}</span>
                                    <span class="badge rounded-pill bg-secondary ms-2 text-white opacity-75">{{
                                        activePets.length }}</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link bg-transparent border-0 pb-2 px-1 position-relative"
                                    :class="{ active: activeTab === 'history' }" @click="activeTab = 'history'">
                                    <span class="fw-semibold">{{ $t('Adopted') }}</span>
                                    <span class="badge rounded-pill bg-secondary ms-2 text-white opacity-75">{{
                                        historyPets.length }}</span>
                                </button>
                            </li>
                        </ul>
                        <RouterLink to="/adopt/new" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i>
                            <span>{{ $t('Create New Adoption') }}</span>
                        </RouterLink>
                    </div>

                    <!-- Tab Contents -->
                    <div v-show="activeTab === 'active'">
                        <AdoptionActiveList :pets="activePets" />
                    </div>

                    <div v-show="activeTab === 'history'">
                        <AdoptionHistoryList :pets="historyPets" />
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Adoptions">
import { onMounted, ref, computed } from 'vue'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import AdoptionActiveList from '@/components/adoptions/AdoptionActiveList.vue'
import AdoptionHistoryList from '@/components/adoptions/AdoptionHistoryList.vue'
import { useAdoptStore } from '@/stores/adopt'
import { useAuthStore } from '@/stores/auth'
import { PetInter } from '@/types/pet'

const authStore = useAuthStore()
const pets = ref<PetInter[]>([])
const activeTab = ref<'active' | 'history'>('active')

const adoptStore = useAdoptStore()
const { fetchUserPets } = adoptStore

const loading = ref(true)

// Computed properties for filtering
const activePets = computed(() => {
    return pets.value.filter(pet => ['available', 'paused', 'pending'].includes(pet.status))
})

const historyPets = computed(() => {
    return pets.value.filter(pet => pet.status === 'adopted')
})

onMounted(async () => {
    loading.value = true
    try {
        await authStore.fetchUser()
        pets.value = await fetchUserPets()
    } catch (error) {
        console.error('Failed to load user pets:', error)
    } finally {
        loading.value = false
    }
})

</script>

<style scoped>
.adoptions__container {
    max-width: 1200px;
    padding: 0 1rem;
}

.adoptions__sub-tabs .nav-link {
    color: #64748b;
    border-bottom: 2px solid transparent;
    border-radius: 0;
}

.adoptions__sub-tabs .nav-link:hover {
    color: var(--color-denim-blue);
}

.adoptions__sub-tabs .nav-link.active {
    color: var(--color-denim-blue);
    border-bottom-color: var(--color-denim-blue);
}

.adoptions__sub-tabs .nav-link.active .badge {
    background-color: var(--color-denim-blue) !important;
    opacity: 1 !important;
}
</style>
