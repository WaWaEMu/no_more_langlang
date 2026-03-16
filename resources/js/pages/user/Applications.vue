<template>
    <Navbar />
    <Content :title="$t('Application Management')">
        <template v-slot:content>
            <div class="applications__container mx-auto">
                <!-- Header Section -->
                <div class="mb-4">
                    <h2 class="fw-bold text-dark mb-1">{{ $t('Application Management') }}</h2>
                    <p class="text-muted mb-0" v-if="!loading">
                        {{ activeTab === 'received' ? $t('Total received applications', {
                            count:
                                totalReceivedApplications.toString()
                        }) : $t('Total sent applications', {
                            count:
                                sentApplications.length.toString()
                        }) }}
                    </p>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-pills mb-4 applications__tabs p-1 rounded-pill">
                    <li class="nav-item flex-grow-1">
                        <button class="nav-link w-100 rounded-pill py-2" :class="{ active: activeTab === 'received' }"
                            @click="activeTab = 'received'">
                            {{ $t('Received Applications') }}
                        </button>
                    </li>
                    <li class="nav-item flex-grow-1">
                        <button class="nav-link w-100 rounded-pill py-2" :class="{ active: activeTab === 'sent' }"
                            @click="activeTab = 'sent'">
                            {{ $t('Sent Applications') }}
                        </button>
                    </li>
                </ul>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ $t('Loading...') }}</span>
                    </div>
                    <p class="mt-3 text-muted">{{ $t('Loading applications...') }}</p>
                </div>

                <!-- Content State -->
                <div v-else>
                    <!-- Received Applications Tab -->
                    <div v-if="activeTab === 'received'">
                        <div v-if="groupedApplications.length > 0" class="d-flex flex-column gap-4">
                            <!-- Pet Group Card -->
                            <div v-for="group in groupedApplications" :key="group.pet.id"
                                class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom-0 p-3 d-flex align-items-center gap-3">
                                    <!-- Pet Image -->
                                    <div class="applications__pet-image rounded-circle overflow-hidden flex-shrink-0">
                                        <img :src="group.pet.image ? `/storage/${group.pet.image}` : '/images/default-pet.png'"
                                            :alt="group.pet.name" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-dark">{{ group.pet.name }}</h5>
                                        <span class="text-muted small">{{ group.applications.length }} {{ $t('applications count') }}</span>
                                    </div>
                                    <div class="ms-auto">
                                        <RouterLink :to="`/adopt/${group.pet.id}`"
                                            class="btn btn-sm btn-outline-secondary rounded-pill">
                                            {{ $t('View Pet Page') }}
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
                                                    <span class="badge rounded-pill"
                                                        :class="getStatusClass(app.status)">
                                                        {{ getStatusText(app.status) }}
                                                    </span>
                                                </div>
                                                <small class="text-muted">{{ formatDate(app.created_at) }}</small>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block">{{ $t('Phone Number') }}</small>
                                                    <span>{{ app.phone }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block">{{ $t('Pet Experience') }}</small>
                                                    <span>{{ $t(app.experience) }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block">{{ $t('Does family/roommate agree?') }}</small>
                                                    <span
                                                        :class="app.family_agreement ? 'text-success' : 'text-danger'">
                                                        {{ app.family_agreement ? $t('Agreed') : $t('Not Confirmed') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="application__message p-3 rounded-3 mb-3">
                                                <small class="text-muted d-block mb-1">{{ $t('Message to Owner (Briefly)') }}</small>
                                                <p class="mb-0 text-secondary" style="white-space: pre-wrap;">
                                                    {{ app.message }}
                                                </p>
                                            </div>

                                            <!-- Custom Fields Answers -->
                                            <div v-if="app.custom_fields && Object.keys(app.custom_fields).length > 0"
                                                class="applications__custom-fields p-3 rounded-3 mb-3">
                                                <small class="text-primary fw-bold d-block mb-2">
                                                    <i class="bi bi-list-check me-1"></i>{{ $t('Additional Answers') }}
                                                </small>
                                                <div class="row g-2">
                                                    <div v-for="(value, key) in app.custom_fields" :key="key"
                                                        class="col-md-6">
                                                        <small class="text-muted d-block">{{ key }}</small>
                                                        <span v-if="Array.isArray(value)">{{ value.join(', ') }}</span>
                                                        <span v-else>{{ value || '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-column align-items-end gap-2">
                                                <a v-if="app.line_id" :href="`https://line.me/ti/p/~${app.line_id}`"
                                                    target="_blank" class="btn btn-sm btn-success text-white mb-3">
                                                    <i class="bi bi-line me-1"></i>{{ $t('LINE Contact') }}
                                                </a>
                                                <div v-if="app.status === 'pending'" class="d-flex gap-2">
                                                    <button @click="updateStatus(app.id, 'approved')"
                                                        class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                        <i class="bi bi-check-lg me-1"></i>{{ $t('Approve') }}
                                                    </button>
                                                    <button @click="updateStatus(app.id, 'rejected')"
                                                        class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                        <i class="bi bi-x-lg me-1"></i>{{ $t('Reject') }}
                                                    </button>
                                                </div>
                                                <div v-else-if="app.status === 'approved'"
                                                    class="d-flex flex-column align-items-end gap-2">
                                                    <button @click="finalizeAdoption(app, group.pet)"
                                                        class="btn btn-sm btn-primary rounded-pill px-3">
                                                        <i class="bi bi-check-circle me-1"></i>{{ $t('Finalize Adoption') }}
                                                    </button>
                                                    <div v-if="app.owner_message"
                                                        class="application__owner-message p-2 rounded-3 w-100 border-start border-4">
                                                        <small class="text-primary fw-bold d-block mb-1">{{ $t('Your Reply:') }}</small>
                                                        <p class="mb-0 text-secondary small"
                                                            style="white-space: pre-wrap;">
                                                            {{ app.owner_message }}</p>
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $t('Processed at', { date: formatDate(app.updated_at) }) }}
                                                    </div>
                                                </div>
                                                <div v-else class="d-flex flex-column align-items-end gap-1">
                                                    <div v-if="app.owner_message"
                                                        class="application__owner-message p-2 rounded-3 mb-1 w-100 border-start border-4">
                                                        <small class="text-primary fw-bold d-block mb-1">{{ $t('Your Reply:') }}</small>
                                                        <p class="mb-0 text-secondary small"
                                                            style="white-space: pre-wrap;">
                                                            {{ app.owner_message }}</p>
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $t('Processed at', { date: formatDate(app.updated_at) }) }}
                                                    </div>
                                                </div>
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
                            <h4 class="fw-bold text-secondary mb-2">{{ $t('No received applications') }}</h4>
                            <p class="text-muted mb-0">{{ $t('No received applications hint') }}</p>
                        </div>
                    </div>

                    <!-- Sent Applications Tab -->
                    <div v-else>
                        <div v-if="sentApplications.length > 0" class="d-flex flex-column gap-3">
                            <div v-for="app in sentApplications" :key="app.id"
                                class="card border-0 shadow-sm overflow-hidden applications__item"
                                :class="{ 'applications__item--highlight': highlightId === app.id }"
                                :id="`application-${app.id}`">
                                <div class="card-body p-3">
                                    <div class="d-flex gap-3">
                                        <!-- Pet Image -->
                                        <div class="applications__pet-image rounded-3 overflow-hidden flex-shrink-0"
                                            style="width: 80px; height: 80px;">
                                            <img :src="app.pet.image ? `/storage/${app.pet.image}` : '/images/default-pet.png'"
                                                :alt="app.pet.name" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="mb-1 fw-bold text-dark">{{ app.pet.name }}</h5>
                                                    <small class="text-muted">{{ $t('Applied at', { date: formatDate(app.created_at) }) }}</small>
                                                </div>
                                                <span class="badge rounded-pill" :class="getStatusClass(app.status)">
                                                    {{ getStatusText(app.status) }}
                                                </span>
                                            </div>

                                            <!-- Owner Message if processed -->
                                            <div v-if="app.owner_message"
                                                class="application__owner-message p-2 rounded-3 mt-2 border-start border-4">
                                                <small class="text-primary fw-bold d-block mb-1">{{ $t('Owner Reply:') }}</small>
                                                <p class="mb-0 text-secondary small" style="white-space: pre-wrap;">{{ app.owner_message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-end gap-2">
                                        <RouterLink :to="`/adopt/${app.pet.id}`"
                                            class="btn btn-sm btn-outline-secondary rounded-pill">
                                            {{ $t('View Pet Page') }}
                                        </RouterLink>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-5 bg-light rounded-3 border border-dashed">
                            <div class="mb-3">
                                <i class="bi bi-send display-1 text-muted opacity-50"></i>
                            </div>
                            <h4 class="fw-bold text-secondary mb-2">{{ $t('No sent applications') }}</h4>
                            <p class="text-muted mb-0">{{ $t('No sent applications hint') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { trans } from 'laravel-vue-i18n'

const $t = trans

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
    owner_message: string | null
    status: string
    created_at: string
    updated_at: string
    custom_fields?: Record<string, any>
}

interface PetGroup {
    pet: {
        id: number
        name: string
        image: string | null
    }
    applications: Application[]
}

interface SentApplication {
    id: number
    pet: {
        id: number
        name: string
        image: string | null
    }
    status: string
    owner_message: string | null
    created_at: string
}

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const activeTab = ref<'received' | 'sent'>('received')
const groupedApplications = ref<PetGroup[]>([])
const sentApplications = ref<SentApplication[]>([])
const highlightId = ref<number | null>(null)

const totalReceivedApplications = computed(() => {
    return groupedApplications.value.reduce((total, group) => total + group.applications.length, 0)
})

async function fetchApplications() {
    try {
        loading.value = true
        const [receivedRes, sentRes] = await Promise.all([
            axios.get('/api/user/applications'),
            axios.get('/api/user/applications/sent')
        ])
        groupedApplications.value = receivedRes.data.data
        sentApplications.value = sentRes.data.data
    } catch (error) {
        console.error('Failed to fetch applications:', error)
    } finally {
        loading.value = false
    }
}

async function updateStatus(applicationId: number, status: string) {
    const statusText = status === 'approved' ? trans('Approve') : trans('Reject')
    const placeholder = status === 'approved'
        ? trans('Approve placeholder')
        : trans('Reject placeholder')

    const confirmResult = await Swal.fire({
        title: trans('Confirm update status', { status: statusText }),
        html: `
            <div class="text-start mb-3">
                <p class="mb-2">${status === 'approved' ? trans('Approve hint') : trans('Reject hint')}</p>
                <label for="swal-owner-message" class="form-label small text-muted">${trans('Owner message label')}</label>
                <textarea id="swal-owner-message" class="form-control" rows="3" placeholder="${placeholder}"></textarea>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: status === 'approved' ? '#198754' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: trans('Confirm status', { status: statusText }),
        cancelButtonText: trans('Cancel'),
        preConfirm: () => {
            return (document.getElementById('swal-owner-message') as HTMLTextAreaElement).value
        }
    })

    if (confirmResult.isConfirmed) {
        const ownerMessage = confirmResult.value
        try {
            await axios.put(`/api/user/applications/${applicationId}`, {
                status,
                owner_message: ownerMessage
            })
            await Swal.fire({
                icon: 'success',
                title: trans('Update Successful'),
                text: trans('Update successful message', { status: statusText }),
                timer: 1500,
                showConfirmButton: false
            })
            // Refresh data
            await fetchApplications()
        } catch (error) {
            console.error('Failed to update status:', error)
            await Swal.fire({
                icon: 'error',
                title: trans('Update Failed'),
                text: trans('Please try again later'),
                confirmButtonColor: '#2c5282'
            })
        }
    }
}

function getStatusClass(status: string) {
    switch (status) {
        case 'approved': return 'bg-success'
        case 'finalized': return 'bg-success'
        case 'rejected': return 'bg-danger'
        default: return 'bg-warning text-dark'
    }
}

function getStatusText(status: string) {
    switch (status) {
        case 'approved': return trans('Approved')
        case 'finalized': return trans('Adopted')
        case 'rejected': return trans('Rejected')
        default: return trans('Pending')
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

async function handleHighlight() {
    const appId = route.query.highlight
    if (appId) {
        highlightId.value = Number(appId)

        // Determine which tab the highlighted application belongs to
        const isSent = sentApplications.value.some(a => a.id === highlightId.value)
        if (isSent) {
            activeTab.value = 'sent'
        } else {
            activeTab.value = 'received'
        }

        await nextTick()
        // Small delay to ensure tab content is rendered
        setTimeout(() => {
            const element = document.getElementById(`application-${appId}`)
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' })
                // Remove highlight after 2 seconds
                setTimeout(() => {
                    highlightId.value = null
                }, 2000)
            }
        }, 100)
    }
}

async function finalizeAdoption(application: any, pet: any) {
    const result = await Swal.fire({
        title: $t('Finalize Adoption Confirmation', {
            petName: pet.name,
            adopterName: application.applicant_name
        }),
        html: `
            <div class="mb-4 text-start text-secondary small">
                ${$t('Finalize Adoption Hint')}
            </div>
            <div class="form-group text-start pt-3 border-top">
                <label class="form-label fw-bold mb-2">${$t('Tracking Frequency')}</label>
                <select id="tracking-frequency" class="form-select">
                    <option value="">${$t('Please select tracking frequency')}</option>
                    <option value="none">${$t('No Tracking')}（${$t('case.FreqNoneDesc')}）</option>
                    <option value="weekly">${$t('Weekly')}</option>
                    <option value="monthly">${$t('Monthly')}</option>
                    <option value="quarterly">${$t('Quarterly')}</option>
                </select>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2c5282',
        confirmButtonText: $t('Confirm'),
        cancelButtonText: $t('Cancel'),
        preConfirm: () => {
            const select = document.getElementById('tracking-frequency') as HTMLSelectElement
            const frequency = select.value
            if (!frequency) {
                Swal.showValidationMessage($t('Please select tracking frequency'))
                return false
            }
            return frequency
        }
    })

    if (result.isConfirmed && result.value) {
        const frequency = result.value
        const trackingConfig = frequency === 'none' ? null : { frequency }

        // Validate that applicant_user exists
        if (!application.applicant_user) {
            await Swal.fire({
                icon: 'error',
                title: $t('Finalize Failed'),
                text: '申請人資料不完整，無法完成結案',
                confirmButtonColor: '#2c5282'
            })
            return
        }

        try {
            await axios.post('/api/adoption-cases', {
                pet_id: pet.id,
                adopter_id: application.applicant_user.id,
                application_id: application.id,
                tracking_config: trackingConfig
            })

            await Swal.fire({
                icon: 'success',
                title: $t('Finalize Success'),
                text: $t('Finalize Success Message'),
                timer: 2000,
                showConfirmButton: false
            })

            // Redirect to adoptions page with history tab
            router.push({
                path: '/user/adoptions',
                query: { tab: 'history' }
            })
        } catch (error: any) {
            Swal.fire({
                icon: 'error',
                title: $t('Finalize Failed'),
                text: error.response?.data?.message || error.message,
                confirmButtonColor: '#2c5282'
            })
        }
    }
}

onMounted(async () => {
    await fetchApplications()
    handleHighlight()
})

// Watch for highlight changes (e.g. when clicking another notification while on this page)
watch(() => route.query.highlight, () => {
    handleHighlight()
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

.application__owner-message {
    background-color: var(--color-fog-gray);
    border-color: var(--color-denim-blue) !important;
}

.applications__tabs {
    border: 1px solid #e2e8f0;
    background-color: #fff;
}

.applications__tabs .nav-link {
    color: #64748b;
    font-weight: 600;
    transition: all 0.3s ease;
}

.applications__tabs .nav-link.active {
    background-color: var(--color-denim-blue);
    color: #fff;
    box-shadow: 0 4px 6px rgba(44, 82, 130, 0.2);
}
</style>
