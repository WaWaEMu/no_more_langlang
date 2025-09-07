<template>
    <div class="container">
        <h2 class="mb-5 fs-4">送養動物表單</h2>
        <div class="apply-form__wrapper d-flex justify-content-center">
            <form id="adopt-form" class="m-5 d-flex flex-column gap-4">
                <div>
                    <label for="title">
                        <span class="text-danger">*</span>
                        送養標題
                    </label>
                    <input type="text" id="title" class="form-control" placeholder="" v-model="formState.title" required>
                </div>
                <div class="d-flex gap-4">
                    <div class="w-100">
                        <label for="city">
                            <span class="text-danger">*</span>
                            縣市
                        </label>
                        <select id="city" class="form-select" v-model="formState.city" required>
                            <option disabled>請選擇縣市</option>
                            <optgroup v-for="(cities, region) in areas" :key="region" :label="String(region)">
                                <option v-for="city in Object.keys(cities)" :key="city" :value="city">
                                    {{ city }}
                                </option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="town">
                            <span class="text-danger">*</span>
                            鄉鎮區
                        </label>
                        <select id="town" class="form-select" v-model="formState.town" required>
                            <option v-if="!formState.city" disabled>請先選擇縣市</option>
                            <option v-if="formState.city" disabled>請選擇鄉鎮區</option>
                            <option v-if="formState.city" v-for="town in districtsForCity" :key="town" :value="town">
                                {{ town }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="is-stray">
                            <span class="text-danger">*</span>
                            是否為浪浪
                        </label>
                        <select id="is-stray" class="form-select" v-model="formState.is_stray" required>
                            <option disabled>請選擇</option>
                            <option :value=true>是</option>
                            <option :value=false>否</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-4">
                    <div class="w-100">
                        <label for="type">
                            <span class="text-danger">*</span>
                            動物種類
                        </label>
                        <select id="type" class="form-select" v-model="formState.type" required>
                            <option disabled>請選擇種類</option>
                            <option v-for="pet in Object.keys(pets)" :key="pet" :value="pet">
                                {{ pet }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="color">
                            <span class="text-danger">*</span>
                            花紋
                        </label>
                        <select id="color" class="form-select" v-model="formState.color" required>
                            <option v-if="!formState.type" disabled>請先選擇動物種類</option>
                            <option v-if="formState.type" disabled>請選擇花紋</option>
                            <option v-if="formState.type" v-for="color in pets[formState.type]" :key="color" :value="color">
                                {{ color }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="fur-type">
                            <span class="text-danger">*</span>
                            毛型
                        </label>
                        <select id="fur-type" class="form-select" v-model="formState.fur_type" required>
                            <option disabled>請選擇毛型</option>
                            <option value="短毛">短毛</option>
                            <option value="長毛">長毛</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-4">
                    <div class="w-100">
                        <label for="name">
                            <span class="text-danger">*</span>
                            動物名字
                        </label>
                        <input type="text" id="name" class="form-control" placeholder="" v-model="formState.name" required>
                    </div>
                    <div class="w-100">
                        <label for="gender">
                            <span class="text-danger">*</span>
                            動物性別
                        </label>
                        <select id="gender" class="form-select" v-model="formState.gender" required>
                            <option disabled>請選擇性別</option>
                            <option value="male">男生</option>
                            <option value="female">女生</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-4">
                    <div class="w-100">
                        <label for="age">
                            <span class="text-danger">*</span>
                            大約年紀
                        </label>
                        <select id="age" class="form-select" v-model="formState.age" required>
                            <option disabled>請選擇年紀範圍</option>
                            <option value="0-1">0 ~ 1歲</option>
                            <option value="1-4">1 ~ 4歲</option>
                            <option value="4-7">4 ~ 7歲</option>
                            <option value="7-10">7 ~ 10歲</option>
                            <option value="10-13">10 ~ 13歲</option>
                            <option value="13-16">13 ~ 16歲</option>
                            <option value="16+">16歲 ~</option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="is-neuter">
                            <span class="text-danger">*</span>
                            結紮狀態
                        </label>
                        <select id="is-neuter" class="form-select" v-model="formState.is_neuter" required>
                            <option disabled>請選擇結紮狀態</option>
                            <option :value=true>已結紮</option>
                            <option :value=false>未結紮</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="sendable-city">
                        <span class="text-danger">*</span>
                        可送養地區 (複選)
                    </label>
                    <div id="sendable-city" class="d-flex gap-4">
                        <div v-for="(cities, region) in areas" :key="region" class="w-25">
                            <span class="mx-1">{{ region }}</span>
                            <hr>
                            <label v-for="(districts, city) in cities" class="me-3">
                                <input type="checkbox" v-model="formState.sendable_city" :value=city class="me-1">
                                {{ city }}
                            </label>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="adoption-description">
                        <span class="text-danger">*</span>
                        送養說明
                    </label>
                    <textarea id="adoption-description" class="form-control" rows="4" v-model="formState.adoption_description" required></textarea>
                </div>
                <div>
                    <label for="health-description">
                        <span class="text-danger">*</span>
                        健康狀態說明
                    </label>
                    <textarea id="health-description" class="form-control" rows="4" v-model="formState.health_description" required></textarea>
                </div>
                <div>
                    <label for="adoption-condition">
                        <span class="text-danger">*</span>
                        領養條件
                    </label>
                    <textarea id="adoption-condition" class="form-control" rows="4" v-model="formState.adoption_condition" required></textarea>
                </div>
                <div>
                    <label>送養相關圖片</label>
                    <div class="d-flex gap-4 flex-wrap">
                        <div v-for="(url, index) in imgUrls" :key="index" class="d-flex flex-column w-25">
                            <div v-if="url === ''" class="apply-form__upload--placeholder d-flex justify-content-center align-items-center w-100 h-100">
                                <img src="@public/icons/image.svg" alt="" class="w-50">
                            </div>
                            <img v-else :src="url" class="apply-form__upload--img">
                            <button type="button"
                                    @click="updateImg(index, url)"
                                    class="apply-form__btn apply-form__btn--img btn fw-medium"
                                    data-bs-toggle="modal"
                                    data-bs-target="#update-img-modal">
                                    更新圖檔
                            </button>
                        </div>
                    </div>
                    <UpdateImg v-show="showModal"
                        @update:confirm-img="saveConfirmImg"
                    />
                </div>
                <div class="text-end">
                    <button type="submit" @click="submit()" class="apply-form__btn btn px-5 py-2">確定送出</button>
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
        blobs: ['', '', '']
    })
    const imgUrls = ref<string[]>(['', '', ''])
    const selectedImg = ref<{ index: number | null, url: string }>({
        index: null ,
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
    function submit() {
        const realForm = toFormData()

        axios.post('/api/adopt/store', realForm)
            .then()
            .catch()
    }
</script>

<style scoped>
    label {
        margin-bottom: 0.5rem;
    }

    .apply-form__wrapper {
        background-color: #fff2cc;
        width: 85%;
    }

    .apply-form__upload--placeholder {
        background-color: #fff;
        width: 150px;
        height: 150px;
        aspect-ratio: 1 / 1;
        border-radius: 5px 5px 0 0;
    }

    .apply-form__upload--img {
        border-radius: 5px 5px 0 0;
        aspect-ratio: 1 / 1;
        object-fit: cover;
    }

    .apply-form__btn {
        background-color: #d79f64;
        color: #fff;
        border: none;
        outline: none;
        transition: background-color 0.2s ease;
        &&:hover {
            background-color: #b37d41;
        }
        &&:active {
            color: #fff;
        }
    }

    .apply-form__btn--img {
        border-radius: 0 0 5px 5px;
    }

    #sendable-city hr {
        margin: 0.25rem 0;
        height: 2px;
        background-color: black;
    }
</style>
