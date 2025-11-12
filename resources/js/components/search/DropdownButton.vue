<template>
    <li class="dropdown-button__list position-relative" @mouseenter="show = true" @mouseleave="show = false">
        <button class="btn w-100 border px-3 py-2" :class="{ 'dropdown-button__active': hasValue }">
            <slot name="label"></slot>
            <i class="bi bi-chevron-down"></i>
        </button>
        <FilterDropdown v-show="show" class="position-absolute" :options="options"
            @update:pet-filters="savePetFilters" />
    </li>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import FilterDropdown from '@/components/search/FilterDropdown.vue'
import { OptionInter, OptionItem } from '@/types/option'
import { AreaInter } from '@/../data/areas'

const props = defineProps<{
    options: AreaInter | string[] | OptionInter,
    modelValue: OptionItem,
}>()

const emit = defineEmits<{
    (event: 'update:modelValue', payload: { label: string, value: string | boolean }): void
}>()

const show = ref(false)

const hasValue = computed(() => !!props.modelValue?.value)

function savePetFilters(payload: { label: string, value: string | boolean }) {
    emit('update:modelValue', payload)
}
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
