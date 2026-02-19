<template>
    <Navbar />
    <Content :title="$t('Adopt Management')">
        <template v-slot:content>
            <div class="adopted__container mx-auto">
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
                    <ul class="nav nav-tabs border-bottom-0 mb-3 adopted__sub-tabs">
                        <li class="nav-item">
                            <button class="nav-link bg-transparent border-0 pb-2 px-1 me-4 position-relative"
                                :class="{ active: activeTab === 'active' }" @click="activeTab = 'active'">
                                <span class="fw-semibold">{{ $t('Tracking Report') }}</span>
                                <span class="badge rounded-pill bg-secondary ms-1 text-white opacity-75">{{ activeCases.length }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link bg-transparent border-0 pb-2 px-1 position-relative"
                                :class="{ active: activeTab === 'completed' }" @click="activeTab = 'completed'">
                                <span class="fw-semibold">{{ $t('Tracking Ended') }}</span>
                                <span class="badge rounded-pill bg-secondary ms-1 text-white opacity-75">{{ completedCases.length }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Active Adopted Cases -->
                    <div v-show="activeTab === 'active'">
                        <div v-if="activeCases.length > 0" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <div class="col" v-for="pet in activeCases" :key="pet.id">
                                <AdoptionCaseCard :pet="pet" role="adopter" />
                            </div>
                        </div>
                        <div v-else class="adopted__empty-state text-center py-5 bg-light rounded-3 shadow-sm border">
                            <div class="mb-3">
                                <i class="bi bi-house-heart display-1 text-muted opacity-50"></i>
                            </div>
                            <h4 class="fw-bold text-secondary mb-2">{{ $t('No active tracking cases') }}</h4>
                            <p class="text-muted mb-0">{{ $t('No active tracking cases desc') }}</p>
                        </div>
                    </div>

                    <!-- Completed Adopted Cases -->
                    <div v-show="activeTab === 'completed'">
                        <div v-if="completedCases.length > 0" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <div class="col" v-for="pet in completedCases" :key="pet.id">
                                <AdoptionCaseCard :pet="pet" role="adopter" />
                            </div>
                        </div>
                        <div v-else class="adopted__empty-state text-center py-5 bg-light rounded-3 shadow-sm border">
                            <div class="mb-3">
                                <i class="bi bi-check2-all display-1 text-muted opacity-50"></i>
                            </div>
                            <h4 class="fw-bold text-secondary mb-2">{{ $t('No completed tracking cases') }}</h4>
                            <p class="text-muted mb-0">{{ $t('No completed tracking cases desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Adopted">
import { ref, onMounted, computed } from 'vue'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import AdoptionCaseCard from '@/components/adoptions/AdoptionCaseCard.vue'
import { useAuthStore } from '@/stores/auth'
import { useAdoptStore } from '@/stores/adopt'
import { PetInter } from '@/types/pet'

const authStore = useAuthStore()
const adoptStore = useAdoptStore()
const activeTab = ref<'active' | 'completed'>('active')
const loading = ref(true)
const adoptionCases = ref<PetInter[]>([])

const activeCases = computed(() => {
    return adoptionCases.value.filter(pet => pet.adoption_case?.status === 'active')
})

const completedCases = computed(() => {
    return adoptionCases.value.filter(pet => pet.adoption_case?.status === 'completed')
})

onMounted(async () => {
    loading.value = true
    try {
        await authStore.fetchUser()
        adoptionCases.value = await adoptStore.fetchAdoptionCases('adopter')
    } catch (error) {
        console.error('Failed to fetch adoption cases:', error)
    } finally {
        loading.value = false
    }
})

</script>

<style scoped>
.adopted__container {
    max-width: 1200px;
    padding: 0 1rem;
}

.adopted__empty-state {
    border: 1px dashed #dee2e6;
}

.adopted__sub-tabs .nav-link {
    color: #64748b;
    border-bottom: 2px solid transparent;
    border-radius: 0;
}

.adopted__sub-tabs .nav-link:hover {
    color: var(--color-denim-blue);
}

.adopted__sub-tabs .nav-link.active {
    color: var(--color-denim-blue);
    border-bottom-color: var(--color-denim-blue);
}

.adopted__sub-tabs .nav-link.active .badge {
    background-color: var(--color-denim-blue) !important;
    opacity: 1 !important;
}
</style>
