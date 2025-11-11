<template>
    <ul class="list-unstyled d-flex gap-5">
        <li v-for="(colors, petType) in pets" :key="petType" class="pet-search__item"
            :class="{ 'active': activePet === petType }" @click="changePetType(petType as string)">
            {{ petType }}
        </li>
    </ul>

    <PetFilter :pet-colors="pets[activePet]" @change:pet-filters="handlePetFiltersChange" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import PetFilter from '@/components/search/PetFilter.vue'
import { pets } from '@/../data/pets'
import { PetFiltersInter } from '@/types/option'

const emit = defineEmits<{
    (event: 'change:pet-type', value: string): void
    (event: 'change:pet-filters', filters: PetFiltersInter): void
}>()
const activePet = ref<string | number>('貓咪')

function changePetType(petType: string) {
    activePet.value = petType

    emit('change:pet-type', petType)
}

function handlePetFiltersChange(petFilters: PetFiltersInter) {
    emit('change:pet-filters', petFilters)
}
</script>

<style>
.pet-search__item {
    font-size: 1.4rem;
    font-weight: bold;
    color: #7f7f7f;
    cursor: pointer;
}

.pet-search__item.active {
    color: black;
}

.pet-search__item.active::after {
    content: '';
    display: block;
    width: 50%;
    height: 3px;
    background-color: black;
    margin: 4px auto 0;
}
</style>
