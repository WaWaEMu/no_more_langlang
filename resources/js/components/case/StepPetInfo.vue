<template>
    <div class="create-case__body">
        <h3 class="create-case__subtitle">{{ $t('case.PetInfo') }}</h3>
        <p class="create-case__hint">{{ $t('case.PetInfoHint') }}</p>

        <div class="create-case__form-group">
            <label class="create-case__label create-case__label--required">{{ $t('case.PetName') }}</label>
            <input type="text" class="form-control create-case__input" :value="modelValue.pet_name"
                @input="$emit('update:modelValue', { ...modelValue, pet_name: ($event.target as HTMLInputElement).value })"
                :placeholder="$t('case.PetNamePlaceholder')" maxlength="50" />
        </div>

        <div class="create-case__form-group">
            <label class="create-case__label create-case__label--required">{{ $t('case.PetType') }}</label>
            <div class="create-case__type-grid">
                <button type="button" v-for="typeOption in petTypes" :key="typeOption.value"
                    class="create-case__type-btn"
                    :class="{ 'create-case__type-btn--selected': modelValue.pet_type === typeOption.value }"
                    @click="$emit('update:modelValue', { ...modelValue, pet_type: typeOption.value })">
                    <i :class="typeOption.icon"></i>
                    <span>{{ $t(typeOption.label) }}</span>
                </button>
            </div>
        </div>

        <div class="create-case__form-group">
            <label class="create-case__label">{{ $t('case.PetImage') }}</label>
            <div class="create-case__upload" @click="fileInput?.click()" @dragover.prevent @drop.prevent="handleDrop">
                <input type="file" ref="fileInput" accept="image/*" class="d-none" @change="handleFileChange" />
                <div v-if="imagePreview" class="create-case__preview">
                    <img :src="imagePreview" alt="Preview" />
                    <button type="button" class="create-case__preview-remove" @click.stop="$emit('remove-image')">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div v-else class="create-case__upload-placeholder">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span>{{ $t('case.UploadHint') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
    modelValue: {
        pet_name: string
        pet_type: string
    }
    imagePreview: string | null
}>()

const emit = defineEmits(['update:modelValue', 'file-change', 'drop-file', 'remove-image'])

const fileInput = ref<HTMLInputElement | null>(null)

const petTypes = [
    { value: 'dog', label: 'case.TypeDog', icon: 'bi bi-emoji-smile' },
    { value: 'cat', label: 'case.TypeCat', icon: 'bi bi-emoji-heart-eyes' },
]

function handleFileChange(e: Event) {
    emit('file-change', e)
}

function handleDrop(e: DragEvent) {
    emit('drop-file', e)
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
    width: 100%;
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
    grid-template-columns: repeat(3, 1fr);
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

.create-case__upload {
    border: 2px dashed #cbd5e0;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f7fafc;
}

.create-case__upload:hover {
    border-color: var(--color-denim-blue);
    background: rgba(44, 82, 130, 0.02);
}

.create-case__upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: #a0aec0;
}

.create-case__upload-placeholder i {
    font-size: 2rem;
}

.create-case__preview {
    position: relative;
    display: inline-block;
}

.create-case__preview img {
    max-height: 180px;
    border-radius: 8px;
    object-fit: cover;
}

.create-case__preview-remove {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #e53e3e;
    color: white;
    border: none;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

@media (max-width: 576px) {
    .create-case__type-grid {
        grid-template-columns: 1fr;
    }

    .create-case__body {
        padding: 1.25rem;
    }
}
</style>
