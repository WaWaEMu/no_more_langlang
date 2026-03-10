<template>
    <div class="card h-100 border-0 shadow-sm adoption-card">
        <div class="card-body">
            <!-- Card Header: Image & Basic Info -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                    <img :src="getPetImageUrl(pet)" class="rounded-circle object-fit-cover adoption-card__image"
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

            <!-- Footer Action -->
            <div class="d-flex justify-content-end mt-3">
                <RouterLink v-if="pet.adoption_case?.id" :to="`/case/${pet.adoption_case.id}`"
                    class="btn btn-primary btn-sm px-3 py-2 shadow-sm adoption-card__view-btn">
                    <i class="bi bi-info-circle me-1"></i>
                    {{ $t('View Details') }}
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { trans } from 'laravel-vue-i18n'
import { PetInter } from '@/types/pet'

interface Props {
    pet: PetInter
    role?: 'owner' | 'adopter'
}

withDefaults(defineProps<Props>(), {
    role: 'owner'
})

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
    width: 48px;
    height: 48px;
}

.adoption-card__subtitle {
    font-size: 0.8rem;
}

.adoption-card__tag {
    font-size: 0.75rem;
}

.adoption-card__tag--weekly {
    background-color: rgba(56, 161, 105, 0.1);
    color: #276749;
}

.adoption-card__tag--monthly {
    background-color: rgba(49, 130, 206, 0.1);
    color: #2b6cb0;
}

.adoption-card__tag--quarterly {
    background-color: rgba(159, 122, 234, 0.1);
    color: #6b46c1;
}

.adoption-card__tag--none {
    background-color: #f7fafc;
    color: #a0aec0;
}

.adoption-card__alert {
    background-color: rgba(49, 130, 206, 0.06);
    border: 1px solid rgba(49, 130, 206, 0.1);
}

.adoption-card__info-box {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
}

.adoption-card__view-btn {
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.adoption-card__view-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(44, 82, 130, 0.2);
}

@media (max-width: 576px) {
    .adoption-card__view-btn {
        width: 100%;
    }
}
</style>
