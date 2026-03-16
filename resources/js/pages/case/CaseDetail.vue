<template>
    <Navbar />
    <Content :title="$t('Case Detail')">
        <template v-slot:content>
            <div class="case-detail mx-auto">
                <!-- Loading -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">{{ $t('Loading data...') }}</p>
                </div>

                <!-- Error -->
                <div v-else-if="error" class="text-center py-5">
                    <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                    <h4 class="mt-3 text-secondary">{{ error }}</h4>
                    <RouterLink to="/user/adoptions" class="btn btn-outline-primary mt-3">
                        <i class="bi bi-arrow-left me-1"></i>{{ $t('Back') }}
                    </RouterLink>
                </div>

                <!-- Content -->
                <div v-else-if="adoptionCase">
                    <!-- Back Button -->
                    <button @click="router.back()" class="btn btn-link text-muted text-decoration-none mb-3 p-0">
                        <i class="bi bi-arrow-left me-1"></i>{{ $t('Back') }}
                    </button>

                    <!-- Hero: Pet Image + Basic Info -->
                    <div class="case-detail__hero mb-4">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-4 text-center">
                                <img :src="petImageUrl" :alt="pet.name"
                                    class="case-detail__pet-image rounded-4 shadow" />
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <h2 class="fw-bold text-dark mb-0">{{ pet.name }}</h2>
                                    <span class="badge rounded-pill px-3 py-2" :class="statusBadgeClass">
                                        {{ statusText }}
                                    </span>
                                </div>
                                <p class="text-muted mb-3">
                                    {{ $t(pet.type?.toLowerCase()) }} · {{ pet.age }} · {{ pet.city }} {{ pet.town }}
                                </p>

                                <!-- Quick Stats -->
                                <div class="row g-2">
                                    <div class="col-4 col-md-3">
                                        <div class="case-detail__stat">
                                            <span class="case-detail__stat-label">{{ $t('Gender') }}</span>
                                            <span class="case-detail__stat-value">
                                                {{ pet.gender === 'male' ? '男生' : pet.gender === 'female' ? '女生' : '-'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-3">
                                        <div class="case-detail__stat">
                                            <span class="case-detail__stat-label">{{ $t('Color') }}</span>
                                            <span class="case-detail__stat-value">{{ pet.color || '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-3">
                                        <div class="case-detail__stat">
                                            <span class="case-detail__stat-label">{{ $t('Fur Type') }}</span>
                                            <span class="case-detail__stat-value">{{ pet.fur_type || '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-3">
                                        <div class="case-detail__stat">
                                            <span class="case-detail__stat-label">{{ $t('Neutered') }}</span>
                                            <span class="case-detail__stat-value">
                                                {{ pet.is_neuter ? '已結紮' : '未結紮' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <ul class="nav case-detail__tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="case-detail__tab-btn"
                                :class="{ 'case-detail__tab-btn--active': activeTab === 'overview' }"
                                @click="activeTab = 'overview'" role="tab">
                                <i class="bi bi-clipboard-data me-1"></i>{{ $t('caseDetail.TabOverview') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="case-detail__tab-btn"
                                :class="{ 'case-detail__tab-btn--active': activeTab === 'diary' }"
                                @click="activeTab = 'diary'" role="tab">
                                <i class="bi bi-camera me-1"></i>{{ $t('caseDetail.TabDiary') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="case-detail__tab-btn"
                                :class="{ 'case-detail__tab-btn--active': activeTab === 'tracking' }"
                                @click="activeTab = 'tracking'" role="tab">
                                <i class="bi bi-clock-history me-1"></i>{{ $t('caseDetail.TabTracking') }}
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <CaseOverviewTab v-show="activeTab === 'overview'" :adoption-case="adoptionCase"
                        :application="application" :role="role" @refresh="refreshCase"
                        @edit-tracking="editTrackingModal?.show()" />

                    <EditTrackingModal ref="editTrackingModal" :case-id="adoptionCase.id"
                        :initial-config="adoptionCase.tracking_config" @updated="refreshCase" />

                    <CaseDiaryTab v-show="activeTab === 'diary'" :case-id="adoptionCase.id" :role="role"
                        :status="adoptionCase.status" :pet-name="pet.name" :pet-image="petImageUrl" />

                    <CaseTrackingTab v-show="activeTab === 'tracking'" :case-id="adoptionCase.id" :role="role"
                        :status="adoptionCase.status" :has-tracking-config="!!adoptionCase.tracking_config"
                        :pet-name="pet.name" :pet-image="petImageUrl" />
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { trans } from 'laravel-vue-i18n'
import axios from 'axios'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import CaseOverviewTab from '@/components/case/CaseOverviewTab.vue'
import CaseDiaryTab from '@/components/case/CaseDiaryTab.vue'
import CaseTrackingTab from '@/components/case/CaseTrackingTab.vue'
import EditTrackingModal from '@/components/case/EditTrackingModal.vue'

const $t = trans
const route = useRoute()
const router = useRouter()

const loading = ref(true)
const error = ref('')
const adoptionCase = ref<any>(null)
const role = ref<'owner' | 'adopter'>('owner')
const activeTab = ref<'overview' | 'diary' | 'tracking'>('overview')
const editTrackingModal = ref<any>(null)

const pet = computed(() => adoptionCase.value?.pet || {})
const application = computed(() => adoptionCase.value?.application || null)

const petImageUrl = computed(() => {
    const images = pet.value?.images
    if (images && images.length > 0) {
        return `/storage/${images[0].path}`
    }
    return '/images/default-pet.png'
})

const statusText = computed(() => {
    const map: Record<string, string> = {
        'active': $t('Tracking'),
        'completed': $t('Completed'),
        'paused': $t('Paused'),
    }
    return map[adoptionCase.value?.status] || adoptionCase.value?.status
})

const statusBadgeClass = computed(() => {
    const map: Record<string, string> = {
        'active': 'bg-success',
        'completed': 'bg-secondary',
        'paused': 'bg-warning text-dark',
    }
    return map[adoptionCase.value?.status] || 'bg-secondary'
})

async function refreshCase() {
    try {
        const res = await axios.get(`/api/adoption-cases/${route.params.id}`)
        adoptionCase.value = res.data.data
        role.value = res.data.role
    } catch (err) {
        console.error('Failed to refresh case:', err)
    }
}

onMounted(async () => {
    loading.value = true
    try {
        await refreshCase()
    } catch (err: any) {
        if (err.response?.status === 403) {
            error.value = $t('caseDetail.NoPermission')
        } else if (err.response?.status === 404) {
            error.value = $t('caseDetail.NotFound')
        } else {
            error.value = $t('caseDetail.LoadError')
        }
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
.case-detail {
    max-width: 960px;
    padding: 0 1rem;
}

.case-detail__hero {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.case-detail__pet-image {
    width: 100%;
    max-width: 280px;
    aspect-ratio: 1;
    object-fit: cover;
}

.case-detail__stat {
    background: #f7fafc;
    border-radius: 10px;
    padding: 0.6rem 0.75rem;
    text-align: center;
    border: 1px solid #e2e8f0;
}

.case-detail__stat-label {
    display: block;
    font-size: 0.7rem;
    color: #a0aec0;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.2rem;
}

.case-detail__stat-value {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #2d3748;
}

/* Tab Nav */
.case-detail__tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0;
}

.case-detail__tab-btn {
    background: none;
    border: none;
    padding: 0.75rem 1.25rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    white-space: nowrap;
}

.case-detail__tab-btn:hover {
    color: var(--color-denim-blue);
}

.case-detail__tab-btn--active {
    color: var(--color-denim-blue);
}

.case-detail__tab-btn--active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--color-denim-blue);
    border-radius: 3px 3px 0 0;
}

@media (max-width: 768px) {
    .case-detail__hero {
        padding: 1.25rem;
    }

    .case-detail__pet-image {
        max-width: 200px;
    }

    .case-detail__tabs {
        gap: 0;
    }

    .case-detail__tab-btn {
        padding: 0.6rem 0.75rem;
        font-size: 0.8rem;
    }
}
</style>
