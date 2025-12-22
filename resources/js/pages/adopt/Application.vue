<template>
    <Navbar />
    <Content title="領養申請">
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
                                <h5 class="fw-bold text-dark mb-1">申請領養：{{ pet.name }}</h5>
                                <p class="text-secondary mb-0 small">{{ pet.breed }} · {{ pet.gender === 'male' ? '男生' :
                                    '女生' }} · {{ pet.age }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="application-form__wrapper">
                        <h2 class="mb-4 fs-3 fw-bold text-dark text-center">領養申請表</h2>

                        <form @submit.prevent="submitApplication" class="d-flex flex-column gap-4">
                            <!-- Applicant Info -->
                            <section class="application-form__section">
                                <h3 class="application-form__section-title">申請人資料</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label
                                            class="form-label application-form__label application-form__label--required">暱稱</label>
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
                                            class="form-label application-form__label application-form__label--required">聯絡電話</label>
                                        <input type="tel" id="phone" class="form-control application-form__input"
                                            v-model="form.phone" required placeholder="請輸入聯絡電話">
                                    </div>
                                    <div class="col-12">
                                        <label for="line_id" class="form-label application-form__label">Line ID
                                            (選填)</label>
                                        <input type="text" id="line_id" class="form-control application-form__input"
                                            v-model="form.line_id" placeholder="方便送養者聯繫">
                                    </div>
                                </div>
                            </section>

                            <!-- Environment & Experience -->
                            <section class="application-form__section">
                                <h3 class="application-form__section-title">環境與經驗</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="housing"
                                            class="form-label application-form__label application-form__label--required">居住環境</label>
                                        <select id="housing" class="form-select application-form__input"
                                            v-model="form.housing_type" required>
                                            <option value="" disabled>請選擇</option>
                                            <option value="apartment">公寓/大樓</option>
                                            <option value="house">透天/別墅</option>
                                            <option value="suite">套房</option>
                                            <option value="other">其他</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="experience"
                                            class="form-label application-form__label application-form__label--required">養寵經驗</label>
                                        <select id="experience" class="form-select application-form__input"
                                            v-model="form.experience" required>
                                            <option value="" disabled>請選擇</option>
                                            <option value="none">無經驗</option>
                                            <option value="newbie">新手 (1年以下)</option>
                                            <option value="experienced">有經驗 (1-5年)</option>
                                            <option value="expert">資深 (5年以上)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label
                                            class="form-label application-form__label application-form__label--required">同住家人/室友是否同意？</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="family_agreement"
                                                    id="agree_yes" :value="true" v-model="form.family_agreement"
                                                    required>
                                                <label class="form-check-label" for="agree_yes">是，皆已同意</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="family_agreement"
                                                    id="agree_no" :value="false" v-model="form.family_agreement">
                                                <label class="form-check-label" for="agree_no">尚未確認/溝通中</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Message -->
                            <section class="application-form__section">
                                <h3 class="application-form__section-title">給送養者的話</h3>
                                <div class="mb-3">
                                    <label for="message"
                                        class="form-label application-form__label application-form__label--required">自我介紹與申請動機</label>
                                    <textarea id="message" class="form-control application-form__input" rows="5"
                                        v-model="form.message" required
                                        placeholder="請簡單介紹自己，並說明為什麼想領養這隻動物..."></textarea>
                                </div>
                            </section>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill"
                                    @click="$router.back()">
                                    取消返回
                                </button>
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm"
                                    :disabled="isSubmitting">
                                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ isSubmitting ? '送出中...' : '確認送出申請' }}
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
            title: '錯誤',
            text: '無法載入寵物資料'
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
        // TODO: Implement API endpoint
        // await axios.post(`/api/adopt/${petId.value}/apply`, form)

        // Mock success for now
        await new Promise(resolve => setTimeout(resolve, 1000))

        await Swal.fire({
            icon: 'success',
            title: '申請已送出！',
            text: '送養者將會收到您的申請通知，請耐心等候聯繫。',
            confirmButtonText: '返回寵物頁面',
            confirmButtonColor: '#2c5282'
        })

        router.push(`/adopt/${petId.value}`)
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: '申請失敗',
            text: error.response?.data?.message || '請稍後再試'
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
