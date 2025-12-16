<template>
    <div class="modal fade" id="update-img-modal" tabindex="-1" aria-label="上傳圖片" aria-hidden="true">
        <div class="modal-dialog update-img__modal">
            <div class="modal-content">
                <div class="modal-header me-3 mt-3">
                    <button type="button" @click="cancelClick()" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center my-2 gap-5">
                    <div class="update-img__upload d-flex flex-column overflow-hidden">
                        <template v-if="!imgUrl.original">
                            <label for="choose-img"
                                class="update-img__choose d-flex justify-content-center align-items-center rounded-circle mt-4 ms-4 fs-1 fw-bold">
                                <input type="file" accept="image/*" id="choose-img" class="d-none" @change="showImg">
                                <span>+</span>
                            </label>
                            <span
                                class="d-flex justify-content-center align-items-center flex-grow-1 fs-3">圖檔將上傳至此</span>
                        </template>
                        <template v-if="imgUrl.original">
                            <ImgCropper :originalImg="imgUrl.original" @update:preview-url="savePreviewUrl"
                                @update:image-blob="saveImageBlob" />
                        </template>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-5 fw-medium">裁減預覽</span>
                        <img src="@public/icons/arrow-right.svg" class="update-img__icon--arrow">
                    </div>
                    <div class="update-img__preview d-flex justify-content-center align-items-center overflow-hidden">
                        <img v-if="!imgUrl.preview" src="@public/icons/image.svg" class="update-img__icon--img">
                        <img v-if="imgUrl.preview" :src="imgUrl.preview" class="w-100 h-100 object-fit-contain">
                    </div>
                </div>
                <div class="modal-footer mx-5 mb-3">
                    <div>
                        <button type="button" @click="resetUpload()"
                            class="update-img__btn--dark btn px-5 py-2">重新上傳</button>
                    </div>
                    <div class="ms-auto">
                        <button type="button" @click="cancelClick()" class="update-img__btn--dark btn me-3 px-4 py-2"
                            data-bs-dismiss="modal" aria-label="Close">取消</button>
                        <button type="button" @click="confirmImg()"
                            class="update-img__btn--light btn px-4 py-2">確認</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts" name="UpdateImg">
import { ref, watch } from 'vue'
import ImgCropper from '@/components/ImgCropper.vue'

const emit = defineEmits<{
    (event: 'update:confirm-img', payload: { previewUrl: string, blob: Blob }): void
}>()

const imgUrl = ref<{ original: string | null; preview: string | null }>({
    original: '',
    preview: ''
})
const imgBlob = ref<Blob>()

// Show upload image
const showImg = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (!target.files || target.files.length === 0) return
    imgUrl.value.original = URL.createObjectURL(target.files[0])
}

function savePreviewUrl(url: string) {
    imgUrl.value.preview = url
}

function saveImageBlob(blob: Blob) {
    imgBlob.value = blob
}

function resetUpload() {
    imgUrl.value.original = null
    imgUrl.value.preview = null
}

function confirmImg() {
    if (imgUrl.value.original === null || imgUrl.value.preview === null) {
        alert('請先上傳圖檔')
        return
    }
    emit('update:confirm-img', {
        previewUrl: imgUrl.value.preview,
        blob: imgBlob.value!
    })
    resetUpload()
}

function cancelClick() {
    resetUpload()
}
</script>

<style scoped>
button {
    font-size: 1rem;
    letter-spacing: 0.025em;
}

.update-img__modal {
    --bs-modal-width: 960px;
}

.update-img__upload {
    width: 400px;
    height: 400px;
    aspect-ratio: 1;
    background-color: #f8fafc;
    color: #a0aec0;
    border: 2px dashed #cbd5e0;
    border-radius: 16px;
    position: relative;
    transition: border-color 0.2s ease;
}

.update-img__upload:hover {
    border-color: #a0aec0;
}

.update-img__choose {
    width: 64px;
    height: 64px;
    background-color: #2c5282;
    color: #ffffff;
    position: absolute;
    box-shadow: 0 4px 12px rgba(44, 82, 130, 0.3);
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 10;
}

.update-img__choose:hover {
    transform: scale(1.05);
    background-color: #2a4365;
}

.update-img__preview {
    width: 250px;
    height: 250px;
    color: #cbd5e0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 16px;
    background-color: #fff;
    border: 1px solid #e2e8f0;
}

.update-img__icon--arrow {
    width: 60px;
    height: 60px;
    opacity: 0.5;
}

.update-img__icon--img {
    width: 48px;
    height: 48px;
    opacity: 0.5;
}

.update-img__btn--dark {
    background-color: #e2e8f0;
    color: #4a5568;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
    border-radius: 8px;
}

.update-img__btn--dark:hover {
    background-color: #cbd5e0;
    color: #2d3748;
}

.update-img__btn--light {
    background-color: #2c5282;
    color: #ffffff;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(44, 82, 130, 0.2);
}

.update-img__btn--light:hover {
    background-color: #2a4365;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(44, 82, 130, 0.3);
}
</style>