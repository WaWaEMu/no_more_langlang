<template>
    <transition name="fade">
        <div class="filter-dropdown__panel">
            <!-- 箭頭 -->
            <div class="filter-dropdown__arrow"></div>
            <div class="filter-dropdown__list mt-3 shadow-lg border rounded">

                <!-- 縣市選項 -->
                <template v-if="isCity(options)">
                    <ul v-for="(cities, area) in options" :key="area" class="list-unstyled">
                        <li class="py-2 rounded-md">
                            {{ area }}
                            <hr>
                            <ul v-for="(districts, city) in cities" class="list-unstyled">
                                <li class="filter-dropdown__option">
                                    {{ city }}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </template>

                <!-- 花紋選項 -->
                <template v-if="isPetColor(options)">
                    <ul class="list-unstyled">
                        <li v-for="(color, index) in options" :key="index"
                            class="filter-dropdown__option py-2 rounded-md">
                            {{ color }}
                        </li>
                    </ul>
                </template>

                <!-- 一般選項 -->
                <template v-if="isOption(options)">
                    <ul class="list-unstyled">
                        <template v-for="option in options" :key="String(option.value)">
                            <li v-if="!option.disabled" class="filter-dropdown__option py-2 rounded-md">
                                {{ option.label }}
                            </li>
                        </template>
                    </ul>
                </template>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
import { AreaInter } from '@/../data/areas'
import { PetColorMapInter } from '@/types/pet'
import { OptionInter, OptionItem } from '@/types/option'

type FilterOptionsInter = OptionItem[] | AreaInter | PetColorMapInter

defineProps<{
    options: FilterOptionsInter
}>()

function isCity(group: unknown): group is AreaInter {
    return typeof group === 'object'
        && group !== null
        && !Array.isArray(group)
        && Object.values(group).every(
            regionObj => typeof regionObj === 'object' && regionObj !== null
        )
}

function isPetColor(group: unknown): group is PetColorMapInter {
    return typeof group !== null
        && Array.isArray(group)
        && Object.values(group).every(
            color => typeof color === 'string'
        )
}

function isOption(group: unknown): group is OptionInter {
    return Array.isArray(group) && group.every(
        item => typeof item === 'object' &&
            item !== null &&
            'value' in item &&
            'label' in item
    )
}

</script>

<style scoped>
.filter-dropdown__panel {
    font-size: 0.85rem;
    position: relative;
    color: #7f7f7f;
    z-index: 1;
}

.filter-dropdown__panel hr {
    margin: 0.5rem 0;
    height: 2px;
    width: 75%;
    background-color: black;
}

.filter-dropdown__list {
    column-count: 2;
    background-color: white;
    padding-left: 30px;
    width: 275px;
}

.filter-dropdown__list>ul>li {
    break-inside: avoid;
}

.filter-dropdown__option {
    cursor: pointer;
    transition: color 0.15s ease;
}

.filter-dropdown__option:hover {
    color: #000000;
}

.filter-dropdown__arrow {
    position: absolute;
    top: 8px;
    left: 10%;
    transform: translateX(-50%) rotate(45deg);
    width: 15px;
    height: 15px;
    background: white;
    border-left: 1px solid #ddd;
    border-top: 1px solid #ddd;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
