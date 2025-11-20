<template>
    <li class="dropdown-button__list position-relative" @mouseenter="show = true" @mouseleave="show = false">
        <button class="btn w-100 border px-3 py-2" :class="{ 'dropdown-button__active': hasValue }">
            <slot name="label"></slot>
            <i class="bi bi-chevron-down"></i>
        </button>
        <FilterDropdown v-show="show" class="position-absolute" :options="options" :filterKey="filterKey" />
    </li>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import FilterDropdown from '@/components/search/FilterDropdown.vue'
import { OptionInter, PetFiltersInter } from '@/types/option'
import { AreaInter } from '@/../data/areas'
import { usePetStore } from '@/stores/petBrowse';

const props = defineProps<{
    options: AreaInter | string[] | OptionInter,
    filterKey: keyof PetFiltersInter,
}>()

const show = ref(false)
const petStore = usePetStore()
const { petFilters } = petStore

const hasValue = computed(() => {
    const val = petFilters[props.filterKey]?.value

    if (props.filterKey === 'is_neuter') {
        return typeof val === 'boolean' || val !== ''
    }

    return !!val
})
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

.dropdown-button__active {
    color: #000 !important;
}
</style>
