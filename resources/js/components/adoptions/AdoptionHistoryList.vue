<template>
    <div class="adoption-history-list">
        <div class="mb-3 text-muted" v-if="pets.length > 0">
            {{ $t('Total Adoption Cases', { count: pets.length.toString() }) }}
        </div>

        <div v-if="pets.length > 0" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <div class="col" v-for="pet in pets" :key="pet.id">
                <AdoptionCaseCard :pet="pet" />
            </div>
        </div>

        <!-- Empty State for History -->
        <div v-else class="adoption-history-list__empty text-center py-5 bg-light rounded-3 shadow-sm border">
            <div class="mb-3">
                <i class="bi bi-check-circle display-1 text-muted opacity-50"></i>
            </div>
            <h4 class="fw-bold text-secondary mb-2">{{ $t('No completed adoption cases') }}</h4>
            <p class="text-muted mb-0">{{ $t('No completed adoption cases desc') }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { PetInter } from '@/types/pet'
import AdoptionCaseCard from '@/components/adoptions/AdoptionCaseCard.vue'

defineProps<{
    pets: PetInter[]
}>()
</script>

<style scoped>
.adoption-history-list__empty {
    border-style: dashed !important;
}
</style>
