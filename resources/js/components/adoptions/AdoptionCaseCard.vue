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
                            {{ pet.type }} / {{ pet.age }}
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
                    <span class="text-secondary small">{{ $t('Adopter') }}</span>
                    <span class="fw-semibold text-dark">{{ pet.adoption_case?.adopter.name }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary small">{{ $t('Adopted Date') }}</span>
                    <span class="text-dark small">{{ formatDate(pet.adoption_case?.started_at) }}</span>
                </div>
            </div>

            <!-- Expandable Content -->
            <div v-show="isExpanded" class="mt-3 pt-3 border-top adoption-card__extra">
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">案件編號</span>
                        <span class="text-dark small fw-medium">#{{ pet.adoption_case?.id }}</span>
                    </div>
                    <div v-if="pet.adoption_case?.tracking_config" class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">追蹤頻率</span>
                        <span class="text-dark small">{{ getTrackingFrequencyText(pet.adoption_case.tracking_config.frequency) }}</span>
                    </div>
                    <!-- Future: Last Report Date can go here -->
                </div>
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
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { trans } from 'laravel-vue-i18n'
import { PetInter } from '@/types/pet'

const props = defineProps<{
    pet: PetInter
}>()

const isExpanded = ref(false)

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

/* Tag Colors */
.adoption-card__tag--weekly {
    background-color: #bee3f8 !important;
    color: #2c5282 !important;
}

.adoption-card__tag--monthly {
    background-color: #feebc8 !important;
    color: #9c4221 !important;
}

.adoption-card__tag--quarterly {
    background-color: #c6f6d5 !important;
    color: #22543d !important;
}

.adoption-card__tag--none {
    background-color: #e2e8f0 !important;
    color: #4a5568 !important;
}

.adoption-card__extra {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
