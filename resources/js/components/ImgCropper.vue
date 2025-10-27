<template>
    <div class="w-100 h-100">
        <VueCropper v-bind="option"
            class="w-100 h-100"
            ref="cropper"
            @realTime="onRealTime"
        >
        </VueCropper>
    </div>
</template>999

<script setup lang="ts" name="ImgCropper">
import 'vue-cropper/dist/index.css'
import { VueCropper } from 'vue-cropper'
import { ref, reactive, watch } from 'vue'

const props = defineProps<{
    originalImg: string
}>()

const cropper = ref<VueCropper | null>(null)
const option = reactive({
    img: props.originalImg as string,
    outputSize: 1,
    outputType: 'jpeg',
    info: true,
    canScale: false,
    autoCrop: true,
    autoCropWidth: 250,
    autoCropHeight: 250,
    fixed: true,
    fixedBox: false,
    fixedNumber: [1, 1],
    canMove: true,
    canMoveBox: true,
    original: false,
    centerBox: true,
    infoTrue: true,
    full: false,
    enlarge: '1',
    mode: 'contain'
})
const emit = defineEmits<{
    (event: 'update:preview-url', value: any): void
    (event: 'update:image-blob', value: Blob): void
}>()
const previewImg = ref<any>(null)
const oldUrl = ref<string | null>(null)

watch(() => props.originalImg, (newVal) => {
    option.img = newVal
}, { immediate: true })

function onRealTime() {
    if (cropper.value) {
        cropper.value.getCropBlob((blob: Blob) => {
            const objUrl = URL.createObjectURL(blob)
            // Emit the preview URL to the parent component for image display
            emit('update:preview-url', objUrl)
            // Emit the Blob to the parent component for storage
            emit('update:image-blob', blob)

            if (oldUrl.value) {
                URL.revokeObjectURL(oldUrl.value)
            }
            oldUrl.value = objUrl
        })
    }
}
</script>