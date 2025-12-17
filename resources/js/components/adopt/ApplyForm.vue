<template>
    <div class="container-fluid px-0">
        <h2 class="mb-4 fs-3 fw-bold text-dark">送養動物表單</h2>
        <div class="apply-form__wrapper">
            <form id="adopt-form" class="d-flex flex-column gap-5" @submit.prevent="submit()">

                <!-- Section 1: Basic Info -->
                <section class="form-section">
                    <h3 class="form-section__title">基本資料</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="title" class="form-label required">送養標題</label>
                            <input type="text" id="title" class="form-control" placeholder="請輸入吸引人的標題"
                                v-model="formState.title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label required">動物名字</label>
                            <input type="text" id="name" class="form-control" v-model="formState.name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label required">動物性別</label>
                            <select id="gender" class="form-select" v-model="formState.gender" required>
                                <option disabled value="">請選擇性別</option>
                                <option value="male">男生</option>
                                <option value="female">女生</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="age" class="form-label required">大約年紀</label>
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
                            <label for="is-stray" class="form-label required">是否為浪浪</label>
                            <select id="is-stray" class="form-select" v-model="formState.is_stray" required>
                                <option disabled :value="null">請選擇</option>
                                <option :value="true">是</option>
                                <option :value="false">否</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="is-neuter" class="form-label required">結紮狀態</label>
                            <select id="is-neuter" class="form-select" v-model="formState.is_neuter" required>
                                <option disabled :value="null">請選擇結紮狀態</option>
                                <option :value="true">已結紮</option>
                                <option :value="false">未結紮</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Appearance -->
                <section class="form-section">
                    <h3 class="form-section__title">外觀特徵</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="type" class="form-label required">動物種類</label>
                            <select id="type" class="form-select" v-model="formState.type" required>
                                <option disabled value="">請選擇種類</option>
                                <option v-for="pet in Object.keys(pets)" :key="pet" :value="pet">
                                    {{ pet }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="color" class="form-label required">花紋</label>
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
                            <label for="fur-type" class="form-label required">毛型</label>
                            <select id="fur-type" class="form-select" v-model="formState.fur_type" required>
                                <option disabled value="">請選擇毛型</option>
                                <option value="短毛">短毛</option>
                                <option value="長毛">長毛</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Location -->
                <section class="form-section">
                    <h3 class="form-section__title">位置資訊</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label required">所在縣市</label>
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
                            <label for="town" class="form-label required">鄉鎮區</label>
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
                            <label class="form-label required">可送養地區 (複選)</label>
                            <div class="sendable-city__container p-3 rounded">
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
                <section class="form-section">
                    <h3 class="form-section__title">詳細說明</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="adoption-description" class="form-label required">送養說明</label>
                            <textarea id="adoption-description" class="form-control" rows="4"
                                v-model="formState.adoption_description" required
                                placeholder="請詳細描述動物的個性、習慣等..."></textarea>
                        </div>
                        <div class="col-12">
                            <label for="health-description" class="form-label required">健康狀態說明</label>
                            <textarea id="health-description" class="form-control" rows="4"
                                v-model="formState.health_description" required
                                placeholder="是否有打疫苗、驅蟲、或是其他健康狀況..."></textarea>
                        </div>
                        <div class="col-12">
                            <label for="adoption-condition" class="form-label required">領養條件</label>
                            <textarea id="adoption-condition" class="form-control" rows="4"
                                v-model="formState.adoption_condition" required
                                placeholder="例如：需年滿20歲、需簽署認養切結書..."></textarea>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Images -->
                <section class="form-section">
                    <h3 class="form-section__title">送養相關圖片</h3>
                    <div class="d-flex gap-4 flex-wrap">
                        <div v-for="(url, index) in imgUrls" :key="index" class="d-flex flex-column img-upload-card">
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
import { ref, reactive, watch, computed } from 'vue'
import { areas } from '@/../data/areas'
import { pets } from '@/../data/pets'
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
    blobs: []
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
    const realForm = toFormData()

    try {
        await axios.post('/api/adopt/store', realForm)

        await Swal.fire({
            icon: 'success',
            title: '發布成功',
            text: '您的送養資訊已成功發布！',
            confirmButtonText: '前往我的送養清單',
            confirmButtonColor: '#2c5282'
        })

        router.push('/user/my-pets')
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
    /* Warm Mist Gradient: Gentle Beige/Warm Gray */
    background: linear-gradient(135deg, #f5f5f4 0%, #e7e5e4 100%);
    width: 100%;
    max-width: 960px;
    /* Limit max width for readability */
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #d6d3d1;
    padding: 2.5rem;
}

.form-section {
    background: rgba(255, 255, 255, 0.6);
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.form-section__title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #4a5568;
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #cbd5e0;
    display: inline-block;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-label.required::after {
    content: "*";
    color: #e53e3e;
    margin-left: 0.25rem;
}

.sendable-city__container {
    background-color: #fff;
    border: 1px solid #e2e8f0;
}

.img-upload-card {
    width: 150px;
}

.apply-form__upload--placeholder {
    background-color: #fff;
    width: 150px;
    height: 150px;
    aspect-ratio: 1 / 1;
    border-radius: 6px 6px 0 0;
    border: 2px dashed #cbd5e0;
    border-bottom: none;
}

.apply-form__upload--img {
    width: 150px;
    height: 150px;
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

/* Update Image Button (Secondary) - Dark Warm Gray */
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
    background-color: #2d3748;
    color: #ffffff;
}

/* Submit Button (Primary) - Muted Clay/Latte Gradient */
button[type="submit"].apply-form__btn {
    background: #2c5282;
    color: #fff;
    border-radius: 6px;
    box-shadow: 0 4px 6px rgba(44, 82, 130, 0.2);
    padding: 0.75rem 3rem;
    font-size: 1.1rem;
}

button[type="submit"].apply-form__btn:hover {
    background: #2a4365;
    transform: translateY(-1px);
    box-shadow: 0 6px 8px rgba(44, 82, 130, 0.25);
}

button[type="submit"].apply-form__btn:active {
    transform: translateY(0);
}

/* Inputs */
.form-control,
.form-select {
    background-color: #fff;
    border: 1px solid #cbd5e0;
    color: #2d3748;
    border-radius: 6px;
    padding: 0.6rem 0.75rem;
    transition: all 0.2s;
}

.form-control:focus,
.form-select:focus {
    background-color: #fff;
    border-color: #3182ce;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
}

.form-check-input:checked {
    background-color: #3182ce;
    border-color: #3182ce;
}
</style>
