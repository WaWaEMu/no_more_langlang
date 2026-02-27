<template>
    <div class="pet-filter__wrapper d-flex flex-column flex-lg-row justify-content-between gap-3">
        <ul class="list-unstyled d-flex flex-wrap gap-2 mb-0">
            <DropdownButton :options="areas" filterKey="city">
                <template #label>
                    {{ petFilters.city['label'] || '縣市' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="petColors" filterKey="color">
                <template #label>
                    {{ petFilters.color['label'] || '花紋' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="furTypeOptions" filterKey="fur_type">
                <template #label>
                    {{ petFilters.fur_type['label'] || '毛型' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="genderOptions" filterKey="gender">
                <template #label>
                    {{ petFilters.gender['label'] || '性別' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="ageOptions" filterKey="age">
                <template #label>
                    {{ petFilters.age['label'] || '年紀' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="isNeuterOptions" filterKey="is_neuter">
                <template #label>
                    {{ petFilters.is_neuter['label'] || '是否結紮' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="statusOptions" filterKey="status">
                <template #label>
                    {{ petFilters.status['label'] ? $t(petFilters.status['label']) : $t('Adoption Status') }}
                </template>
            </DropdownButton>

            <li class="d-flex align-items-center ms-2">
                <a href="#" class="pet-filter__reset underline" @click.prevent="resetFilters">
                    重設篩選
                </a>
            </li>
        </ul>
        <form class="pet-filter__form" @submit.prevent="fetchPets(1)">
            <div class="input-group border">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-0" placeholder="請輸入關鍵字" v-model.trim="keyword">
                <button type="submit" class="btn border-0">搜尋</button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import DropdownButton from '@/components/search/DropdownButton.vue'
import { areas } from '@/../data/areas'
import options from '@/../data/options'
import { useAdoptStore } from '@/stores/adopt'
import { storeToRefs } from 'pinia'

const props = defineProps<{
    petColors: string[]
}>()

const { isStrayOptions, furTypeOptions, genderOptions, ageOptions, isNeuterOptions, statusOptions } = options

const adoptStore = useAdoptStore()
const { petFilters, keyword } = storeToRefs(adoptStore)
const { resetFilters, fetchPets } = adoptStore
</script>

<style scoped>
.pet-filter__wrapper {
    position: relative;
    z-index: 10;
}

.pet-filter__reset {
    color: #6b7280;
    transition: color 0.2s ease;
}

.pet-filter__reset:hover {
    color: #3182ce;
}

.pet-filter__form .input-group {
    height: 38px;
    border-radius: 5%;
}

.pet-filter__form .input-group>* {
    font-size: 0.85rem;
}

.pet-filter__form button {
    color: #ffffff;
    background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%);
    border-radius: 5% !important;
    font-weight: 500;
    transition: all 0.2s ease;
}

.pet-filter__form button:hover {
    background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(44, 82, 130, 0.3);
}

.form-control:focus {
    box-shadow: none;
    outline: none;
    border-color: #3182ce;
}
</style>
