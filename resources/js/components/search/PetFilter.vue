<template>
    <div class="pet-filter__wrapper d-flex justify-content-between">
        <ul class="list-unstyled d-flex flex-wrap w-75 gap-2">
            <DropdownButton :options="areas">
                <template #label>縣市</template>
            </DropdownButton>

            <DropdownButton :options="petColors">
                <template #label>花紋</template>
            </DropdownButton>

            <DropdownButton :options="furOptions">
                <template #label>毛型</template>
            </DropdownButton>

            <DropdownButton :options="genderOptions">
                <template #label>性別</template>
            </DropdownButton>

            <DropdownButton :options="ageOptions">
                <template #label>年紀</template>
            </DropdownButton>

            <DropdownButton :options="isNeuterOptions">
                <template #label>是否結紮</template>
            </DropdownButton>

            <li class="d-flex align-items-center mx-3">
                <a href="#" onclick="" class="pet-filter__reset underline">
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
import { ref } from 'vue'
import DropdownButton from '@/components/search/DropdownButton.vue'
import { areas } from '@/../data/areas'
import options from '@/../data/options'

const props = defineProps<{
    petColors: string[]
}>()

const { isStrayOptions, furOptions, genderOptions, ageOptions, isNeuterOptions } = options

const filters = ref()

const show = ref(false)

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
