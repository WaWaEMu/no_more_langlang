<template>
    <div class="app-select" v-click-outside="close">
        <div 
            class="app-select__trigger" 
            :class="[
                { 'app-select__trigger--active': isOpen || modelValue },
                `app-select__trigger--rounded-${rounded}`
            ]"
            @click="toggle"
        >
            <i v-if="icon" :class="[icon, 'app-select__icon']"></i>
            <span class="app-select__label">
                {{ modelValue || placeholder || $t('請選擇') }}
            </span>
            <i class="bi bi-chevron-down app-select__arrow" :class="{ 'app-select__arrow--open': isOpen }"></i>
        </div>

        <transition name="fade-slide">
            <div v-if="isOpen" class="app-select__dropdown">
                <div 
                    v-if="allowClear"
                    class="app-select__option" 
                    :class="{ 'app-select__option--active': !modelValue }"
                    @click="select('')"
                >
                    {{ clearLabel || $t('全部') }}
                </div>
                <div 
                    v-for="option in options" 
                    :key="typeof option === 'string' ? option : option.value" 
                    class="app-select__option"
                    :class="{ 'app-select__option--active': (typeof option === 'string' ? option : option.value) === modelValue }"
                    @click="select(typeof option === 'string' ? option : option.value)"
                >
                    {{ typeof option === 'string' ? option : (option.label || option.value) }}
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Option {
    value: string | number
    label?: string
}

const props = withDefaults(defineProps<{
    modelValue: string | number
    options: readonly string[] | string[] | Option[]
    placeholder?: string
    icon?: string
    rounded?: 'sharp' | 'pill' | 'standard'
    allowClear?: boolean
    clearLabel?: string
}>(), {
    rounded: 'standard',
    allowClear: true
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void
    (e: 'change', value: string | number): void
}>()

const isOpen = ref(false)

const toggle = () => {
    isOpen.value = !isOpen.value
}

const close = () => {
    isOpen.value = false
}

const select = (value: string | number) => {
    emit('update:modelValue', value)
    emit('change', value)
    close()
}

// Click outside directive logic
const vClickOutside = {
    mounted(el: any, binding: any) {
        el.clickOutsideEvent = (event: Event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value()
            }
        }
        document.addEventListener('click', el.clickOutsideEvent)
    },
    unmounted(el: any) {
        document.removeEventListener('click', el.clickOutsideEvent)
    },
}
</script>

<style scoped>
.app-select {
    position: relative;
    user-select: none;
}

.app-select__trigger {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.45rem 1rem;
    background: #fff;
    border: 1.5px solid #cbd5e1;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 140px;
}

/* Rounded variations */
.app-select__trigger--rounded-sharp { border-radius: 3px; }
.app-select__trigger--rounded-standard { border-radius: 8px; }
.app-select__trigger--rounded-pill { border-radius: 50px; }

.app-select__trigger:hover {
    border-color: #94a3b8;
    background-color: #f8fafc;
}

.app-select__trigger--active {
    border-color: var(--color-denim-blue) !important;
    background-color: #fff !important;
}

.app-select__icon {
    color: #94a3b8;
    font-size: 0.9rem;
}

.app-select__trigger--active .app-select__icon {
    color: var(--color-denim-blue);
}

.app-select__label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1a202c;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.app-select__arrow {
    font-size: 0.7rem;
    color: #94a3b8;
    transition: transform 0.3s ease;
}

.app-select__arrow--open {
    transform: rotate(180deg);
}

/* Dropdown */
.app-select__dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 100%;
    min-width: 180px;
    max-height: 250px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    overflow-y: auto;
    padding: 0.4rem;
}

.app-select__dropdown::-webkit-scrollbar {
    width: 4px;
}

.app-select__dropdown::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.app-select__option {
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 500;
    color: #475569;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.app-select__option:hover {
    background-color: #f1f5f9;
    color: #1a202c;
}

.app-select__option--active {
    background-color: #ebf4ff;
    color: var(--color-denim-blue);
    font-weight: 700;
}

/* Animations */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.2s ease-out;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
