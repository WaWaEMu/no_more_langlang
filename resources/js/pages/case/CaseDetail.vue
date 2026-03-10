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

                    <!-- Info Cards Row -->
                    <div class="row g-4 mb-4">
                        <!-- Case Info -->
                        <div class="col-md-6">
                            <div class="case-detail__card h-100">
                                <h5 class="case-detail__card-title">
                                    <i class="bi bi-clipboard-data me-2"></i>{{ $t('caseDetail.CaseInfo') }}
                                </h5>
                                <div class="case-detail__info-grid">
                                    <div class="case-detail__info-row">
                                        <span class="case-detail__info-key">{{ $t('caseDetail.CaseId') }}</span>
                                        <span class="case-detail__info-val">#{{ adoptionCase.id }}</span>
                                    </div>
                                    <div class="case-detail__info-row">
                                        <span class="case-detail__info-key">{{ $t('caseDetail.Source') }}</span>
                                        <span class="case-detail__info-val">
                                            {{ adoptionCase.source === 'manual' ? $t('caseDetail.SourceManual') :
                                            $t('caseDetail.SourcePlatform') }}
                                        </span>
                                    </div>
                                    <div class="case-detail__info-row">
                                        <span class="case-detail__info-key">{{ $t('caseDetail.TrackingFrequency')
                                            }}</span>
                                        <span class="case-detail__info-val">
                                            {{ getTrackingFrequencyText(adoptionCase.tracking_config?.frequency) }}
                                        </span>
                                    </div>
                                    <div class="case-detail__info-row">
                                        <span class="case-detail__info-key">{{ $t('Adopted Date') }}</span>
                                        <span class="case-detail__info-val">{{ formatDate(adoptionCase.started_at)
                                            }}</span>
                                    </div>
                                    <div v-if="adoptionCase.next_report_due_at" class="case-detail__info-row">
                                        <span class="case-detail__info-key">{{ $t('Next Report Due') }}</span>
                                        <span class="case-detail__info-val text-primary fw-semibold">
                                            {{ formatDate(adoptionCase.next_report_due_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- People Info -->
                        <div class="col-md-6">
                            <div class="case-detail__card h-100">
                                <h5 class="case-detail__card-title">
                                    <i class="bi bi-people me-2"></i>{{ $t('caseDetail.People') }}
                                </h5>
                                <div class="case-detail__info-grid">
                                    <div class="case-detail__info-row">
                                        <span class="case-detail__info-key">{{ $t('Owner') }}</span>
                                        <span class="case-detail__info-val">
                                            {{ adoptionCase.owner?.name || $t('caseDetail.NotSpecified') }}
                                        </span>
                                    </div>
                                    <div class="case-detail__info-row">
                                        <span class="case-detail__info-key">{{ $t('Adopter') }}</span>
                                        <span class="case-detail__info-val">
                                            {{ adoptionCase.adopter?.name || $t('caseDetail.NotSpecified') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Reports Section -->
                    <div class="case-detail__card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="case-detail__card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>{{ $t('caseDetail.TrackingTimeline') }}
                            </h5>
                            <!-- Submit Report Button (Adopter only, active cases) -->
                            <button
                                v-if="role === 'adopter' && adoptionCase.status === 'active' && adoptionCase.tracking_config"
                                class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#trackingReportModal">
                                <i class="bi bi-pencil-square me-1"></i>
                                {{ $t('Submit Report') }}
                            </button>
                        </div>

                        <TrackingReportTimeline :case-id="adoptionCase.id" :refresh-key="reportRefreshKey" />
                    </div>

                    <!-- Report Form Modal -->
                    <TrackingReportForm v-if="role === 'adopter' && adoptionCase.id" :case-id="adoptionCase.id"
                        :pet-name="pet.name" :pet-image="petImageUrl" @submitted="onReportSubmitted" />
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
import TrackingReportTimeline from '@/components/adoptions/TrackingReportTimeline.vue'
import TrackingReportForm from '@/components/adoptions/TrackingReportForm.vue'

const $t = trans
const route = useRoute()
const router = useRouter()

const loading = ref(true)
const error = ref('')
const adoptionCase = ref<any>(null)
const role = ref<'owner' | 'adopter'>('owner')
const reportRefreshKey = ref(0)

const pet = computed(() => adoptionCase.value?.pet || {})

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

function getTrackingFrequencyText(frequency?: string) {
    if (!frequency) return $t('No Tracking')
    const texts: Record<string, string> = {
        'weekly': $t('Weekly'),
        'monthly': $t('Monthly'),
        'quarterly': $t('Quarterly'),
    }
    return texts[frequency] || frequency
}

function formatDate(dateStr?: string) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString()
}

function onReportSubmitted() {
    reportRefreshKey.value++
}

onMounted(async () => {
    loading.value = true
    try {
        const res = await axios.get(`/api/adoption-cases/${route.params.id}`)
        adoptionCase.value = res.data.data
        role.value = res.data.role
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

.case-detail__card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.case-detail__card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-denim-blue);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-denim-blue);
    display: inline-block;
}

.case-detail__info-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.case-detail__info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.case-detail__info-row:last-child {
    border-bottom: none;
}

.case-detail__info-key {
    font-size: 0.875rem;
    color: #718096;
    font-weight: 500;
}

.case-detail__info-val {
    font-size: 0.875rem;
    color: #2d3748;
    font-weight: 600;
}

@media (max-width: 768px) {
    .case-detail__hero {
        padding: 1.25rem;
    }

    .case-detail__pet-image {
        max-width: 200px;
    }
}
</style>
