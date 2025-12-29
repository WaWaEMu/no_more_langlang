<template>
    <Navbar />
    <Content title="領養申請管理">
        <template v-slot:content>
            <div class="applications__container mx-auto">
                <!-- Header Section -->
                <div class="mb-4">
                    <h2 class="fw-bold text-dark mb-1">領養申請管理</h2>
                    <p class="text-muted mb-0" v-if="!loading">
                        共 {{ totalApplications }} 筆申請
                    </p>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">載入申請資料中...</p>
                </div>

                <!-- Content State -->
                <div v-else>
                    <div v-if="groupedApplications.length > 0" class="d-flex flex-column gap-4">
                        <!-- Pet Group Card -->
                        <div v-for="group in groupedApplications" :key="group.pet.id" class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom-0 p-3 d-flex align-items-center gap-3">
                                <!-- Pet Image -->
                                <div class="applications__pet-image rounded-circle overflow-hidden flex-shrink-0">
                                    <img :src="group.pet.image ? `/storage/${group.pet.image}` : '/images/default-pet.png'"
                                        :alt="group.pet.name" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark">{{ group.pet.name }}</h5>
                                    <span class="text-muted small">{{ group.applications.length }} 筆申請</span>
                                </div>
                                <div class="ms-auto">
                                    <RouterLink :to="`/adopt/${group.pet.id}`"
                                        class="btn btn-sm btn-outline-secondary rounded-pill">
                                        查看寵物頁面
                                    </RouterLink>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div v-for="app in group.applications" :key="app.id"
                                        class="list-group-item p-3 border-0 border-top applications__item"
                                        :class="{ 'applications__item--highlight': highlightId === app.id }"
                                        :id="`application-${app.id}`">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-primary">{{ app.applicant_name }}</span>
                                                <span class="badge rounded-pill" :class="getStatusClass(app.status)">
                                                    {{ getStatusText(app.status) }}
                                                </span>
                                            </div>
                                            <small class="text-muted">{{ formatDate(app.created_at) }}</small>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">聯絡電話</small>
                                                <span>{{ app.phone }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">居住類型</small>
                                                <span>{{ app.housing_type }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">飼養經驗</small>
                                                <span>{{ app.experience }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">家人同意</small>
                                                <span :class="app.family_agreement ? 'text-success' : 'text-danger'">
                                                    {{ app.family_agreement ? '已同意' : '未確認' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="application__message p-3 rounded-3 mb-3">
                                            <small class="text-muted d-block mb-1">自我介紹與動機</small>
                                            <p class="mb-0 text-secondary" style="white-space: pre-wrap;">
                                                {{ app.message }}
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2">
                                            <a v-if="app.line_id" :href="`https://line.me/ti/p/~${app.line_id}`"
                                                target="_blank" class="btn btn-sm btn-success text-white">
                                                <i class="bi bi-line me-1"></i>LINE 聯繫
                                            </a>
                                            <!-- Future: Add Approve/Reject buttons here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-5 bg-light rounded-3 border border-dashed">
                        <div class="mb-3">
                            <i class="bi bi-inbox display-1 text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-bold text-secondary mb-2">目前沒有領養申請</h4>
                        <p class="text-muted mb-0">當有人申請領養您的寵物時，會顯示在這裡。</p>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import axios from 'axios'

interface Application {
    id: number
    applicant_name: string
    applicant_user: { id: number, name: string } | null
    phone: string
    line_id: string | null
    housing_type: string
    experience: string
    family_agreement: boolean
    message: string
    status: string
    created_at: string
}

interface PetGroup {
    pet: {
        id: number
        name: string
        image: string | null
    }
    applications: Application[]
}

const route = useRoute()
const loading = ref(true)
const groupedApplications = ref<PetGroup[]>([])
const highlightId = ref<number | null>(null)

const totalApplications = computed(() => {
    return groupedApplications.value.reduce((total, group) => total + group.applications.length, 0)
})

async function fetchApplications() {
    try {
        const response = await axios.get('/api/user/applications')
        groupedApplications.value = response.data.data
    } catch (error) {
        console.error('Failed to fetch applications:', error)
    } finally {
        loading.value = false
    }
}

function getStatusClass(status: string) {
    switch (status) {
        case 'approved': return 'bg-success'
        case 'rejected': return 'bg-danger'
        default: return 'bg-warning text-dark'
    }
}

function getStatusText(status: string) {
    switch (status) {
        case 'approved': return '已通過'
        case 'rejected': return '已婉拒'
        default: return '待回覆'
    }
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleString('zh-TW', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

onMounted(async () => {
    await fetchApplications()

    // Handle highlight if query param exists
    const appId = route.query.highlight
    if (appId) {
        highlightId.value = Number(appId)
        await nextTick()
        const element = document.getElementById(`application-${appId}`)
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' })
            // Remove highlight after 2 seconds
            setTimeout(() => {
                highlightId.value = null
            }, 2000)
        }
    }
})
</script>

<style scoped>
.applications__container {
    max-width: 900px;
    padding: 0 1rem;
}

.applications__pet-image {
    width: 48px;
    height: 48px;
    background-color: var(--color-fog-gray);
}

.applications__item {
    transition: background-color 0.3s ease;
}

.applications__item:hover {
    background-color: #f8f9fa;
}

.applications__item--highlight {
    background-color: rgba(66, 91, 118, 0.15) !important;
}

.application__message {
    background-color: var(--color-fog-gray);
}
</style>
