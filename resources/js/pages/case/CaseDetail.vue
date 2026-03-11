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

                    <!-- Adoption Application Form (only for platform cases) -->
                    <div v-if="application" class="case-detail__card mb-4">
                        <h5 class="case-detail__card-title">
                            <i class="bi bi-file-earmark-text me-2"></i>{{ $t('caseDetail.ApplicationForm') }}
                        </h5>

                        <!-- Standard Fields (Structured) -->
                        <div class="case-detail__standard-section p-3 rounded-4 mb-3">
                            <h6 class="fw-bold text-secondary mb-3 small text-uppercase letter-spacing-1">
                                <i class="bi bi-info-square me-1"></i>基本申請內容
                            </h6>
                            <div class="row g-3">
                                <div class="col-6 col-md-4">
                                    <div class="case-detail__grid-item">
                                        <span class="case-detail__grid-label">{{ $t('caseDetail.ApplicantName')
                                            }}</span>
                                        <span class="case-detail__grid-value">{{ application.name }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="case-detail__grid-item">
                                        <span class="case-detail__grid-label">{{ $t('caseDetail.Phone') }}</span>
                                        <span class="case-detail__grid-value">{{ application.phone || '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="case-detail__grid-item">
                                        <span class="case-detail__grid-label">LINE ID</span>
                                        <span class="case-detail__grid-value">{{ application.line_id || '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="case-detail__grid-item">
                                        <span class="case-detail__grid-label">{{ $t('caseDetail.Experience') }}</span>
                                        <span class="case-detail__grid-value">{{
                                            formatExperience(application.experience) }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="case-detail__grid-item">
                                        <span class="case-detail__grid-label">{{ $t('caseDetail.FamilyAgreement')
                                            }}</span>
                                        <span class="case-detail__grid-value">
                                            <i v-if="application.family_agreement"
                                                class="bi bi-check-circle-fill text-success me-1"></i>
                                            <i v-else class="bi bi-x-circle-fill text-danger me-1"></i>
                                            {{ application.family_agreement ? '是' : '否' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="application.message" class="mt-3 pt-3 border-top border-light">
                                <span class="case-detail__grid-label mb-1 d-block">{{ $t('caseDetail.Message') }}</span>
                                <div class="case-detail__grid-value text-break"
                                    style="font-style: italic; color: #4a5568;">
                                    「{{ application.message }}」
                                </div>
                            </div>
                        </div>

                        <!-- Custom Fields (Q&A Style) -->
                        <div v-if="application.custom_fields && Object.keys(application.custom_fields).length > 0"
                            class="case-detail__custom-section mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi bi-patch-question text-primary me-2"></i>{{ $t('caseDetail.CustomFields')
                                }}
                            </h6>
                            <div class="case-detail__qa-list">
                                <div v-for="(value, key) in application.custom_fields" :key="key"
                                    class="case-detail__qa-item mb-3 p-3 rounded-3">
                                    <div class="case-detail__qa-question fw-bold mb-2">
                                        <span class="text-primary me-2">Q.</span>{{ key }}
                                    </div>
                                    <div class="case-detail__qa-answer text-secondary">
                                        <span class="fw-bold me-2">A.</span>{{ formatFieldValue(value) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 📸 Pet Life Diary Section -->
                    <div class="case-detail__card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="case-detail__card-title mb-0">
                                <i class="bi bi-camera me-2"></i>{{ $t('diary.SectionTitle') }}
                            </h5>
                            <button v-if="adoptionCase.status === 'active' && role === 'adopter'"
                                class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#diaryEntryModal">
                                <i class="bi bi-plus-lg me-1"></i>
                                {{ $t('diary.AddEntry') }}
                            </button>
                        </div>

                        <DiaryTimeline :case-id="adoptionCase.id" :refresh-key="diaryRefreshKey" />
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

                    <!-- Diary Entry Modal -->
                    <DiaryEntryForm v-if="role === 'adopter' && adoptionCase.id" :case-id="adoptionCase.id"
                        :pet-name="pet.name" :pet-image="petImageUrl" @submitted="onDiarySubmitted" />
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
import DiaryTimeline from '@/components/diary/DiaryTimeline.vue'
import DiaryEntryForm from '@/components/diary/DiaryEntryForm.vue'

const $t = trans
const route = useRoute()
const router = useRouter()

const loading = ref(true)
const error = ref('')
const adoptionCase = ref<any>(null)
const role = ref<'owner' | 'adopter'>('owner')
const reportRefreshKey = ref(0)
const diaryRefreshKey = ref(0)

const pet = computed(() => adoptionCase.value?.pet || {})
const application = computed(() => adoptionCase.value?.application || null)
const formSchema = computed(() => adoptionCase.value?.pet?.adoption_form_template?.schema || [])

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

function onDiarySubmitted() {
    diaryRefreshKey.value++
}

function formatFieldValue(value: any): string {
    if (Array.isArray(value)) return value.join('、')
    if (typeof value === 'boolean') return value ? '是' : '否'
    return String(value || '-')
}

function formatExperience(exp: string): string {
    const map: Record<string, string> = {
        'none': $t('Experience.None'),
        'newbie': $t('Experience.Newbie'),
        'experienced': $t('Experience.Experienced'),
        'expert': $t('Experience.Expert'),
    }
    return map[exp] || exp || '-'
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

.case-detail__qa-item {
    background: #f8fafc;
    border-left: 4px solid var(--color-denim-blue);
    transition: background 0.2s ease;
}

.case-detail__qa-item:hover {
    background: #f1f5f9;
}

.case-detail__qa-question {
    font-size: 0.95rem;
    color: #1e293b;
}

.case-detail__qa-answer {
    font-size: 0.9rem;
    line-height: 1.5;
}

.case-detail__standard-section {
    background-color: #f1f5f9;
    border: 1px solid #e2e8f0;
}

.case-detail__grid-label {
    display: block;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.35rem;
}

.case-detail__grid-value {
    font-size: 1.05rem;
    color: #1e293b;
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
