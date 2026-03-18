<template>
    <div class="create-case__body">
        <h3 class="create-case__subtitle">{{ $t('case.PetInfo') }}</h3>
        <p class="create-case__hint">{{ $t('case.PetInfoHint') }}</p>

        <!-- Section 1: Basic Info -->
        <div class="create-case__section-label">
            <i class="bi bi-info-circle me-1"></i>基本資料
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <label class="create-case__label create-case__label--required">{{ $t('case.PetName') }}</label>
                <input type="text" class="form-control create-case__input" :value="modelValue.pet_name"
                    @input="update({ pet_name: ($event.target as HTMLInputElement).value })"
                    :placeholder="$t('case.PetNamePlaceholder')" maxlength="50" />
            </div>
            <div class="col-md-12">
                <label class="create-case__label create-case__label--required">{{ $t('case.PetType') }}</label>
                <div class="create-case__type-grid">
                    <button type="button" v-for="typeOption in petTypes" :key="typeOption.value"
                        class="create-case__type-btn"
                        :class="{ 'create-case__type-btn--selected': modelValue.pet_type === typeOption.value }"
                        @click="update({ pet_type: typeOption.value, color: '' })">
                        <i :class="typeOption.icon"></i>
                        <span>{{ $t(typeOption.label) }}</span>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">性別</label>
                <select class="form-select create-case__input" :value="modelValue.gender"
                    @change="update({ gender: ($event.target as HTMLSelectElement).value })">
                    <option disabled value="">請選擇性別</option>
                    <option value="male">男生</option>
                    <option value="female">女生</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">年紀</label>
                <select class="form-select create-case__input" :value="modelValue.age"
                    @change="update({ age: ($event.target as HTMLSelectElement).value })">
                    <option disabled value="">請選擇年紀範圍</option>
                    <option value="0-1">0 ~ 1歲</option>
                    <option value="1-4">1 ~ 4歲</option>
                    <option value="4-7">4 ~ 7歲</option>
                    <option value="7-10">7 ~ 10歲</option>
                    <option value="10-13">10 ~ 13歲</option>
                    <option value="13-16">13 ~ 16歲</option>
                    <option value="16+">16歲 ~</option>
                </select>
            </div>
        </div>

        <!-- Section 2: Appearance -->
        <div class="create-case__section-label">
            <i class="bi bi-palette me-1"></i>外觀特徵
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">花紋</label>
                <select class="form-select create-case__input" :value="modelValue.color"
                    @change="update({ color: ($event.target as HTMLSelectElement).value })">
                    <option v-if="!modelValue.pet_type" disabled value="">請先選擇動物種類</option>
                    <option v-if="modelValue.pet_type" disabled value="">請選擇花紋</option>
                    <template v-if="modelValue.pet_type">
                        <option v-for="color in colorOptions" :key="color" :value="color">
                            {{ color }}
                        </option>
                    </template>
                </select>
            </div>
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">毛型</label>
                <select class="form-select create-case__input" :value="modelValue.fur_type"
                    @change="update({ fur_type: ($event.target as HTMLSelectElement).value })">
                    <option disabled value="">請選擇毛型</option>
                    <option value="短毛">短毛</option>
                    <option value="長毛">長毛</option>
                </select>
            </div>
        </div>

        <!-- Section 3: Status & Location -->
        <div class="create-case__section-label">
            <i class="bi bi-geo-alt me-1"></i>狀態與位置
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">結紮狀態</label>
                <select class="form-select create-case__input"
                    :value="modelValue.is_neuter === null ? '' : String(modelValue.is_neuter)"
                    @change="update({ is_neuter: ($event.target as HTMLSelectElement).value === 'true' })">
                    <option disabled value="">請選擇結紮狀態</option>
                    <option value="true">已結紮</option>
                    <option value="false">未結紮</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">是否為浪浪</label>
                <select class="form-select create-case__input"
                    :value="modelValue.is_stray === null ? '' : String(modelValue.is_stray)"
                    @change="update({ is_stray: ($event.target as HTMLSelectElement).value === 'true' })">
                    <option disabled value="">請選擇</option>
                    <option value="true">是</option>
                    <option value="false">否</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">所在縣市</label>
                <select class="form-select create-case__input" :value="modelValue.city"
                    @change="update({ city: ($event.target as HTMLSelectElement).value, town: '' })">
                    <option disabled value="">請選擇縣市</option>
                    <optgroup v-for="(cities, region) in areas" :key="region" :label="String(region)">
                        <option v-for="city in Object.keys(cities)" :key="city" :value="city">
                            {{ city }}
                        </option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-6">
                <label class="create-case__label create-case__label--required">鄉鎮區</label>
                <select class="form-select create-case__input" :value="modelValue.town"
                    @change="update({ town: ($event.target as HTMLSelectElement).value })">
                    <option v-if="!modelValue.city" disabled value="">請先選擇縣市</option>
                    <option v-if="modelValue.city" disabled value="">請選擇鄉鎮區</option>
                    <template v-if="modelValue.city">
                        <option v-for="town in districtsForCity" :key="town" :value="town">
                            {{ town }}
                        </option>
                    </template>
                </select>
            </div>
        </div>

        <!-- Pet Image Upload -->
        <div class="create-case__form-group">
            <label class="create-case__label">{{ $t('case.PetImage') }}</label>
            <p class="create-case__hint-small">第 1 張為封面照片，最多上傳 3 張。</p>
            <div class="d-flex gap-4 flex-wrap">
                <div v-for="(url, index) in imagePreviews" :key="index"
                    class="d-flex flex-column create-case__upload-card">
                    <div v-if="!url"
                        class="create-case__upload--placeholder d-flex justify-content-center align-items-center w-100 h-100">
                        <i class="bi bi-image text-secondary fs-1"></i>
                    </div>
                    <div v-else class="create-case__upload--img-container">
                        <img :src="url" class="create-case__upload--img">
                    </div>
                    <button type="button" @click="$emit('open-cropper', index)"
                        class="create-case__upload--btn btn fw-medium" data-bs-toggle="modal"
                        data-bs-target="#update-img-modal">
                        {{ url ? '更換圖片' : '上傳圖片' }}
                    </button>
                </div>
            </div>
            <div v-if="imageError" class="create-case__upload-error">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ imageError }}
            </div>
        </div>
    </div>
</template>


<script setup lang="ts">
import { ref, computed } from 'vue'
import { pets } from '@/../data/pets'
import { areas } from '@/../data/areas'

const props = defineProps<{
    modelValue: {
        pet_name: string
        pet_type: string
        gender: string
        age: string
        color: string
        fur_type: string
        is_neuter: boolean | null
        is_stray: boolean | null
        city: string
        town: string
    }
    imagePreviews: (string | null)[]
    imageError?: string | null
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', patch: Record<string, any>): void
    (e: 'open-cropper', idx: number): void
    (e: 'remove-image', idx: number): void
}>()

const petTypes = [
    { value: 'dog', label: 'dog', icon: 'bi bi-emoji-smile' },
    { value: 'cat', label: 'cat', icon: 'bi bi-emoji-heart-eyes' },
]

// Map pet_type value to Chinese label used in pets.ts
const typeToLabel: Record<string, string> = { dog: '狗狗', cat: '貓咪' }

const colorOptions = computed(() => {
    if (!props.modelValue.pet_type) return []
    const label = typeToLabel[props.modelValue.pet_type]
    return label ? pets[label] || [] : []
})

const districtsForCity = computed(() => {
    if (!props.modelValue.city) return []
    for (const region in areas) {
        if (props.modelValue.city in areas[region]) {
            return areas[region][props.modelValue.city]
        }
    }
    return []
})

function update(patch: Record<string, any>) {
    emit('update:modelValue', { ...props.modelValue, ...patch })
}
</script>
<style scoped>
.create-case__body {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
}

.create-case__subtitle {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.create-case__hint {
    color: #718096;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.create-case__section-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--color-denim-blue);
    margin-bottom: 0.75rem;
    padding-bottom: 0.4rem;
    border-bottom: 2px solid var(--color-denim-blue);
    display: inline-block;
}

.create-case__form-group {
    margin-bottom: 1.5rem;
}

.create-case__label {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.create-case__label--required::after {
    content: ' *';
    color: #e53e3e;
}

.create-case__input {
    border-radius: 10px;
    padding: 0.625rem 1rem;
    border: 1.5px solid #e2e8f0;
    transition: border-color 0.2s;
}

.create-case__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.1);
    outline: none;
}

.create-case__type-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.create-case__type-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    padding: 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: #f7fafc;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    font-weight: 500;
    color: #4a5568;
}

.create-case__type-btn i {
    font-size: 1.25rem;
}

.create-case__type-btn:hover {
    border-color: var(--color-denim-blue);
}

.create-case__type-btn--selected {
    border-color: var(--color-denim-blue);
    background: rgba(44, 82, 130, 0.04);
    color: var(--color-denim-blue);
    font-weight: 600;
}

.create-case__hint-small {
    font-size: 0.8rem;
    color: #a0aec0;
    margin-bottom: 0.75rem;
}

/* 3-slot image grid (matched with ApplyForm.vue) */
.create-case__upload-card {
    width: min(150px, 45vw);
    max-width: 100%;
}

.create-case__upload--placeholder {
    background-color: #fff;
    width: min(150px, 45vw);
    height: min(150px, 45vw);
    aspect-ratio: 1 / 1;
    border-radius: 6px 6px 0 0;
    border: 2px dashed #a0aec0;
    border-bottom: none;
}

.create-case__upload--img-container {
    position: relative;
    width: min(150px, 45vw);
    height: min(150px, 45vw);
}

.create-case__upload--img {
    width: 100%;
    height: 100%;
    border-radius: 6px 6px 0 0;
    aspect-ratio: 1 / 1;
    object-fit: cover;
}

.create-case__upload--btn {
    border: none;
    outline: none;
    transition: all 0.2s ease;
    font-weight: 500;
    letter-spacing: 0.025em;
    width: 100%;
    padding: 0.5rem;
    background-color: #4a5568;
    color: #ffffff;
    border: 1px solid #4a5568;
    border-top: none;
    border-radius: 0 0 6px 6px;
    font-size: 0.875rem;
    cursor: pointer;
}

.create-case__upload--btn:hover {
    background-color: var(--color-denim-blue-dark);
    color: #ffffff;
}



@media (max-width: 576px) {
    .create-case__type-grid {
        grid-template-columns: 1fr;
    }

    .create-case__body {
        padding: 1.25rem;
    }
}

.create-case__upload-error {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #e53e3e;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}
</style>
