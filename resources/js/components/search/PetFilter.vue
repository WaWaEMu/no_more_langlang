<template>
    <div class="pet-filter__wrapper d-flex justify-content-between">
        <ul class="pet-filter__list list-unstyled d-flex flex-wrap w-75 gap-2">
            <li>
                <button class="btn w-100 border px-3 py-2">
                    <span>縣市</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </li>
            <li>
                <button class="btn w-100 border px-3 py-2">
                    <span>花紋</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </li>
            <li>
                <button class="btn w-100 border px-3 py-2">
                    <span>毛型</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </li>
            <li>
                <button class="btn w-100 border px-3 py-2">
                    <span>性別</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </li>
            <li>
                <button class="btn w-100 border px-3 py-2">
                    <span>年紀</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </li>
            <li>
                <button class="btn w-100 border px-3 py-2">
                    <span>是否結紮</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </li>
            <li class="d-flex align-items-center mx-3">
                <a href="#" onclick="" class="underline">
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

const props = defineProps<{
    petColors: string[]
}>()

const filters = ref()

</script>

<style>
.pet-filter__list li {
    min-width: 130px;
}

.pet-filter__list li button {
    font-size: 0.85rem;
    border-radius: 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #7f7f7f;
}

.pet-filter__list li a {
    color: #7f7f7f;
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
