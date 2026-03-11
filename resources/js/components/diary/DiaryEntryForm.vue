<template>
    <div class="modal fade" id="diaryEntryModal" tabindex="-1" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow diary-form">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-camera me-2 text-primary"></i>{{ $t('diary.Title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Pet Info Header -->
                    <div class="d-flex align-items-center mb-3 p-2 rounded-3 diary-form__pet-info">
                        <img :src="petImage || '/images/default-pet.png'" class="rounded-circle object-fit-cover"
                            width="40" height="40" :alt="petName">
                        <span class="ms-2 fw-semibold text-dark">{{ petName }}</span>
                    </div>

                    <!-- Photo Upload (1~5) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">
                            📸 {{ $t('diary.PhotoLabel') }}
                            <span class="text-danger">*</span>
                            <span class="text-muted fw-normal ms-1">({{ photoFiles.length }}/5)</span>
                        </label>
                        <div class="diary-form__photo-grid d-flex gap-2 flex-wrap">
                            <!-- Previews -->
                            <div v-for="(preview, index) in photoPreviews" :key="index"
                                class="diary-form__photo-item position-relative rounded-3 overflow-hidden">
                                <img :src="preview" class="diary-form__photo-thumb">
                                <button @click="removePhoto(index)" type="button"
                                    class="btn btn-sm position-absolute top-0 end-0 m-1 diary-form__remove-btn">
                                    <i class="bi bi-x-circle-fill text-white"></i>
                                </button>
                            </div>
                            <!-- Upload Button -->
                            <label v-if="photoPreviews.length < 5"
                                class="diary-form__upload-btn d-flex flex-column align-items-center justify-content-center rounded-3">
                                <input type="file" accept="image/*" multiple @change="onFileChange" class="d-none">
                                <i class="bi bi-plus-lg text-muted fs-4"></i>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ $t('diary.PhotoHint') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Location (Optional) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">
                            📍 {{ $t('diary.LocationLabel') }}
                        </label>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            <button v-for="loc in quickLocations" :key="loc" type="button"
                                class="diary-form__loc-btn px-3 py-1 rounded-pill border-0 small"
                                :class="{ 'diary-form__loc-btn--active': location === loc }"
                                @click="location = location === loc ? '' : loc">
                                {{ loc }}
                            </button>
                        </div>
                        <input v-model="location" type="text" class="form-control form-control-sm rounded-3"
                            :placeholder="$t('diary.LocationPlaceholder')" maxlength="50">
                    </div>

                    <!-- Content (Optional) -->
                    <div class="mb-1">
                        <label class="form-label fw-semibold small text-secondary">
                            ✏️ {{ $t('diary.ContentLabel') }}
                        </label>
                        <textarea v-model="content" class="form-control diary-form__textarea rounded-3"
                            :placeholder="$t('diary.ContentPlaceholder')" rows="2" maxlength="200"></textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <small class="text-muted">{{ content.length }} / 200</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="submitDiary" type="button"
                        class="btn btn-primary px-4 shadow-sm diary-form__submit-btn"
                        :disabled="submitting || photoFiles.length === 0">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>
                        {{ submitting ? $t('Sending...') : $t('diary.Submit') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { Modal } from 'bootstrap'
import { trans } from 'laravel-vue-i18n'

const $t = trans

interface Props {
    caseId: number
    petName: string
    petImage?: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
    (e: 'submitted'): void
}>()

const content = ref('')
const location = ref('')
const photoFiles = ref<File[]>([])
const photoPreviews = ref<string[]>([])
const submitting = ref(false)
const modalRef = ref<HTMLElement | null>(null)

const quickLocations = [
    $t('diary.LocHome'),
    $t('diary.LocPark'),
    $t('diary.LocVet'),
    $t('diary.LocPetShop'),
    $t('diary.LocRestaurant'),
]

function onFileChange(event: Event) {
    const input = event.target as HTMLInputElement
    if (!input.files) return

    const remaining = 5 - photoFiles.value.length
    const newFiles = Array.from(input.files).slice(0, remaining)

    for (const file of newFiles) {
        photoFiles.value.push(file)
        const reader = new FileReader()
        reader.onload = (e) => {
            photoPreviews.value.push(e.target?.result as string)
        }
        reader.readAsDataURL(file)
    }

    input.value = ''
}

function removePhoto(index: number) {
    photoFiles.value.splice(index, 1)
    photoPreviews.value.splice(index, 1)
}

async function submitDiary() {
    if (photoFiles.value.length === 0 || submitting.value) return

    submitting.value = true

    try {
        const formData = new FormData()
        photoFiles.value.forEach((file, i) => {
            formData.append(`photos[${i}]`, file)
        })
        if (content.value.trim()) formData.append('content', content.value)
        if (location.value.trim()) formData.append('location', location.value)

        await axios.post(`/api/adoption-cases/${props.caseId}/diary`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        Swal.fire({
            icon: 'success',
            title: $t('diary.SuccessTitle'),
            text: $t('diary.SuccessText'),
            timer: 2000,
            showConfirmButton: false,
        })

        // Reset
        content.value = ''
        location.value = ''
        photoFiles.value = []
        photoPreviews.value = []
        emit('submitted')

        if (modalRef.value) {
            const bsModal = Modal.getInstance(modalRef.value) || new Modal(modalRef.value)
            bsModal.hide()
        }
    } catch (error: any) {
        console.error('Failed to submit diary entry:', error)
        Swal.fire({
            icon: 'error',
            title: $t('diary.ErrorTitle'),
            text: error.response?.data?.message || $t('diary.ErrorText'),
        })
    } finally {
        submitting.value = false
    }
}
</script>

<style scoped>
.diary-form__pet-info {
    background-color: var(--color-fog-gray);
}

.diary-form__photo-grid {
    min-height: 90px;
}

.diary-form__photo-item {
    width: 90px;
    height: 90px;
}

.diary-form__photo-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.diary-form__remove-btn {
    background: rgba(0, 0, 0, 0.4);
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.diary-form__upload-btn {
    width: 90px;
    height: 90px;
    border: 2px dashed #cbd5e0;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8fafc;
}

.diary-form__upload-btn:hover {
    border-color: var(--color-denim-blue);
    background: rgba(44, 82, 130, 0.03);
}

.diary-form__loc-btn {
    background: #f1f5f9;
    color: #475569;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.diary-form__loc-btn:hover {
    background: #e2e8f0;
}

.diary-form__loc-btn--active {
    background: var(--color-denim-blue) !important;
    color: white !important;
    box-shadow: 0 2px 8px rgba(44, 82, 130, 0.3);
}

.diary-form__textarea {
    resize: none;
    border: 1px solid #dee2e6;
    transition: border-color 0.2s ease;
}

.diary-form__textarea:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.2rem rgba(44, 82, 130, 0.15);
}

.diary-form__submit-btn {
    transition: all 0.3s ease;
    font-weight: 600;
}

.diary-form__submit-btn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(44, 82, 130, 0.2);
}
</style>
