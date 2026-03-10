<template>
    <div class="card h-100 border-0 shadow-sm adoption-card" :class="{ 'adoption-card--expanded': isExpanded }">
        <div class="card-body">
            <!-- Card Header: Image & Basic Info -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                    <img :src="getPetImageUrl(pet)" 
                        class="rounded-circle object-fit-cover adoption-card__image" 
                        :alt="pet.name">
                    <div class="ms-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">{{ pet.name }}</h5>
                        <small class="text-muted fw-semibold adoption-card__subtitle">
                            {{ $t(pet.type.toLowerCase()) }} / {{ pet.age }}
                        </small>
                    </div>
                </div>
                <!-- Status Tag -->
                <div class="d-flex flex-column align-items-end">
                    <span v-if="pet.adoption_case?.tracking_config" 
                        class="badge rounded-pill px-3 py-2 adoption-card__tag"
                        :class="getTrackingBadgeClass(pet.adoption_case.tracking_config.frequency)">
                        {{ getTrackingFrequencyText(pet.adoption_case.tracking_config.frequency) }}
                    </span>
                    <span v-else class="badge adoption-card__tag adoption-card__tag--none rounded-pill p-2">
                        {{ $t('No Tracking') }}
                    </span>
                </div>
            </div>

            <!-- Report Alert -->
            <div v-if="pet.adoption_case?.next_report_due_at" 
                class="alert adoption-card__alert py-2 px-3 mb-3 d-flex align-items-center gap-2 rounded-3 shadow-sm">
                <i class="bi bi-calendar-event text-primary"></i>
                <small class="text-dark">
                    <strong class="text-primary">{{ $t('Next Report Due') }}:</strong> 
                    {{ formatDate(pet.adoption_case.next_report_due_at) }}
                </small>
            </div>
            
            <!-- Adoption Base Info -->
            <div class="adoption-card__info-box rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small">{{ role === 'adopter' ? $t('Owner') : $t('Adopter') }}</span>
                    <span class="fw-semibold text-dark">
                        {{ role === 'adopter' ? pet.adoption_case?.owner?.name : pet.adoption_case?.adopter?.name }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary small">{{ $t('Adopted Date') }}</span>
                    <span class="text-dark small">{{ formatDate(pet.adoption_case?.started_at) }}</span>
                </div>
            </div>

            <!-- Expandable Content -->
            <div v-show="isExpanded" class="mt-3 pt-3 border-top adoption-card__extra">
                <div class="d-flex flex-column gap-2 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">案件編號</span>
                        <span class="text-dark small fw-medium">#{{ pet.adoption_case?.id }}</span>
                    </div>
                    <div v-if="pet.adoption_case?.tracking_config" class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">追蹤頻率</span>
                        <span class="text-dark small">{{ getTrackingFrequencyText(pet.adoption_case.tracking_config.frequency) }}</span>
                    </div>
                </div>

                <!-- Submit Report Button (Adopter only, active cases only) -->
                <button v-if="role === 'adopter' && pet.adoption_case?.status === 'active' && pet.adoption_case?.tracking_config"
                    class="btn btn-outline-primary btn-sm w-100 mb-3 adoption-card__report-btn"
                    data-bs-toggle="modal" data-bs-target="#trackingReportModal"
                    @click="prepareReportForm">
                    <i class="bi bi-pencil-square me-1"></i>
                    {{ $t('Submit Report') }}
                </button>

                <!-- Tracking Report Timeline -->
                <TrackingReportTimeline 
                    v-if="pet.adoption_case?.id"
                    :case-id="pet.adoption_case.id"
                    :refresh-key="reportRefreshKey" />
            </div>

            <!-- Footer Action -->
            <div class="d-flex justify-content-end mt-3">
                <button @click="isExpanded = !isExpanded" 
                    class="btn btn-primary btn-sm px-3 py-2 shadow-sm adoption-card__view-btn">
                    <i class="bi me-1" :class="isExpanded ? 'bi-chevron-up' : 'bi-info-circle'"></i>
                    {{ isExpanded ? '收合詳情' : $t('View Details') }}
                </button>
            </div>
        </div>

        <!-- Report Form Modal -->
        <TrackingReportForm 
            v-if="role === 'adopter' && pet.adoption_case?.id"
            :case-id="pet.adoption_case.id"
            :pet-name="pet.name"
            :pet-image="getPetImageUrl(pet)"
            @submitted="onReportSubmitted" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { trans } from 'laravel-vue-i18n'
import { PetInter } from '@/types/pet'
import TrackingReportForm from './TrackingReportForm.vue'
import TrackingReportTimeline from './TrackingReportTimeline.vue'

interface Props {
    pet: PetInter
    role?: 'owner' | 'adopter'
}

const props = withDefaults(defineProps<Props>(), {
    role: 'owner'
})

const isExpanded = ref(false)
const reportRefreshKey = ref(0)

function prepareReportForm() {
    // This method is called when the "Submit Report" button is clicked
    // The modal is opened via Bootstrap's data-bs-toggle
}

function onReportSubmitted() {
    // Increment the refresh key to trigger a re-fetch of reports in the timeline
    reportRefreshKey.value++
}

function getTrackingBadgeClass(frequency: string) {
    const classes = {
        'weekly': 'adoption-card__tag--weekly',
        'monthly': 'adoption-card__tag--monthly',
        'quarterly': 'adoption-card__tag--quarterly'
    }
    return classes[frequency as keyof typeof classes] || 'adoption-card__tag--none'
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

function formatDate(dateStr: string | undefined) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString()
}
</script>

<style scoped>
.adoption-card {
    transition: all 0.3s ease;
}

.adoption-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

.adoption-card__image {
    width: 50px;
    height: 50px;
}

.adoption-card__subtitle {
    font-size: 0.85rem;
}

.adoption-card__tag {
    font-size: 0.85rem;
}

.adoption-card__alert {
    background-color: var(--color-fog-gray);
    border: none;
    border-left: 4px solid var(--color-denim-blue) !important;
}

.adoption-card__info-box {
    background-color: var(--color-fog-gray);
    border: 1px solid #dee2e6;
}

.adoption-card__view-btn {
    transition: all 0.3s ease;
}

.adoption-card__view-btn:hover {
    transform: translateY(-2px);
}

.adoption-card__report-btn {
    border-color: var(--color-denim-blue);
    color: var(--color-denim-blue);
    transition: all 0.3s ease;
    font-weight: 600;
}

.adoption-card__report-btn:hover {
    background-color: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(44, 82, 130, 0.2);
}

/* Tag Colors */
.adoption-card__tag--weekly {
    background-color: rgba(44, 82, 130, 0.1) !important;
    color: var(--color-denim-blue) !important;
}

.adoption-card__tag--monthly {
    background-color: rgba(237, 137, 54, 0.1) !important;
    color: #c05621 !important;
}

.adoption-card__tag--quarterly {
    background-color: rgba(72, 187, 120, 0.1) !important;
    color: #2f855a !important;
}

.adoption-card__tag--none {
    background-color: var(--color-fog-gray) !important;
    color: #718096 !important;
}

.adoption-card__extra {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
