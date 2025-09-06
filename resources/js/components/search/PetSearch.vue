<template>
    <ul class="list-unstyled d-flex gap-5">
        <li v-for="(colors, petType) in pets" :key="petType" class="pet-search__item"
            :class="{ 'active': activePet === petType }" @click="activePet = petType">
            {{ petType }}
        </li>
    </ul>

    <PetFilter :pet-colors="pets[activePet]" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import PetFilter from '@/components/search/PetFilter.vue'
import { pets } from '@/../data/pets'

const activePet = ref<string | number>('貓咪')

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
