<template>
    <Navbar />
    <Content title="帳號設定">
        <template v-slot:content>
            <div class="settings__content">
                <!-- Profile Section -->
                <div class="settings__section mb-5">
                    <h3 class="settings__section-title">基本資料</h3>
                    <div class="settings__card">
                        <div class="mb-4 text-center">
                            <div class="settings__avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                        </div>

                        <form @submit.prevent="updateProfile">
                            <div class="mb-3">
                                <label class="form-label">電子郵件</label>
                                <input type="email" class="form-control" :value="user?.email" disabled readonly>
                                <div class="form-text">電子郵件無法修改</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">顯示名稱</label>
                                <input type="text" class="form-control" v-model="profileForm.name"
                                    placeholder="請輸入您的名稱">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                                    儲存變更
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="settings__section">
                    <h3 class="settings__section-title">安全設定</h3>
                    <div class="settings__card">
                        <form @submit.prevent="updatePassword">
                            <div class="mb-3">
                                <label class="form-label">目前密碼</label>
                                <input type="password" class="form-control" v-model="passwordForm.current_password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">新密碼</label>
                                <input type="password" class="form-control" v-model="passwordForm.password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">確認新密碼</label>
                                <input type="password" class="form-control"
                                    v-model="passwordForm.password_confirmation">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-outline-primary" :disabled="loading">
                                    修改密碼
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Settings">
import { onMounted, reactive, computed } from 'vue'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'

import { trans } from 'laravel-vue-i18n'

const authStore = useAuthStore()
const user = computed(() => authStore.user)
const loading = computed(() => authStore.loading)

const profileForm = reactive({
    name: ''
})

const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: ''
})

onMounted(async () => {
    await authStore.fetchUser()
    if (authStore.user) {
        profileForm.name = authStore.user.name
    }
})

async function updateProfile() {
    try {
        await authStore.updateProfile(profileForm)

        await Swal.fire({
            icon: 'success',
            title: '更新成功',
            text: '您的個人資料已更新',
            timer: 1500,
            showConfirmButton: false
        })
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '更新失敗',
            text: authStore.error || '請稍後再試'
        })
    }
}

async function updatePassword() {
    if (passwordForm.password !== passwordForm.password_confirmation) {
        Swal.fire({
            icon: 'error',
            title: '密碼不符',
            text: '新密碼與確認密碼不一致'
        })
        return
    }

    try {
        await authStore.updatePassword(passwordForm)

        await Swal.fire({
            icon: 'success',
            title: '修改成功',
            text: '您的密碼已更新',
            timer: 1500,
            showConfirmButton: false
        })

        // Reset form
        passwordForm.current_password = ''
        passwordForm.password = ''
        passwordForm.password_confirmation = ''
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '修改失敗',
            text: authStore.error ? trans(authStore.error) : '請確認您的目前密碼是否正確'
        })
    }
}
</script>

<style scoped>
.settings__section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
    padding-left: 0.5rem;
    border-left: 4px solid #3182ce;
}

.settings__card {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.settings__avatar {
    font-size: 4rem;
    color: #cbd5e0;
    line-height: 1;
}

.form-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
}

.form-control {
    border-radius: 8px;
    padding: 0.6rem 1rem;
    border-color: #e2e8f0;
}

.form-control:focus {
    border-color: #3182ce;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

.btn-primary {
    background: #3182ce;
    border-color: #3182ce;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
}

.btn-primary:hover {
    background: #2c5282;
    border-color: #2c5282;
}

.btn-outline-primary {
    color: #3182ce;
    border-color: #3182ce;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
}

.btn-outline-primary:hover {
    background: #ebf8ff;
    color: #2c5282;
    border-color: #2c5282;
}
</style>