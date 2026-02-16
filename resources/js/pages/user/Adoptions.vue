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

                    <!-- Active Pets List -->
                    <div v-if="activeTab === 'active'">
                        <div class="mb-3 text-muted" v-if="activePets.length > 0">
                            {{ $t('Total Adoption Cases', { count: activePets.length.toString() }) }}
                        </div>

                        <div v-if="activePets.length > 0">
                            <PetList :pet-list="activePets" :editable="true" />
                        </div>

                        <!-- Empty State for Active -->
                        <div v-else class="adoptions__empty-state text-center py-5 bg-light rounded-3">
                            <div class="mb-3">
                                <i class="bi bi-box2-heart display-1 text-muted opacity-50"></i>
                            </div>
                            <h4 class="fw-bold text-secondary mb-2">{{ $t('No active adoption cases') }}</h4>
                            <p class="text-muted mb-4">{{ $t('No active adoption cases desc') }}</p>
                        </div>
                    </div>

                    <!-- History Pets List -->
                    <div v-else-if="activeTab === 'history'">
                        <div class="mb-3 text-muted" v-if="historyPets.length > 0">
                            {{ $t('Total Adoption Cases', { count: historyPets.length.toString() }) }}
                        </div>

                        <!-- Simple list view for now, without detailed Case data -->
                        <div v-if="historyPets.length > 0" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <div class="col" v-for="pet in historyPets" :key="pet.id">
                                <div class="card h-100 border-0 shadow-sm adoptions__card adoptions__card--hover">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="d-flex align-items-center">
                                                <img :src="getPetImageUrl(pet)" 
                                                    class="rounded-circle object-fit-cover" 
                                                    style="width: 50px; height: 50px;" 
                                                    :alt="pet.name">
                                                <div class="ms-3">
                                                    <h5 class="card-title fw-bold mb-0 text-dark">{{ pet.name }}</h5>
                                                    <small class="text-muted fw-semibold" style="font-size: 0.85rem;">
                                                        {{ pet.type }} / {{ pet.age }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column align-items-end">
                                                <span v-if="pet.adoption_case?.tracking_config" 
                                                    class="badge rounded-pill px-3 py-2"
                                                    style="font-size: 0.85rem;"
                                                    :class="getTrackingBadgeClass(pet.adoption_case.tracking_config.frequency)">
                                                    {{ getTrackingFrequencyText(pet.adoption_case.tracking_config.frequency) }}
                                                </span>
                                                <span v-else class="badge adoptions__tag--none rounded-pill p-2" style="font-size: 0.85rem;">
                                                    {{ $t('No Tracking') }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Next Report Due (if applicable) -->
                                        <div v-if="pet.adoption_case?.next_report_due_at" 
                                            class="alert adoptions__report-alert py-2 px-3 mt-3 d-flex align-items-center gap-2 rounded-3 shadow-sm">
                                            <i class="bi bi-calendar-event text-primary"></i>
                                            <small class="text-dark">
                                                <strong class="text-primary">{{ $t('Next Report Due') }}:</strong> 
                                                {{ new Date(pet.adoption_case.next_report_due_at).toLocaleDateString() }}
                                            </small>
                                        </div>
                                        
                                        <div class="adoptions__case-info rounded-3 p-3" v-if="pet.adoption_case">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-secondary small">{{ $t('Adopter') }}</span>
                                                <span class="fw-semibold text-dark">{{ pet.adoption_case.adopter.name }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-secondary small">{{ $t('Adopted Date') }}</span>
                                                <span class="text-dark small">{{ new Date(pet.adoption_case.started_at).toLocaleDateString() }}</span>
                                            </div>
                                        </div>

                                        <!-- Final Action -->
                                        <div class="d-flex justify-content-end mt-3">
                                            <button class="btn btn-primary btn-sm px-3 py-2 shadow-sm adoptions__view-btn">
                                                <i class="bi bi-info-circle me-1"></i>
                                                {{ $t('View Details') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State for History -->
                        <div v-else class="adoptions__empty-state text-center py-5 bg-light rounded-3">
                            <div class="mb-3">
                                <i class="bi bi-check-circle display-1 text-muted opacity-50"></i>
                            </div>
                            <h4 class="fw-bold text-secondary mb-2">{{ $t('No completed adoption cases') }}</h4>
                            <p class="text-muted mb-0">{{ $t('No completed adoption cases desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Adoptions">
import { onMounted, ref, computed } from 'vue'
import { trans } from 'laravel-vue-i18n'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import PetList from '@/components/adopt/PetList.vue'
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

function getTrackingBadgeClass(frequency: string) {
    const classes = {
        'weekly': 'adoptions__tag--weekly',
        'monthly': 'adoptions__tag--monthly',
        'quarterly': 'adoptions__tag--quarterly'
    }
    return classes[frequency as keyof typeof classes] || 'adoptions__tag--none'
}

function getTrackingFrequencyText(frequency: string) {
    const texts = {
        'weekly': trans('Weekly'),
        'monthly': trans('Monthly'),
        'quarterly': trans('Quarterly')
    }
    return texts[frequency as keyof typeof texts] || frequency
}

function getPetImageUrl(pet: PetInter) {
    if (pet.images && pet.images.length > 0) {
        return `/storage/${pet.images[0].path}`
    }
    return '/images/default-pet.png'
}

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

.adoptions__empty-state {
    border: 1px dashed #dee2e6;
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

.adoptions__card {
    transition: all 0.3s ease;
}

.adoptions__card--hover:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

.adoptions__view-btn {
    transition: all 0.3s ease;
}

.adoptions__view-btn:hover {
    transform: translateY(-2px);
}

.adoptions__report-alert {
    background-color: var(--color-fog-gray);
    border: none;
    border-left: 4px solid var(--color-denim-blue) !important;
}

.adoptions__tag--weekly {
    background-color: #bee3f8 !important;
    color: #2c5282 !important;
}

.adoptions__tag--monthly {
    background-color: #feebc8 !important;
    color: #9c4221 !important;
}

.adoptions__tag--quarterly {
    background-color: #c6f6d5 !important;
    color: #22543d !important;
}

.adoptions__tag--none {
    background-color: #e2e8f0 !important;
    color: #4a5568 !important;
}

.adoptions__case-info {
    background-color: var(--color-fog-gray);
    border: 1px solid #dee2e6;
}
</style>
