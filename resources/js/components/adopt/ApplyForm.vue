<template>
    <div class="container-fluid px-0">
        <h2 class="mb-4 fs-3 fw-bold text-dark">送養動物表單</h2>
        <div class="apply-form__wrapper">
            <form id="adopt-form" class="d-flex flex-column gap-5" @submit.prevent="submit()">

                <!-- Section 1: Basic Info -->
                <section class="apply-form__section">
                    <h3 class="apply-form__section-title">基本資料</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="title" class="form-label apply-form__label required">送養標題</label>
                            <input type="text" id="title" class="form-control" placeholder="請輸入吸引人的標題"
                                v-model="formState.title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label apply-form__label required">動物名字</label>
                            <input type="text" id="name" class="form-control" v-model="formState.name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label apply-form__label required">動物性別</label>
                            <select id="gender" class="form-select" v-model="formState.gender" required>
                                <option disabled value="">請選擇性別</option>
                                <option value="male">男生</option>
                                <option value="female">女生</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="age" class="form-label apply-form__label required">大約年紀</label>
                            <select id="age" class="form-select" v-model="formState.age" required>
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
                        <div class="col-md-4">
                            <label for="is-stray" class="form-label apply-form__label required">是否為浪浪</label>
                            <select id="is-stray" class="form-select" v-model="formState.is_stray" required>
                                <option disabled :value="null">請選擇</option>
                                <option :value="true">是</option>
                                <option :value="false">否</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="is-neuter" class="form-label apply-form__label required">結紮狀態</label>
                            <select id="is-neuter" class="form-select" v-model="formState.is_neuter" required>
                                <option disabled :value="null">請選擇結紮狀態</option>
                                <option :value="true">已結紮</option>
                                <option :value="false">未結紮</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label apply-form__label required">{{ $t('Adoption Status')
                                }}</label>
                            <select id="status" class="form-select" v-model="formState.status" required>
                                <option v-for="option in statusOptions.items" :key="String(option.value)"
                                    :value="option.value" :disabled="option.disabled">
                                    {{ $t(option.label) }}
                                </option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Appearance -->
                <section class="apply-form__section">
                    <h3 class="apply-form__section-title">外觀特徵</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="type" class="form-label apply-form__label required">動物種類</label>
                            <select id="type" class="form-select" v-model="formState.type" required>
                                <option disabled value="">請選擇種類</option>
                                <option v-for="pet in Object.keys(pets)" :key="pet" :value="pet">
                                    {{ pet }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="color" class="form-label apply-form__label required">花紋</label>
                            <select id="color" class="form-select" v-model="formState.color" required>
                                <option v-if="!formState.type" disabled value="">請先選擇動物種類</option>
                                <option v-if="formState.type" disabled value="">請選擇花紋</option>
                                <option v-if="formState.type" v-for="color in pets[formState.type]" :key="color"
                                    :value="color">
                                    {{ color }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fur-type" class="form-label apply-form__label required">毛型</label>
                            <select id="fur-type" class="form-select" v-model="formState.fur_type" required>
                                <option disabled value="">請選擇毛型</option>
                                <option value="短毛">短毛</option>
                                <option value="長毛">長毛</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Location -->
                <section class="apply-form__section">
                    <h3 class="apply-form__section-title">位置資訊</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label apply-form__label required">所在縣市</label>
                            <select id="city" class="form-select" v-model="formState.city" required>
                                <option disabled value="">請選擇縣市</option>
                                <optgroup v-for="(cities, region) in areas" :key="region" :label="String(region)">
                                    <option v-for="city in Object.keys(cities)" :key="city" :value="city">
                                        {{ city }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="town" class="form-label apply-form__label required">鄉鎮區</label>
                            <select id="town" class="form-select" v-model="formState.town" required>
                                <option v-if="!formState.city" disabled value="">請先選擇縣市</option>
                                <option v-if="formState.city" disabled value="">請選擇鄉鎮區</option>
                                <option v-if="formState.city" v-for="town in districtsForCity" :key="town"
                                    :value="town">
                                    {{ town }}
                                </option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label apply-form__label required">可送養地區 (複選)</label>
                            <div class="apply-form__city-container p-3 rounded">
                                <div class="row g-3">
                                    <div v-for="(cities, region) in areas" :key="region" class="col-md-6 col-lg-3">
                                        <h6 class="text-muted mb-2 border-bottom pb-1">{{ region }}</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            <div v-for="(districts, city) in cities" :key="city" class="form-check">
                                                <input class="form-check-input" type="checkbox" :value="city"
                                                    :id="'check-' + city" v-model="formState.sendable_city">
                                                <label class="form-check-label" :for="'check-' + city">
                                                    {{ city }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Details -->
                <section class="apply-form__section">
                    <h3 class="apply-form__section-title">詳細說明</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="adoption-description" class="form-label apply-form__label required">送養說明</label>
                            <textarea id="adoption-description" class="form-control" rows="4"
                                v-model="formState.adoption_description" required
                                placeholder="請詳細描述動物的個性、習慣等..."></textarea>
                        </div>
                        <div class="col-12">
                            <label for="health-description" class="form-label apply-form__label required">健康狀態說明</label>
                            <textarea id="health-description" class="form-control" rows="4"
                                v-model="formState.health_description" required
                                placeholder="是否有打疫苗、驅蟲、或是其他健康狀況..."></textarea>
                        </div>
                        <div class="col-12">
                            <label for="adoption-condition" class="form-label apply-form__label required">領養條件</label>
                            <textarea id="adoption-condition" class="form-control" rows="4"
                                v-model="formState.adoption_condition" required
                                placeholder="例如：需年滿20歲、需簽署認養切結書..."></textarea>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Adoption Template -->
                <section class="apply-form__section">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="apply-form__section-title mb-0">{{ $t('Adoption Form Template') }}</h3>
                        <RouterLink to="/user/adoption-templates" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="bi bi-gear me-1"></i>{{ $t('Manage Templates') }}
                        </RouterLink>
                    </div>
                    <p class="text-muted small mb-3">{{ $t('template_selector.hint') }}</p>

                    <div class="mb-4">
                        <label for="template-select" class="form-label apply-form__label">{{ $t('Select Template')
                        }}</label>
                        <select id="template-select" class="form-select" v-model="formState.adoption_form_template_id">
                            <option :value="null">{{ $t('No template (basic form only)') }}</option>
                            <option v-for="tpl in availableTemplates" :key="tpl.id" :value="tpl.id">
                                {{ tpl.name }} ({{ tpl.schema?.length || 0 }} {{ $t('fields') }})
                            </option>
                        </select>
                    </div>

                    <!-- Template Preview -->
                    <div v-if="selectedTemplate" class="apply-form__template-preview">
                        <div class="apply-form__template-preview-header">
                            <i class="bi bi-eye me-2"></i>{{ $t('Custom Questions Preview') }} ({{ selectedTemplate.name
                            }})
                        </div>
                        <div class="apply-form__template-preview-body">
                            <div v-for="(field, fi) in selectedTemplate.schema" :key="fi"
                                class="apply-form__template-preview-field">
                                <div class="d-flex align-items-center gap-2">
                                    <i :class="fieldTypeIcon(field.type)" class="text-primary"></i>
                                    <span>{{ field.label }}</span>
                                    <span v-if="field.required" class="text-danger fw-bold">*</span>
                                    <span class="apply-form__template-preview-type">{{ $t(`fieldType.${field.type}`)
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Fields Reference -->
                    <div v-else class="apply-form__template-preview">
                        <div class="apply-form__template-preview-header d-flex justify-content-between align-items-center"
                            style="cursor: pointer; user-select: none;" @click="showBaseFields = !showBaseFields">
                            <div>
                                <i class="bi bi-info-circle me-2"></i>{{ $t('Basic Form Content') }}
                            </div>
                            <i class="bi" :class="showBaseFields ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                        </div>
                        <Transition name="collapse">
                            <div v-if="showBaseFields" class="apply-form__template-preview-body">
                                <div class="text-muted small mb-2">{{ $t('basic_fields.preview_hint') }}</div>
                                <div v-for="field in baseFields" :key="field.key"
                                    class="apply-form__template-preview-field">
                                    <div class="d-flex align-items-center gap-2">
                                        <i :class="field.icon" class="text-secondary"></i>
                                        <span>{{ $t(field.label) }}</span>
                                        <span v-if="field.required" class="text-danger fw-bold">*</span>
                                        <span class="apply-form__template-preview-type">{{ $t(field.typeName) }}</span>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </section>

                <!-- Section 6: Images -->
                <section class="apply-form__section">
                    <h3 class="apply-form__section-title">送養相關圖片</h3>
                    <div class="d-flex gap-4 flex-wrap">
                        <div v-for="(url, index) in imgUrls" :key="index"
                            class="d-flex flex-column apply-form__upload-card">
                            <div v-if="url === ''"
                                class="apply-form__upload--placeholder d-flex justify-content-center align-items-center w-100 h-100">
                                <i class="bi bi-image text-secondary fs-1"></i>
                            </div>
                            <img v-else :src="url" class="apply-form__upload--img">
                            <button type="button" @click="updateImg(index, url)"
                                class="apply-form__btn apply-form__btn--img btn fw-medium" data-bs-toggle="modal"
                                data-bs-target="#update-img-modal">
                                {{ url ? '更換圖片' : '上傳圖片' }}
                            </button>
                        </div>
                    </div>
                    <UpdateImg v-show="showModal" @update:confirm-img="saveConfirmImg" />
                </section>

                <div class="text-end mt-4 border-top pt-4">
                    <button type="submit" class="apply-form__btn btn px-5 py-2" :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"
                            aria-hidden="true"></span>
                        {{ isSubmitting ? '送出中...' : '確定送出' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts" name="ApplyForm">
import { ref, reactive, watch, computed, onMounted } from 'vue'
import { areas } from '@/../data/areas'
import { pets } from '@/../data/pets'
import { statusOptions } from '@/../data/options'
import UpdateImg from '@/components/modals/UpdateImg.vue'
import { PetFormInter } from '@/types/pet'
import { Modal } from 'bootstrap'
import axios from 'axios'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'

const router = useRouter()
const isSubmitting = ref(false)

const showModal = ref<boolean>(false)
// Use snake_case keys to match backend expectations
const formState = reactive<PetFormInter>({
    title: '',
    city: '',
    town: '',
    is_stray: null,
    type: '',
    color: '',
    fur_type: '',
    name: '',
    gender: '',
    age: '',
    is_neuter: null,
    sendable_city: [],
    adoption_description: '',
    health_description: '',
    adoption_condition: '',
    status: 'available',
    adoption_form_template_id: null,
    blobs: []
})

// Basic fields for preview
const baseFields = [
    { key: 'name', label: 'Nickname', icon: 'bi bi-person', typeName: 'fieldType.text', required: true },
    { key: 'phone', label: 'Phone Number', icon: 'bi bi-telephone', typeName: 'fieldType.text', required: true },
    { key: 'line_id', label: 'Line ID (Optional)', icon: 'bi bi-chat-dots', typeName: 'fieldType.text', required: false },
    { key: 'experience', label: 'Pet Experience', icon: 'bi bi-star', typeName: 'fieldType.select', required: true },
    { key: 'family', label: 'Does family/roommate agree?', icon: 'bi bi-people', typeName: 'fieldType.radio', required: true },
    { key: 'message', label: 'Message to Owner (Briefly)', icon: 'bi bi-chat-square-text', typeName: 'fieldType.textarea', required: true },
]

// Template data
interface TemplateItem {
    id: number
    name: string
    schema: Array<{ label: string; type: string; required: boolean; options?: string[] }>
}
const availableTemplates = ref<TemplateItem[]>([])
const showBaseFields = ref(false)

const selectedTemplate = computed(() => {
    if (!formState.adoption_form_template_id) return null
    return availableTemplates.value.find(t => t.id === formState.adoption_form_template_id) || null
})

function fieldTypeIcon(type: string): string {
    const icons: Record<string, string> = {
        text: 'bi bi-input-cursor-text',
        textarea: 'bi bi-textarea-t',
        select: 'bi bi-menu-button-wide',
        radio: 'bi bi-record-circle',
        checkbox: 'bi bi-check2-square',
    }
    return icons[type] || 'bi bi-question-circle'
}

async function fetchTemplates() {
    try {
        const res = await axios.get('/api/user/adoption-templates')
        availableTemplates.value = res.data.data
    } catch {
        // Non-blocking: templates are optional
        console.warn('Failed to fetch adoption templates')
    }
}

onMounted(() => {
    fetchTemplates()
})
const imgUrls = ref<string[]>(['', '', ''])
const selectedImg = ref<{ index: number | null, url: string }>({
    index: null,
    url: ''
})

const initTown = watch(
    () => formState.city,
    () => {
        formState.town = ''
    })

const initColor = watch(
    () => formState.type,
    () => {
        formState.color
    }
)

const districtsForCity = computed(() => {
    for (const region in areas) {
        if (formState.city in areas[region]) {
            return areas[region][formState.city]
        }
    }
    return []
})

// Opens the image update modal
function updateImg(index: number, url: string) {
    // Fill in the empty preview field
    let resolvedIndex = index
    if (url === '') {
        const emptyIndex = imgUrls.value.slice(0, index).findIndex(p => p === '')
        if (emptyIndex !== -1) {
            resolvedIndex = emptyIndex
        }
    }

    // Prevent out-of-range index
    if (resolvedIndex < 0 || resolvedIndex >= imgUrls.value.length) return

    selectedImg.value = {
        index: resolvedIndex,
        url,
    }
    showModal.value = true
}

// Update the form with the preview image selected from the image update modal
function saveConfirmImg(payload: { previewUrl: string, blob: Blob }) {
    const { previewUrl, blob } = payload
    const index = selectedImg.value.index

    if (index !== null) {
        imgUrls.value[index] = previewUrl
        formState.blobs[index] = blob
    }

    showModal.value = false
    closeModal()
}

function closeModal() {
    const modalElement = document.getElementById('update-img-modal') as HTMLElement
    if (modalElement) {
        const modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement)
        modalInstance.hide()
    }
}

// Convert formState to FormData format for submission
function toFormData() {
    const form = new FormData()
    for (const [key, value] of Object.entries(formState)) {
        // If the value is an array, append each item with an indexed key (e.g., ('sendable_city[0]', '台北市'), ('sendable_city[1]', '新北市'))
        if (Array.isArray(value)) {
            value.forEach((v, i) => {
                form.append(`${key}[${i}]`, v)
            })
        }
        // If the value is a boolean, convert it to '1' or '0'
        else if (typeof value === 'boolean') {
            form.append(key, value ? '1' : '0')
        }
        // For other types (e.g., string, Blob), append as is (if not null/undefined)
        else if (value !== null && value !== undefined) {
            form.append(key, value as string | Blob)
        }
    }
    return form
}



// Submit the form
async function submit() {
    if (isSubmitting.value) return

    isSubmitting.value = true

    // Validate images
    const hasImage = formState.blobs.some(blob => blob !== undefined && blob !== null)
    if (!hasImage) {
        Swal.fire({
            icon: 'warning',
            title: '請上傳圖片',
            text: '請至少上傳一張動物的照片，讓大家更認識牠！',
            confirmButtonColor: '#2c5282'
        })
        isSubmitting.value = false
        return
    }

    const realForm = toFormData()

    try {
        await axios.post('/api/adopt', realForm)

        await Swal.fire({
            icon: 'success',
            title: '發布成功',
            text: '您的送養資訊已成功發布！',
            confirmButtonText: '前往送養管理頁面',
            confirmButtonColor: '#2c5282'
        })

        router.push('/user/adoptions')
    } catch (error: any) {
        console.error('Submit failed:', error)
        Swal.fire({
            icon: 'error',
            title: '發布失敗',
            text: error.response?.data?.message || '發生未知錯誤，請稍後再試',
            confirmButtonColor: '#d33'
        })
    } finally {
        isSubmitting.value = false
    }
}
</script>

<style scoped>
.apply-form__wrapper {
    background: #ffffff;
    width: 100%;
    max-width: 960px;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(44, 82, 130, 0.10), 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #dde3ec;
    padding: 3rem;
}

.apply-form__section {
    background: var(--color-fog-gray);
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #d1d9e6;
}

.apply-form__section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-denim-blue-dark);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    padding-left: 0.75rem;
    border-left: 4px solid var(--color-denim-blue);
    display: block;
}

.apply-form__label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.apply-form__label.required::after {
    content: "*";
    color: #e53e3e;
    margin-left: 0.25rem;
}

.apply-form__city-container {
    background-color: #ffffff;
    border: 1px solid #d1d9e6;
    border-radius: 8px;
}

.apply-form__upload-card {
    width: min(150px, 45vw);
    max-width: 100%;
}

.apply-form__upload--placeholder {
    background-color: #fff;
    width: min(150px, 45vw);
    height: min(150px, 45vw);
    aspect-ratio: 1 / 1;
    border-radius: 6px 6px 0 0;
    border: 2px dashed #a0aec0;
    border-bottom: none;
}

.apply-form__upload--img {
    width: min(150px, 45vw);
    height: min(150px, 45vw);
    border-radius: 6px 6px 0 0;
    aspect-ratio: 1 / 1;
    object-fit: cover;
}

/* Base Button Style */
.apply-form__btn {
    border: none;
    outline: none;
    transition: all 0.2s ease;
    font-weight: 500;
    letter-spacing: 0.025em;
}

/* Update Image Button */
.apply-form__btn--img {
    background-color: #4a5568;
    color: #ffffff;
    border: 1px solid #4a5568;
    border-top: none;
    border-radius: 0 0 6px 6px;
    padding: 0.5rem;
    font-size: 0.875rem;
    width: 100%;
}

.apply-form__btn--img:hover {
    background-color: var(--color-denim-blue-dark);
    color: #ffffff;
}

/* Submit Button */
button[type="submit"].apply-form__btn {
    background: var(--color-denim-blue);
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(44, 82, 130, 0.25);
    padding: 0.75rem 3rem;
    font-size: 1.05rem;
    font-weight: 700;
}

button[type="submit"].apply-form__btn:hover {
    background: var(--color-denim-blue-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(44, 82, 130, 0.3);
}

button[type="submit"].apply-form__btn:active {
    transform: translateY(0);
}

/* Divider before submit */
.border-top {
    border-color: #d1d9e6 !important;
}

/* Inputs */
.form-control,
.form-select {
    background-color: #fff;
    border: 1px solid #c4cdd9;
    color: #2d3748;
    border-radius: 8px;
    padding: 0.6rem 0.75rem;
    transition: all 0.2s;
}

.form-control:focus,
.form-select:focus {
    background-color: #fff;
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.15);
}

.form-check-input:checked {
    background-color: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
}

/* Template Preview */
.apply-form__template-preview {
    background: #ffffff;
    border: 1px solid #d1d9e6;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 0.75rem;
    box-shadow: 0 2px 8px rgba(44, 82, 130, 0.07);
}

.apply-form__template-preview-header {
    background: #edf2f7;
    padding: 0.65rem 1rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--color-denim-blue-dark);
    border-radius: 11px 11px 0 0;
    border-bottom: 1px solid #d1d9e6;
}

.apply-form__template-preview-body {
    padding: 0.75rem 1rem;
}

.apply-form__template-preview-field {
    padding: 0.4rem 0.6rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    margin-bottom: 0.3rem;
    font-size: 0.88rem;
    color: #4a5568;
}

/* Collapse Transition */
.collapse-enter-active,
.collapse-leave-active {
    transition: all 0.25s ease;
    overflow: hidden;
}

.collapse-enter-from,
.collapse-leave-to {
    opacity: 0;
    max-height: 0;
}

.collapse-enter-to,
.collapse-leave-from {
    opacity: 1;
    max-height: 500px;
}

.apply-form__template-preview-type {
    margin-left: auto;
    font-size: 0.75rem;
    color: #718096;
    background: #edf2f7;
    padding: 0.1rem 0.5rem;
    border-radius: 4px;
}

/* Mobile Responsive Styles */
@media (max-width: 576px) {
    .apply-form__wrapper {
        padding: 1.5rem;
        border-radius: 16px;
    }

    .apply-form__section {
        padding: 1.25rem;
        border-radius: 12px;
    }

    .apply-form__section-title {
        font-size: 1rem;
        margin-bottom: 1rem;
    }

    .apply-form__label {
        font-size: 0.875rem;
    }

    button[type="submit"].apply-form__btn {
        width: 100%;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    .apply-form__btn--img {
        font-size: 0.75rem;
        padding: 0.4rem;
    }
}
</style>
