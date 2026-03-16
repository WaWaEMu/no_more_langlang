<template>
    <div>
        <!-- Info Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Case Info -->
            <div class="col-md-6">
                <div class="case-overview__card h-100">
                    <h5 class="case-overview__card-title">
                        <i class="bi bi-clipboard-data me-2"></i>{{ $t('caseDetail.CaseInfo') }}
                    </h5>
                    <div class="case-overview__info-grid">
                        <div class="case-overview__info-row">
                            <span class="case-overview__info-key">{{ $t('caseDetail.CaseId') }}</span>
                            <span class="case-overview__info-val">#{{ adoptionCase.id }}</span>
                        </div>
                        <div class="case-overview__info-row">
                            <span class="case-overview__info-key">{{ $t('caseDetail.Source') }}</span>
                            <span class="case-overview__info-val">
                                {{ adoptionCase.source === 'manual' ? $t('caseDetail.SourceManual') :
                                    $t('caseDetail.SourcePlatform') }}
                            </span>
                        </div>
                        <div class="case-overview__info-row">
                            <span class="case-overview__info-key">{{ $t('caseDetail.TrackingFrequency') }}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="case-overview__info-val">
                                    {{ getTrackingFrequencyFullText(adoptionCase.tracking_config) }}
                                </span>
                                <button v-if="role === 'owner' && adoptionCase.status === 'active'"
                                    class="btn btn-sm btn-outline-primary py-0 px-2" @click="$emit('edit-tracking')">
                                    {{ $t('Edit') }}
                                </button>
                            </div>
                        </div>
                        <div class="case-overview__info-row">
                            <span class="case-overview__info-key">{{ $t('Adopted Date') }}</span>
                            <span class="case-overview__info-val">{{ formatDate(adoptionCase.started_at) }}</span>
                        </div>
                        <div v-if="adoptionCase.next_report_due_at" class="case-overview__info-row">
                            <span class="case-overview__info-key">{{ $t('Next Report Due') }}</span>
                            <span class="case-overview__info-val text-primary fw-semibold">
                                {{ formatDate(adoptionCase.next_report_due_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- Delete Actions -->
                    <div class="mt-4 pt-3 border-top text-end">
                        <button class="btn btn-link text-danger text-decoration-none p-0 small" @click="confirmDelete">
                            <i class="bi bi-trash3 me-1"></i>{{ $t('caseDetail.DeleteCase') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- People Info -->
            <div class="col-md-6">
                <div class="case-overview__card h-100">
                    <h5 class="case-overview__card-title">
                        <i class="bi bi-people me-2"></i>{{ $t('caseDetail.People') }}
                    </h5>
                    <div class="case-overview__info-grid">
                        <div class="case-overview__info-row">
                            <span class="case-overview__info-key">{{ $t('Owner') }}</span>
                            <span class="case-overview__info-val">
                                {{ adoptionCase.owner?.name || $t('caseDetail.NotSpecified') }}
                            </span>
                        </div>
                        <div class="case-overview__info-row">
                            <span class="case-overview__info-key">{{ $t('Adopter') }}</span>
                            <span class="case-overview__info-val">
                                {{ adoptionCase.adopter?.name || $t('caseDetail.NotSpecified') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adoption Application (Collapsible Accordion) -->
        <div v-if="application" class="case-overview__card mb-4">
            <div class="case-overview__accordion" data-bs-toggle="collapse" data-bs-target="#applicationCollapse"
                aria-expanded="false" aria-controls="applicationCollapse">
                <h5 class="case-overview__card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>{{ $t('caseDetail.ApplicationForm') }}
                </h5>
                <i class="bi bi-chevron-down case-overview__accordion-icon"></i>
            </div>

            <div id="applicationCollapse" class="collapse">
                <div class="pt-3">
                    <!-- Standard Fields -->
                    <div class="case-overview__standard-section p-3 rounded-4 mb-3">
                        <h6 class="fw-bold text-secondary mb-3 small text-uppercase letter-spacing-1">
                            <i class="bi bi-info-square me-1"></i>基本申請內容
                        </h6>
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <div class="case-overview__grid-item">
                                    <span class="case-overview__grid-label">{{ $t('caseDetail.ApplicantName') }}</span>
                                    <span class="case-overview__grid-value">{{ application.name }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="case-overview__grid-item">
                                    <span class="case-overview__grid-label">{{ $t('caseDetail.Phone') }}</span>
                                    <span class="case-overview__grid-value">{{ application.phone || '-' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="case-overview__grid-item">
                                    <span class="case-overview__grid-label">LINE ID</span>
                                    <span class="case-overview__grid-value">{{ application.line_id || '-' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="case-overview__grid-item">
                                    <span class="case-overview__grid-label">{{ $t('caseDetail.Experience') }}</span>
                                    <span class="case-overview__grid-value">{{ formatExperience(application.experience)
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="case-overview__grid-item">
                                    <span class="case-overview__grid-label">{{ $t('caseDetail.FamilyAgreement')
                                    }}</span>
                                    <span class="case-overview__grid-value">
                                        <i v-if="application.family_agreement"
                                            class="bi bi-check-circle-fill text-success me-1"></i>
                                        <i v-else class="bi bi-x-circle-fill text-danger me-1"></i>
                                        {{ application.family_agreement ? '是' : '否' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-if="application.message" class="mt-3 pt-3 border-top border-light">
                            <span class="case-overview__grid-label mb-1 d-block">{{ $t('caseDetail.Message') }}</span>
                            <div class="case-overview__grid-value text-break"
                                style="font-style: italic; color: #4a5568;">
                                「{{ application.message }}」
                            </div>
                        </div>
                    </div>

                    <!-- Custom Fields (Q&A Style) -->
                    <div v-if="application.custom_fields && Object.keys(application.custom_fields).length > 0"
                        class="case-overview__custom-section mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-patch-question text-primary me-2"></i>{{ $t('caseDetail.CustomFields') }}
                        </h6>
                        <div class="case-overview__qa-list">
                            <div v-for="(value, key) in application.custom_fields" :key="key"
                                class="case-overview__qa-item mb-3 p-3 rounded-3">
                                <div class="case-overview__qa-question fw-bold mb-2">
                                    <span class="text-primary me-2">Q.</span>{{ key }}
                                </div>
                                <div class="case-overview__qa-answer text-secondary">
                                    <span class="fw-bold me-2">A.</span>{{ formatFieldValue(value) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { trans } from 'laravel-vue-i18n'
import Swal from 'sweetalert2'
import axios from 'axios'
import { useRouter } from 'vue-router'

const $t = trans
const router = useRouter()

interface Props {
    adoptionCase: any
    application: any
    role: 'owner' | 'adopter'
    currentUserId?: number
}

const props = defineProps<Props>()

async function confirmDelete() {
    // Permission check: only pet creator (original uploader) can delete the case
    const isCreator = props.currentUserId === props.adoptionCase.pet?.user_id

    if (!isCreator) {
        await Swal.fire({
            icon: 'info',
            title: $t('caseDetail.DeleteCase'),
            text: $t('caseDetail.OnlyCreatorCanDelete'),
            confirmButtonText: $t('Got it'),
            confirmButtonColor: 'var(--color-denim-blue)',
        })
        return
    }

    const result = await Swal.fire({
        icon: 'warning',
        title: $t('caseDetail.DeleteConfirm'),
        text: $t('caseDetail.DeleteWarning'),
        showCancelButton: true,
        confirmButtonText: $t('Delete'),
        cancelButtonText: $t('Cancel'),
        confirmButtonColor: '#e53e3e',
        cancelButtonColor: '#94a3b8',
    })

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/adoption-cases/${props.adoptionCase.id}`)
            await Swal.fire({
                icon: 'success',
                title: $t('caseDetail.DeleteSuccess'),
                timer: 1500,
                showConfirmButton: false,
            })
            router.push('/user/adoptions')
        } catch (error) {
            console.error('Failed to delete case:', error)
            Swal.fire({
                icon: 'error',
                title: $t('caseDetail.DeleteError'),
                confirmButtonText: $t('Got it'),
            })
        }
    }
}

function getTrackingFrequencyText(frequency?: string) {
    if (!frequency) return $t('No Tracking')
    const texts: Record<string, string> = {
        'weekly': $t('Weekly'),
        'monthly': $t('Monthly'),
        'quarterly': $t('Quarterly'),
        'none': $t('case.FreqNone'),
    }
    return texts[frequency] || frequency
}

function getTrackingFrequencyFullText(config: any) {
    if (!config?.frequency) return $t('No Tracking')

    const base = getTrackingFrequencyText(config.frequency)

    if (config.frequency === 'weekly' && config.tracking_day) {
        return `${base} (${$t(`case.Weekday.${config.tracking_day}`)})`
    }

    if (config.frequency === 'monthly' && config.tracking_day) {
        return `${base} (${config.tracking_day} 日)`
    }

    if (config.frequency === 'quarterly' && config.tracking_day && config.tracking_start_month) {
        const months = getQuarterlyCycleMonths(config.tracking_start_month)
        return `${base} (${months} ${config.tracking_day} 日)`
    }

    return base
}

function getQuarterlyCycleMonths(startMonth: number): string {
    const months = []
    for (let i = 0; i < 4; i++) {
        let m = startMonth + (i * 3)
        if (m > 12) m -= 12
        months.push(m)
    }
    return months.sort((a, b) => a - b).join('、') + ' 月'
}

function formatDate(dateStr?: string) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString()
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
</script>

<style scoped>
.case-overview__card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.case-overview__card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-denim-blue);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-denim-blue);
    display: inline-block;
}

.case-overview__accordion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.25rem 0;
    transition: all 0.2s ease;
}

.case-overview__accordion:hover {
    opacity: 0.8;
}

.case-overview__accordion-icon {
    font-size: 1.1rem;
    color: #94a3b8;
    transition: transform 0.3s ease;
}

[aria-expanded="true"]>.case-overview__accordion-icon {
    transform: rotate(180deg);
}

.case-overview__info-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.case-overview__info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.case-overview__info-row:last-child {
    border-bottom: none;
}

.case-overview__info-key {
    font-size: 0.875rem;
    color: #718096;
    font-weight: 500;
}

.case-overview__info-val {
    font-size: 0.875rem;
    color: #2d3748;
    font-weight: 600;
}

.case-overview__standard-section {
    background-color: #f1f5f9;
    border: 1px solid #e2e8f0;
}

.case-overview__grid-label {
    display: block;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.35rem;
}

.case-overview__grid-value {
    font-size: 1.05rem;
    color: #1e293b;
    font-weight: 600;
}

.case-overview__qa-item {
    background: #f8fafc;
    border-left: 4px solid var(--color-denim-blue);
    transition: background 0.2s ease;
}

.case-overview__qa-item:hover {
    background: #f1f5f9;
}

.case-overview__qa-question {
    font-size: 0.95rem;
    color: #1e293b;
}

.case-overview__qa-answer {
    font-size: 0.9rem;
    line-height: 1.5;
}
</style>
