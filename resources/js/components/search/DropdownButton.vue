<template>
    <li class="dropdown-button__list position-relative" @mouseenter="show = true" @mouseleave="show = false">
        <button class="btn w-100 border px-3 py-2">
            <slot name="label"></slot>
            <i class="bi bi-chevron-down"></i>
        </button>
        <FilterDropdown v-show="show" class="position-absolute" :options="options" />
    </li>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import FilterDropdown from '@/components/search/FilterDropdown.vue'

defineProps(['options'])

const show = ref(false)
</script>

<style scoped>
.dropdown-button__list {
    min-width: 130px;
}

.dropdown-button__list button {
    font-size: 0.85rem;
    border-radius: 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #7f7f7f;
}

.dropdown-button__list button:focus {
    color: #7f7f7f;
}

</style>
