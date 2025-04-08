<template>
    <div class="modal fade"
        id="update-img-modal"
        tabindex="-1"
        aria-label="上傳圖片"
        aria-hidden="true"
    >
        <div class="modal-dialog update-img__modal">
            <div class="modal-content">
                <div class="modal-header me-3 mt-3">
                    <button type="button" @click="cancelClick()" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center my-2 gap-5">
                    <div class="update-img__upload d-flex flex-column overflow-hidden">
                        <template v-if="!imgUrl.original">
                            <label for="choose-img" class="update-img__choose d-flex justify-content-center align-items-center rounded-circle mt-4 ms-4 fs-1 fw-bold">
                                <input type="file" accept="image/*" id="choose-img" class="d-none" @change="previewImg">
                                <span>+</span>
                            </label>
                            <span class="d-flex justify-content-center align-items-center flex-grow-1 fs-3">圖檔將上傳至此</span>
                        </template>
                        <template v-if="imgUrl.original">
                            <ImgCropper :originalImg="imgUrl.original" @update:preview-img="savePreviewImg" />
                        </template>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-5 fw-medium">裁減預覽</span>
                        <img src="@public/icons/arrow-right.svg" class="update-img__icon--arrow">
                    </div>
                    <div class="update-img__preview d-flex justify-content-center align-items-center overflow-hidden">
                        <img v-if="!imgUrl.preview" src="`@public/icons/image.svg`" class="update-img__icon--img">
                        <img v-if="imgUrl.preview" :src="imgUrl.preview" class="w-100 h-100 object-fit-contain">
                    </div>
                </div>
                <div class="modal-footer mx-5 mb-3">
                    <div>
                        <button type="button" @click="resetUpload()" class="update-img__btn--dark btn px-5 py-2">重新上傳</button>
                    </div>
                    <div class="ms-auto">
                        <button type="button" @click="cancelClick()" class="update-img__btn--dark btn me-3 px-4 py-2" data-bs-dismiss="modal" aria-label="Close">取消</button>
                        <button type="button" @click="confirmImg()" class="update-img__btn--light btn px-4 py-2">確認</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts" name="UpdateImg">
    import { ref, watch } from 'vue'
    import ImgCropper from '@/components/ImgCropper.vue'

    const { selectedImg } = defineProps<{ selectedImg: { index: number | null; originalUrl: string, previewUrl: string } }>()

    const emit = defineEmits<{
        (event: 'update:confirm-img', value: { original: string; preview: string }): void
    }>()

    const imgUrl = ref<{ original: string | null; preview: string | null }>({
        original: '',
        preview: ''
    })

    const previewImg = (event: Event) => {
        const target = event.target as HTMLInputElement
        if (!target.files || target.files.length === 0) return 
        imgUrl.value.original = URL.createObjectURL(target.files[0])
    }

    watch(() => selectedImg, (newVal) => {
        if (newVal) {
            Object.assign(imgUrl.value, {
                original: newVal.originalUrl,
                preview: newVal.previewUrl
            })
        }
    }, { deep: true })

    function savePreviewImg(url: string) {
        imgUrl.value.preview = url
    }

    function resetUpload() {
        imgUrl.value.original = null
        imgUrl.value.preview = null
    }

    function confirmImg() {
        if (imgUrl.value.original === null || imgUrl.value.preview === null ) {
            alert('請先上傳圖檔')
            return
        }
        emit('update:confirm-img', {
            original: imgUrl.value.original,
            preview: imgUrl.value.preview
        })
        resetUpload()
    }

    function cancelClick() {
        if (selectedImg.originalUrl !== '') return
        resetUpload()
    }
</script>

<style scoped>
    button {
        font-size: large;
    }

    .update-img__modal {
        --bs-modal-width: 960px
    }

    .update-img__upload {
        width: 400px;
        height: 400px;
        aspect-ratio: 1;
        background-color: #f2f2f2;
        color: #bfbfbf;
        border: 2px dashed #ccc;
        border-radius: 5%;
    }

    .update-img__choose {
        width: 70px;
        height: 70px;
        background-color: #3d3d5c;
        color: #fff2cc;
        position: absolute;
        box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
    }

    .update-img__preview {
        width: 250px;
        height: 250px;
        color: #bfbfbf;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.25);
        border-radius: 5%;
    }

    .update-img__icon--arrow {
        width: 85px;
        height: 85px;
    }

    .update-img__icon--img {
        width: 50px;
        height: 50px;
    }

    .update-img__btn--dark {
        background-color: #d6d6d4;
    }

    .update-img__btn--light {
        background-color: #3d3d5c;
        color: #fff2cc;
    }
</style>