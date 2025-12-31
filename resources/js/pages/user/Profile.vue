<template>
    <Navbar />
    <Content title="個人資料">
        <template v-slot:content>
            <!-- Loading State -->
            <div v-if="loading" class="text-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">載入中...</span>
                </div>
                <p class="mt-3 text-secondary">載入中...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="alert alert-danger mx-auto" style="max-width: 600px;" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ error }}
            </div>

            <!-- Profile Content -->
            <div v-else-if="userProfile" class="profile">
                <!-- User Info Card -->
                <div class="profile__card mb-4">
                    <div class="profile__header">
                        <div class="profile__avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="profile__info">
                            <h2 class="profile__name">{{ userProfile.name }}</h2>
                            <!-- <div class="profile__badge">
                                <i class="bi bi-patch-check-fill me-1"></i>
                                <span>已驗證會員</span>
                            </div> -->
                        </div>
                    </div>

                    <div class="profile__details">
                        <div class="profile__detail-item">
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <span class="profile__label">電子信箱</span>
                                <span class="profile__value">{{ userProfile.email }}</span>
                            </div>
                        </div>
                        <div class="profile__detail-item">
                            <i class="bi bi-person-badge-fill"></i>
                            <div>
                                <span class="profile__label">會員編號</span>
                                <span class="profile__value">{{ userProfile.id }}</span>
                            </div>
                        </div>
                        <div class="profile__detail-item">
                            <i class="bi bi-calendar-check-fill"></i>
                            <div>
                                <span class="profile__label">註冊時間</span>
                                <span class="profile__value">{{ formatDate(userProfile.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                <!-- <div class="profile__stats-title mb-3">
                    <h3 class="fs-5 fw-bold text-dark">送養統計</h3>
                </div>
                <div class="profile__stats">
                    <div class="profile__stat-card profile__stat-card--adopt">
                        <div class="profile__stat-icon">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div class="profile__stat-info">
                            <div class="profile__stat-number">0</div>
                            <div class="profile__stat-label">送養案件</div>
                        </div>
                        <RouterLink to="" class="profile__stat-link">
                            查看清單 <i class="bi bi-arrow-right"></i>
                        </RouterLink>
                    </div>

                    <div class="profile__stat-card profile__stat-card--adopted">
                        <div class="profile__stat-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="profile__stat-info">
                            <div class="profile__stat-number">0</div>
                            <div class="profile__stat-label">已完成送養</div>
                        </div>
                        <RouterLink to="" class="profile__stat-link">
                            查看清單 <i class="bi bi-arrow-right"></i>
                        </RouterLink>
                    </div>

                    <div class="profile__stat-card profile__stat-card--saved">
                        <div class="profile__stat-icon">
                            <i class="bi bi-bookmark-heart-fill"></i>
                        </div>
                        <div class="profile__stat-info">
                            <div class="profile__stat-number">0</div>
                            <div class="profile__stat-label">收藏案件</div>
                        </div>
                        <RouterLink to="" class="profile__stat-link">
                            查看清單 <i class="bi bi-arrow-right"></i>
                        </RouterLink>
                    </div>
                </div> -->
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import { UserProfileInter } from '@/types/user'

const route = useRoute()
const userProfile = ref<UserProfileInter | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

// Use computed to make userId reactive to route changes
const userId = computed(() => route.params.id)

async function fetchUserProfile() {
    loading.value = true
    error.value = null

    try {
        const response = await axios.get(`/api/users/${userId.value}`)
        userProfile.value = response.data
    } catch (err: any) {
        error.value = err.response?.data?.message || '無法載入使用者資料'
        console.error('Failed to fetch user profile:', err)
    } finally {
        loading.value = false
    }
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Watch for route param changes
watch(() => route.params.id, (newId, oldId) => {
    if (newId && newId !== oldId) {
        fetchUserProfile()
    }
})

onMounted(() => {
    fetchUserProfile()
})
</script>

<style scoped>
.profile {
    max-width: 800px;
    margin: 0 auto;
}

/* User Info Card */
.profile__card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.profile__header {
    background: linear-gradient(135deg, #b8c5d6 0%, #d1d9e6 100%);
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.profile__avatar {
    font-size: 5rem;
    color: #2c5282;
    line-height: 1;
}

.profile__avatar i {
    filter: drop-shadow(0 2px 4px rgba(44, 82, 130, 0.3));
}

.profile__info {
    flex: 1;
}

.profile__name {
    color: #2c5282;
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 1px 2px rgba(44, 82, 130, 0.1);
}

.profile__badge {
    display: inline-flex;
    align-items: center;
    background: rgba(44, 82, 130, 0.15);
    backdrop-filter: blur(10px);
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    color: #2c5282;
    font-size: 0.875rem;
    font-weight: 600;
}

.profile__details {
    padding: 1.5rem 2rem 2rem;
    background: linear-gradient(to bottom, #f7fafc 0%, #ffffff 100%);
}

.profile__detail-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.profile__detail-item:last-child {
    border-bottom: none;
}

.profile__detail-item>i {
    font-size: 1.5rem;
    color: #3182ce;
    margin-top: 0.25rem;
}

.profile__detail-item>div {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.profile__label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.profile__value {
    font-size: 1rem;
    color: #2d3748;
    font-weight: 600;
}

/* Statistics Section */
/* .profile__stats-title {
    margin-top: 2rem;
}

.profile__stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.profile__stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.profile__stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    border-color: #3182ce;
}

.profile__stat-card--adopt {
    border-left: 4px solid #2c5282;
    background: linear-gradient(to bottom right, #ffffff 0%, #e6f0f7 100%);
}

.profile__stat-card--adopt:hover {
    background: linear-gradient(to bottom right, #f0f9ff 0%, #dbeafe 100%);
}

.profile__stat-card--adopted {
    border-left: 4px solid #4a7ba7;
    background: linear-gradient(to bottom right, #ffffff 0%, #f0f6fa 100%);
}

.profile__stat-card--adopted:hover {
    background: linear-gradient(to bottom right, #f8fbfd 0%, #e8f2f7 100%);
}

.profile__stat-card--saved {
    border-left: 4px solid #7ba3c5;
    background: linear-gradient(to bottom right, #ffffff 0%, #f5f9fc 100%);
}

.profile__stat-card--saved:hover {
    background: linear-gradient(to bottom right, #fafcfe 0%, #f0f6fa 100%);
}

.profile__stat-icon {
    font-size: 1.5rem;
    line-height: 1;
}

.profile__stat-card--adopt .profile__stat-icon {
    color: #2c5282;
}

.profile__stat-card--adopted .profile__stat-icon {
    color: #4a7ba7;
}

.profile__stat-card--saved .profile__stat-icon {
    color: #7ba3c5;
}

.profile__stat-info {
    flex: 1;
}

.profile__stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    line-height: 1.2;
}

.profile__stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
    margin-top: 0.25rem;
}

.profile__stat-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #3182ce;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.profile__stat-link:hover {
    gap: 0.75rem;
    color: #4f46e5;
} */

/* Responsive */
@media (max-width: 768px) {
    .profile__header {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem;
    }

    .profile__details {
        padding: 1rem 1.5rem 1.5rem;
    }

    .profile__stats {
        grid-template-columns: 1fr;
    }
}
</style>
