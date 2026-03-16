import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { trans } from 'laravel-vue-i18n'
import axios from 'axios'
import Swal from 'sweetalert2'

export function useManualCaseCreation() {
    const $t = trans
    const router = useRouter()

    const step = ref(1)
    const submitting = ref(false)
    const imagePreview = ref<string | null>(null)
    const fileInput = ref<HTMLInputElement | null>(null)

    const searchQuery = ref('')
    const lookupLoading = ref(false)
    const lookupError = ref('')
    const selectedCounterpart = ref<{ id: number; name: string; email: string } | null>(null)

    const form = ref({
        role: '' as string,
        pet_name: '',
        pet_type: '' as string,
        gender: '' as string,
        age: '' as string,
        color: '' as string,
        fur_type: '' as string,
        is_neuter: null as boolean | null,
        is_stray: null as boolean | null,
        city: '' as string,
        town: '' as string,
        pet_image: null as File | null,
        frequency: 'monthly' as string,
        tracking_day: null as number | null,
        tracking_start_month: null as number | null,
        counterpart_id: null as number | null,
    })

    const canProceed = computed(() => {
        if (step.value === 1) return form.value.role !== ''
        if (step.value === 2) {
            return form.value.pet_name.trim() !== ''
                && form.value.pet_type !== ''
                && form.value.gender !== ''
                && form.value.age !== ''
                && form.value.color !== ''
                && form.value.fur_type !== ''
                && form.value.is_neuter !== null
                && form.value.is_stray !== null
                && form.value.city !== ''
                && form.value.town !== ''
        }
        if (step.value === 3) {
            const hasCounterpart = form.value.counterpart_id !== null
            const hasFrequency = form.value.frequency !== ''
            let hasTrackingDetails = false

            if (form.value.frequency === 'weekly') {
                hasTrackingDetails = form.value.tracking_day !== null
            } else if (form.value.frequency === 'monthly') {
                hasTrackingDetails = form.value.tracking_day !== null
            } else if (form.value.frequency === 'quarterly') {
                hasTrackingDetails = form.value.tracking_day !== null && form.value.tracking_start_month !== null
            }

            return hasCounterpart && hasFrequency && hasTrackingDetails
        }
        return true
    })

    function handleFileChange(e: Event) {
        const target = e.target as HTMLInputElement
        const file = target.files?.[0]
        if (file) {
            form.value.pet_image = file
            imagePreview.value = URL.createObjectURL(file)
        }
    }

    function handleDrop(e: DragEvent) {
        const file = e.dataTransfer?.files?.[0]
        if (file && file.type.startsWith('image/')) {
            form.value.pet_image = file
            imagePreview.value = URL.createObjectURL(file)
        }
    }

    function removeImage() {
        form.value.pet_image = null
        imagePreview.value = null
    }

    async function lookupUser() {
        lookupError.value = ''
        if (!searchQuery.value || !searchQuery.value.includes('@')) {
            lookupError.value = $t('case.LookupInvalidEmail')
            return
        }
        lookupLoading.value = true
        try {
            const res = await axios.post('/api/users/lookup', {
                email: searchQuery.value.trim()
            })
            if (res.data.found) {
                selectedCounterpart.value = res.data.user
                form.value.counterpart_id = res.data.user.id
                searchQuery.value = ''
            } else {
                lookupError.value = $t('case.LookupNotFound')
            }
        } catch {
            lookupError.value = $t('case.LookupError')
        } finally {
            lookupLoading.value = false
        }
    }

    function removeCounterpart() {
        selectedCounterpart.value = null
        form.value.counterpart_id = null
    }

    async function submitCase() {
        submitting.value = true

        try {
            const formData = new FormData()
            formData.append('role', form.value.role)
            formData.append('pet_name', form.value.pet_name)
            formData.append('pet_type', form.value.pet_type)
            formData.append('gender', form.value.gender)
            formData.append('age', form.value.age)
            formData.append('color', form.value.color)
            formData.append('fur_type', form.value.fur_type)
            formData.append('is_neuter', form.value.is_neuter ? '1' : '0')
            formData.append('is_stray', form.value.is_stray ? '1' : '0')
            formData.append('city', form.value.city)
            formData.append('town', form.value.town)
            if (form.value.pet_image) {
                formData.append('pet_image', form.value.pet_image)
            }
            if (form.value.frequency) {
                formData.append('tracking_config[frequency]', form.value.frequency)
                if (form.value.tracking_day !== null) {
                    formData.append('tracking_config[tracking_day]', String(form.value.tracking_day))
                }
                if (form.value.tracking_start_month !== null) {
                    formData.append('tracking_config[tracking_start_month]', String(form.value.tracking_start_month))
                }
            }
            if (form.value.counterpart_id) {
                formData.append('counterpart_id', String(form.value.counterpart_id))
            }

            await axios.post('/api/adoption-cases/manual', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })

            await Swal.fire({
                icon: 'success',
                title: $t('case.SuccessTitle'),
                text: $t('case.SuccessMessage'),
                confirmButtonColor: '#2c5282',
            })

            // Navigate to the appropriate management page
            if (form.value.role === 'owner') {
                router.push('/user/adoptions?tab=history')
            } else {
                router.push('/user/adopted')
            }
        } catch (err: any) {
            const message = err.response?.data?.message || $t('case.ErrorMessage')
            Swal.fire({
                icon: 'error',
                title: $t('case.ErrorTitle'),
                text: message,
                confirmButtonColor: '#2c5282',
            })
        } finally {
            submitting.value = false
        }
    }

    return {
        step,
        submitting,
        imagePreview,
        fileInput,
        searchQuery,
        lookupLoading,
        lookupError,
        selectedCounterpart,
        form,
        canProceed,
        handleFileChange,
        handleDrop,
        removeImage,
        lookupUser,
        removeCounterpart,
        submitCase
    }
}
