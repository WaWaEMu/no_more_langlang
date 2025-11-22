<template>
    <ul class="list-unstyled d-flex gap-5">
        <li v-for="(colors, type) in pets" :key="type" class="pet-search__item"
            :class="{ 'active': activeType === type }" @click="changeType(type as string)">
            {{ type }}
        </li>
    </ul>

    <PetFilter :pet-colors="pets[activeType]" />
</template>

<script setup lang="ts">
import PetFilter from '@/components/search/PetFilter.vue'
import { pets } from '@/../data/pets'
import { usePetStore } from '@/stores/adopt'
import { storeToRefs } from 'pinia'

const petStore = usePetStore()
const { activeType } = storeToRefs(petStore)
const { changeType } = petStore
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
