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
                                <li class="filter-dropdown__option"
                                    @click="updatePetFilters(props.filterKey, city as string)">
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
                            class="filter-dropdown__option py-2 rounded-md"
                            @click="updatePetFilters(props.filterKey, color as string)">
                            {{ color }}
                        </li>
                    </ul>
                </template>

                <!-- 一般選項 -->
                <template v-if="isOption(options)">
                    <ul class="list-unstyled">
                        <template v-for="item in options.items" :key="String(item.value)">
                            <li v-if="!item.disabled" class="filter-dropdown__option py-2 rounded-md"
                                @click="updatePetFilters(props.filterKey, item)">
                                {{ item.label }}
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
import { OptionInter, OptionItem, PetFiltersInter } from '@/types/option'
import { useAdoptStore } from '@/stores/adopt';

const props = defineProps<{
    options: AreaInter | string[] | OptionInter,
    filterKey: keyof PetFiltersInter,
}>()

const adoptStore = useAdoptStore()
const { updateFilters } = adoptStore

function isCity(group: unknown): group is AreaInter {
    return typeof group === 'object'
        && group !== null
        && !Array.isArray(group)
        && Object.values(group).every(
            regionObj => typeof regionObj === 'object' && regionObj !== null
        )
}

function isPetColor(group: unknown): group is string[] {
    return Array.isArray(group) && group.every(color => typeof color === 'string')
}

function isOption(group: unknown): group is OptionInter {
    return (
        typeof group === 'object' &&
        group !== null &&
        'key' in group &&
        'items' in group &&
        Array.isArray((group as OptionInter).items)
    )
}

function updatePetFilters(key: keyof PetFiltersInter, item: OptionItem | string) {
    let normalized: OptionItem

    if (key === 'city' || key === 'color') {
        normalized = {
            'label': item as string,
            'value': item as string
        }
    } else {
        normalized = item as OptionItem
    }

    updateFilters(key, normalized)
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
