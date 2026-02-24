<template>
    <div class="modal fade" id="trackingReportModal" tabindex="-1" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2 text-primary"></i>{{ $t('Submit Report') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Pet Info Header -->
                    <div class="d-flex align-items-center mb-3 p-2 rounded-3 report-form__pet-info">
                        <img :src="getPetImageUrl()" class="rounded-circle object-fit-cover" 
                            width="40" height="40" :alt="petName">
                        <span class="ms-2 fw-semibold text-dark">{{ petName }}</span>
                    </div>

                    <!-- Report Content -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">
                            {{ $t('Report Content') }}
                        </label>
                        <textarea 
                            v-model="content" 
                            class="form-control report-form__textarea" 
                            :placeholder="$t('Report Content Placeholder')"
                            rows="5"
                            maxlength="1000"
                        ></textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <small class="text-muted">{{ content.length }} / 1000</small>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">
                            {{ $t('Upload Photos') }}
                            <span class="text-muted fw-normal">({{ $t('Max 3 photos') }})</span>
                        </label>
                        <div class="d-flex gap-2 flex-wrap">
                            <!-- Previews -->
                            <div v-for="(preview, index) in imagePreviews" :key="index" 
                                class="report-form__preview position-relative">
                                <img :src="preview" class="rounded-3 object-fit-cover" width="80" height="80">
                                <button @click="removeImage(index)" type="button"
                                    class="btn btn-sm position-absolute top-0 end-0 report-form__remove-btn">
                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                </button>
                            </div>
                            <!-- Upload Button -->
                            <label v-if="imagePreviews.length < 3" 
                                class="report-form__upload-btn d-flex align-items-center justify-content-center rounded-3">
                                <input type="file" accept="image/*" multiple @change="onFileChange" class="d-none">
                                <i class="bi bi-plus-lg text-muted fs-4"></i>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="submitReport" type="button" 
                        class="btn btn-primary px-4 shadow-sm report-form__submit-btn"
                        :disabled="submitting || !content.trim()">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>
                        {{ submitting ? $t('Sending...') : $t('Submit Report') }}
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
const imageFiles = ref<File[]>([])
const imagePreviews = ref<string[]>([])
const submitting = ref(false)
const modalRef = ref<HTMLElement | null>(null)

function getPetImageUrl() {
    return props.petImage || '/images/default-pet.png'
}

function onFileChange(event: Event) {
    const input = event.target as HTMLInputElement
    if (!input.files) return

    const remaining = 3 - imageFiles.value.length
    const newFiles = Array.from(input.files).slice(0, remaining)

    for (const file of newFiles) {
        imageFiles.value.push(file)
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreviews.value.push(e.target?.result as string)
        }
        reader.readAsDataURL(file)
    }

    // Reset input so same file can be selected again
    input.value = ''
}

function removeImage(index: number) {
    imageFiles.value.splice(index, 1)
    imagePreviews.value.splice(index, 1)
}

async function submitReport() {
    if (!content.value.trim() || submitting.value) return

    submitting.value = true

    try {
        const formData = new FormData()
        formData.append('content', content.value)
        imageFiles.value.forEach((file, index) => {
            formData.append(`images[${index}]`, file)
        })

        await axios.post(`/api/adoption-cases/${props.caseId}/reports`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        // Success Feedback
        Swal.fire({
            icon: 'success',
            title: '提交成功',
            text: '追蹤回報已順利送出',
            timer: 2000,
            showConfirmButton: false,
            position: 'center',
            toast: false
        })

        // Reset form
        content.value = ''
        imageFiles.value = []
        imagePreviews.value = []

        emit('submitted')

        // Close modal using imported Modal class
        if (modalRef.value) {
            const bsModal = Modal.getInstance(modalRef.value) || new Modal(modalRef.value)
            bsModal.hide()
        }
    } catch (error: any) {
        console.error('Failed to submit report:', error)
        Swal.fire({
            icon: 'error',
            title: '提交失敗',
            text: error.response?.data?.message || '請稍後再試'
        })
    } finally {
        submitting.value = false
    }
}
</script>

<style scoped>
.report-form__pet-info {
    background-color: var(--color-fog-gray);
}

.report-form__textarea {
    resize: none;
    border: 1px solid #dee2e6;
    transition: border-color 0.2s ease;
}

.report-form__textarea:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.2rem rgba(44, 82, 130, 0.15);
}

.report-form__submit-btn {
    transition: all 0.3s ease;
    font-weight: 600;
}

.report-form__submit-btn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(44, 82, 130, 0.2);
}

.report-form__preview {
    transition: transform 0.2s ease;
}

.report-form__preview:hover {
    transform: scale(1.05);
}

.report-form__remove-btn {
    background: none;
    border: none;
    padding: 0;
    margin: -4px -4px 0 0;
}

.report-form__upload-btn {
    width: 80px;
    height: 80px;
    border: 2px dashed #dee2e6;
    cursor: pointer;
    transition: all 0.2s ease;
}

.report-form__upload-btn:hover {
    border-color: var(--color-denim-blue);
    background-color: rgba(44, 82, 130, 0.05);
}
</style>
