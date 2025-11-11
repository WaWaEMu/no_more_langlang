<template>
    <div class="pet-filter__wrapper d-flex justify-content-between">
        <ul class="list-unstyled d-flex flex-wrap w-75 gap-2">
            <DropdownButton :options="areas" v-model="petFilters.city">
                <template #label>
                    {{ petFilters.city || '縣市' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="petColors" v-model="petFilters.color">
                <template #label>
                    {{ petFilters.color || '花紋' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="furTypeOptions" v-model="petFilters.fur_type">
                <template #label>
                    {{ petFilters.fur_type || '毛型' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="genderOptions" v-model="petFilters.gender">
                <template #label>
                    {{ petFilters.gender || '性別' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="ageOptions" v-model="petFilters.age">
                <template #label>
                    {{ petFilters.age || '年紀' }}
                </template>
            </DropdownButton>

            <DropdownButton :options="isNeuterOptions" v-model="petFilters.is_neuter">
                <template #label>
                    {{ petFilters.is_neuter || '是否結紮' }}
                </template>
            </DropdownButton>

            <li class="d-flex align-items-center mx-3">
                <a href="#" class="pet-filter__reset underline" @click.prevent="resetPetFilters()">
                    重設篩選
                </a>
            </li>
        </ul>
        <form class="pet-filter__form">
            <div class="input-group border">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-0" placeholder="請輸入關鍵字">
                <button class="btn border-0">搜尋</button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import DropdownButton from '@/components/search/DropdownButton.vue'
import { areas } from '@/../data/areas'
import options from '@/../data/options'
import { PetFiltersInter } from '@/types/option'

const props = defineProps<{
    petColors: string[]
}>()

const emit = defineEmits<{
    (event: 'change:pet-filters', petFilters: PetFiltersInter): void
}>()

const { isStrayOptions, furTypeOptions, genderOptions, ageOptions, isNeuterOptions } = options

const petFilters = reactive<PetFiltersInter>({
    city: '',
    color: '',
    fur_type: '',
    gender: '',
    age: '',
    is_neuter: ''
})

watch(() => petFilters, (newVal) => {
    emit('change:pet-filters', newVal)
}, { deep: true, immediate: true })

function resetPetFilters() {
    for (const key in petFilters) {
        petFilters[key as keyof PetFiltersInter] = ''
    }
}
</script>

<style>
.pet-filter__reset {
    color: #7f7f7f;
}

.pet-filter__reset:hover {
    color: #000000;
}

.pet-filter__form .input-group {
    height: 38px;
    border-radius: 5%;
}

.pet-filter__form .input-group>* {
    font-size: 0.85rem;
}

.pet-filter__form button {
    color: #fff2cc;
    background-color: #3d3d5c;
    border-radius: 5% !important;
}

.form-control:focus {
    box-shadow: none;
    outline: none;
    border-color: #000;
}
</style>
