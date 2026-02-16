<template>
    <div class="adoption-active-list">
        <div class="mb-3 text-muted" v-if="pets.length > 0">
            {{ $t('Total Adoption Cases', { count: pets.length.toString() }) }}
        </div>

        <div v-if="pets.length > 0">
            <PetList :pet-list="pets" :editable="true" />
        </div>

        <!-- Empty State for Active -->
        <div v-else class="adoption-active-list__empty text-center py-5 bg-light rounded-3 shadow-sm border">
            <div class="mb-3">
                <i class="bi bi-box2-heart display-1 text-muted opacity-50"></i>
            </div>
            <h4 class="fw-bold text-secondary mb-2">{{ $t('No active adoption cases') }}</h4>
            <p class="text-muted mb-4">{{ $t('No active adoption cases desc') }}</p>
            <RouterLink to="/adopt/new" class="btn btn-primary px-4">
                <i class="bi bi-plus-lg me-1"></i>
                {{ $t('Create Adoption Now') }}
            </RouterLink>
        </div>
    </div>
</template>

<script setup lang="ts">
import { PetInter } from '@/types/pet'
import PetList from '@/components/adopt/PetList.vue'

defineProps<{
    pets: PetInter[]
}>()
</script>

<style scoped>
.adoption-active-list__empty {
    border-style: dashed !important;
}
</style>
