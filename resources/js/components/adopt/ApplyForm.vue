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
                    <input type="text" id="title" class="form-control" placeholder="" v-model="formData.title" required>
                </div>
                <div class="d-flex gap-4">
                    <div class="w-100">
                        <label for="city">
                            <span class="text-danger">*</span>
                            縣市
                        </label>
                        <select id="city" class="form-select" v-model="formData.city" required>
                            <option disabled>請選擇縣市</option>
                            <option v-for="city in Object.keys(cities)" :key="city" :value="city">
                                {{ city }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="town">
                            <span class="text-danger">*</span>
                            鄉鎮區
                        </label>
                        <select id="town" class="form-select" v-model="formData.town" required>
                            <option v-if="!formData.city" disabled>請先選擇縣市</option>
                            <option v-if="formData.city" disabled>請選擇鄉鎮區</option>
                            <option v-if="formData.city" v-for="town in cities[formData.city]" :key="town" :value="town">
                                {{ town }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="is-stray">
                            <span class="text-danger">*</span>
                            是否為浪浪
                        </label>
                        <select id="is-stray" class="form-select" v-model="formData.isStray" required>
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
                        <select id="type" class="form-select" v-model="formData.type" required>
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
                        <select id="color" class="form-select" v-model="formData.color" required>
                            <option v-if="!formData.type" disabled>請先選擇動物種類</option>
                            <option v-if="formData.type" disabled>請選擇花紋</option>
                            <option v-if="formData.type" v-for="color in pets[formData.type]" :key="color" :value="color">
                                {{ color }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="fur-type">
                            <span class="text-danger">*</span>
                            毛型
                        </label>
                        <select id="fur-type" class="form-select" v-model="formData.furType" required>
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
                        <input type="text" id="name" class="form-control" placeholder="" v-model="formData.name" required>
                    </div>
                    <div class="w-100">
                        <label for="gender">
                            <span class="text-danger">*</span>
                            動物性別
                        </label>
                        <select id="gender" class="form-select" v-model="formData.gender" required>
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
                        <select id="age" class="form-select" v-model="formData.age" required>
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
                        <select id="is-neuter" class="form-select" v-model="formData.isNeuter" required>
                            <option disabled>請選擇結紮狀態</option>
                            <option :value=true>已結紮</option>
                            <option :value=false>未結紮</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="can-send-city">
                        <span class="text-danger">*</span>
                        可送養地區 (複選)
                    </label>
                    <div id="can-send-city">
                        <label v-for="city in Object.keys(cities)" class="me-3">
                            <input type="checkbox" v-model="formData.canSendCity" :value=city class="me-1">
                            {{ city }}
                        </label>
                    </div>
                </div>
                <div>
                    <label for="des">
                        <span class="text-danger">*</span>
                        送養說明
                    </label>
                    <textarea id="des" class="form-control" rows="4" v-model="formData.des" required></textarea>
                </div>
                <div>
                    <label for="health-des">
                        <span class="text-danger">*</span>
                        健康狀態說明
                    </label>
                    <textarea id="health-des" class="form-control" rows="4" v-model="formData.healthDes" required></textarea>
                </div>
                <div>
                    <label for="cond">
                        <span class="text-danger">*</span>
                        領養條件
                    </label>
                    <textarea id="cond" class="form-control" rows="4" v-model="formData.cond" required></textarea>
                </div>
                <div>
                    <label>送養相關圖片</label>
                    <div class="d-flex gap-4 flex-wrap">
                        <div v-for="(url, index) in previewImgList" :key="index" class="d-flex flex-column w-25">
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
                        :selected-img="selectedImg"
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
    import { ref, reactive, watch } from 'vue'
    import { cities } from '@/../data/cities'
    import { pets } from '@/../data/pets'
    import UpdateImg from '@/components/modals/UpdateImg.vue'
    import { FormInter } from '@/types/form'
    import { Modal } from 'bootstrap'

    const showModal = ref<boolean>(false)
    const formData = reactive<FormInter>({
        title: '',
        city: '',
        town: '',
        isStray: null,
        type: '',
        color: '',
        furType: '',
        name: '',
        gender: '',
        age: '',
        isNeuter: null,
        canSendCity: [],
        des: '',
        healthDes: '',
        cond: '',
        img: ''
    })
    const selectedImg = ref<{ index: number | null; originalUrl: string, previewUrl: string }>({
        index: null ,
        originalUrl: '',
        previewUrl: ''
    })
    const previewImgList = ref<string[]>(['', '', ''])
    const originalImgList = ref<string[]>(['', '', ''])

    const initTown = watch(
        () => formData.city,
        () => {
            formData.town = ''
        })

    const initColor = watch(
        () => formData.type,
        () => {
            formData.color
        }
    )

    function updateImg(index: number, url: string) {
        selectedImg.value = {
            index,
            previewUrl: url,
            originalUrl: originalImgList.value[index]
        }
        showModal.value = true
    }

    function saveConfirmImg(imgUrl: { original: string ; preview: string }) {
        const index = selectedImg.value.index
        if (index !== null) {
            previewImgList.value[index] = imgUrl.preview
            originalImgList.value[index] = imgUrl.original
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

    function submit() {
        
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
</style>
