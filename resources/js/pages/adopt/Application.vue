<template>
    <Navbar />
    <Content :title="$t('Adoption Application Form')">
        <template #content>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Pet Summary Card -->
                    <div v-if="pet" class="card border-0 shadow-sm mb-4 overflow-hidden">
                        <div class="card-body p-0 d-flex">
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                style="width: 120px;">
                                <img v-if="pet.images && pet.images.length > 0" :src="`/storage/${pet.images[0].path}`"
                                    class="w-100 h-100 object-fit-cover">
                                <i v-else class="bi bi-image text-secondary fs-1"></i>
                            </div>
                            <div class="p-3 flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1">{{ $t('Apply for:') }}{{ pet.name }}</h5>
                                <p class="text-secondary mb-0 small">{{ pet.breed }} · {{ pet.gender === 'male' ?
                                    $t('Male') :
                                    $t('Female') }} · {{ pet.age }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="application-form__wrapper">
                        <h2 class="mb-4 fs-3 fw-bold text-dark text-center">{{ $t('Adoption Application Form') }}</h2>

                        <form @submit.prevent="submitApplication" class="d-flex flex-column gap-4">
                            <!-- Applicant Info -->
                            <section class="application-form__section">
                                <h3 class="application-form__section-title">{{ $t('Applicant Info') }}</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label
                                            class="form-label application-form__label application-form__label--required">{{
                                                $t('Nickname') }}</label>
                                        <input type="text" class="form-control application-form__input"
                                            v-model="form.name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label application-form__label">Email</label>
                                        <input type="email" class="form-control application-form__input"
                                            :value="authStore.user?.email" disabled readonly>
                                    </div>
                                    <div class="col-12">
                                        <label for="phone"
                                            class="form-label application-form__label application-form__label--required">{{
                                                $t('Phone Number') }}</label>
                                        <input type="tel" id="phone" class="form-control application-form__input"
                                            v-model="form.phone" required
                                            :placeholder="$t('Enter phone number')">
                                    </div>
                                    <div class="col-12">
                                        <label for="line_id" class="form-label application-form__label">{{
                                            $t('Line ID (Optional)') }}</label>
                                        <input type="text" id="line_id" class="form-control application-form__input"
                                            v-model="form.line_id" :placeholder="$t('For owner to contact you')">
                                    </div>
                                </div>
                            </section>

                            <!-- Environment & Experience -->
                            <section class="application-form__section">
                                <h3 class="application-form__section-title">{{ $t('Environment & Experience') }}</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="housing"
                                            class="form-label application-form__label application-form__label--required">{{
                                                $t('Housing Environment') }}</label>
                                        <select id="housing" class="form-select application-form__input"
                                            v-model="form.housing_type" required>
                                            <option value="" disabled>{{ $t('Please select') }}</option>
                                            <option value="apartment">{{ $t('Housing.Apartment') }}</option>
                                            <option value="house">{{ $t('Housing.House') }}</option>
                                            <option value="condo">{{ $t('Housing.Condo') }}</option>
                                            <option value="dormitory">{{ $t('Housing.Dormitory') }}</option>
                                            <option value="other">{{ $t('Housing.Other') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="experience"
                                            class="form-label application-form__label application-form__label--required">{{
                                                $t('Pet Experience') }}</label>
                                        <select id="experience" class="form-select application-form__input"
                                            v-model="form.experience" required>
                                            <option value="" disabled>{{ $t('Please select') }}</option>
                                            <option value="none">{{ $t('None') }}</option>
                                            <option value="newbie">{{ $t('Experience.Newbie') }}</option>
                                            <option value="experienced">{{ $t('Experience.Experienced') }}
                                            </option>
                                            <option value="expert">{{ $t('Experience.Expert') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label
                                            class="form-label application-form__label application-form__label--required">{{
                                                $t('Does family/roommate agree?') }}</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="family_agreement"
                                                    id="agree_yes" :value="true" v-model="form.family_agreement"
                                                    required>
                                                <label class="form-check-label" for="agree_yes">{{
                                                    $t('Yes, all agreed') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="family_agreement"
                                                    id="agree_no" :value="false" v-model="form.family_agreement">
                                                <label class="form-check-label" for="agree_no">{{
                                                    $t('Not yet confirmed') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Message -->
                            <section class="application-form__section">
                                <h3 class="application-form__section-title">{{ $t('Message to Owner') }}
                                </h3>
                                <div class="mb-3">
                                    <label for="message"
                                        class="form-label application-form__label application-form__label--required">{{
                                            $t('Self-introduction & Motivation') }}</label>
                                    <textarea id="message" class="form-control application-form__input" rows="5"
                                        v-model="form.message" required
                                        :placeholder="$t('Briefly introduce yourself...')"></textarea>
                                </div>
                            </section>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill"
                                    @click="$router.back()">
                                    {{ $t('Cancel and return') }}
                                </button>
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm"
                                    :disabled="isSubmitting">
                                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ isSubmitting ? $t('Sending...') : $t('Confirm and Submit') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAdoptStore } from '@/stores/adopt'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import Swal from 'sweetalert2'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import { trans } from 'laravel-vue-i18n'

const $t = trans

const route = useRoute()
const router = useRouter()
const adoptStore = useAdoptStore()
const authStore = useAuthStore()

const petId = computed(() => route.params.id as string)
const pet = ref<any>(null)
const isSubmitting = ref(false)

const form = reactive({
    name: '',
    phone: '',
    line_id: '',
    housing_type: '',
    experience: '',
    family_agreement: null,
    message: ''
})

async function fetchPetDetail() {
    try {
        const data = await adoptStore.fetchPetById(petId.value)
        pet.value = data
    } catch (error) {
        console.error('Failed to fetch pet:', error)
        Swal.fire({
            icon: 'error',
            title: $t('Error'),
            text: $t('Unable to load pet details')
        })
        router.push('/adopt')
    }
}

// Watch for user changes to pre-fill form
watch(() => authStore.user, (newUser) => {
    if (newUser && newUser.name && !form.name) {
        form.name = newUser.name
    }
}, { immediate: true })

async function submitApplication() {
    if (isSubmitting.value) return

    isSubmitting.value = true
    try {
        await axios.post(`/api/adopt/${petId.value}/apply`, form)

        await Swal.fire({
            icon: 'success',
            title: $t('Application Submitted!'),
            text: $t('The owner will be notified.'),
            confirmButtonText: $t('Back to Pet Page'),
            confirmButtonColor: '#2c5282'
        })

        router.push(`/adopt/${petId.value}`)
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: $t('Submission Failed'),
            text: error.response?.data?.message || $t('Please try again later')
        })
    } finally {
        isSubmitting.value = false
    }
}

onMounted(async () => {
    await Promise.all([fetchPetDetail(), authStore.fetchUser()])
})
</script>

<style scoped>
.application-form__wrapper {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    border: 1px solid #dee2e6;
}

.application-form__section {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.application-form__section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-denim-blue);
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-fog-gray);
}

.application-form__label {
    font-weight: 600;
    color: #495057;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

.application-form__label--required::after {
    content: "*";
    color: #dc3545;
    margin-left: 0.25rem;
}

.application-form__input {
    border-radius: 8px;
    border: 1px solid #ced4da;
    padding: 0.6rem 0.75rem;
}

.application-form__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.25rem rgba(44, 82, 130, 0.15);
}
</style>
