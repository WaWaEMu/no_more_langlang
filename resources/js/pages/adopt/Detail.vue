<template>
    <Navbar />
    <Content :title="pet ? pet.title : $t('Loading...')">
        <template #content>
            <div class="pet-detail__wrapper mx-auto">
                <!-- Loading State -->
                <div v-if="loading" class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ $t('Loading...') }}</span>
                    </div>
                    <p class="mt-3 text-secondary">{{ $t('Loading...') }}</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="alert alert-danger mx-auto" style="max-width: 600px;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ error }}
                    <div class="mt-3">
                        <RouterLink to="/adopt" class="btn btn-primary">{{ $t('Back to List') }}</RouterLink>
                    </div>
                </div>

                <!-- Pet Detail Content -->
                <div v-else-if="pet" class="pet-detail">
                    <!-- Left Column: Gallery + Owner Info -->
                    <div class="d-flex flex-column gap-3">
                        <!-- Image Gallery Section -->
                        <div class="d-flex flex-column gap-2">
                            <div v-if="pet.images && pet.images.length > 0" class="pet-detail__main-image">
                                <img :src="`/storage/${pet.images[currentImageIndex].path}`" :alt="pet.name"
                                    class="w-100 h-100 object-fit-cover" />
                            </div>
                            <!-- Thumbnail navigation if multiple images -->
                            <div v-if="pet.images && pet.images.length > 1" class="d-flex gap-2 overflow-x-auto p-1">
                                <button v-for="(image, index) in pet.images" :key="index"
                                    @click="currentImageIndex = index" class="pet-detail__thumbnail"
                                    :class="{ 'active': currentImageIndex === index }">
                                    <img :src="`/storage/${image.path}`" :alt="`${pet.name} ${index + 1}`"
                                        class="w-100 h-100 object-fit-cover" />
                                </button>
                            </div>
                        </div>

                        <!-- Owner Info Card -->
                        <div class="card shadow-sm border-0 pet-detail__card-hover">
                            <div class="card-body p-4">
                                <h3 class="fs-5 fw-bold text-primary mb-3 d-flex align-items-center">
                                    <i class="bi bi-person-fill me-2"></i>{{ $t('Posted by') }}
                                </h3>
                                <div class="d-flex flex-column gap-2">
                                    <RouterLink :to="`/user/profile/${pet.user.id}`"
                                        class="pet-detail__owner-link text-decoration-none fw-semibold fs-6 d-inline-flex align-items-center">
                                        <i class="bi bi-person-circle me-2"></i>{{ pet.user.name }}
                                    </RouterLink>
                                    <span class="text-muted small d-flex align-items-center">
                                        <i class="bi bi-calendar me-1"></i>{{ $t('Published at') }} {{
                                            formatDate(pet.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <RouterLink to="/adopt" class="btn btn-outline-secondary flex-fill py-2 fw-semibold">
                                <i class="bi bi-arrow-left me-2"></i>{{ $t('Back to List') }}
                            </RouterLink>

                            <!-- Owner Actions -->
                            <template v-if="isOwner">
                                <button @click="handleDelete" class="btn btn-danger flex-fill py-2 fw-semibold">
                                    <i class="bi bi-trash me-2"></i>{{ $t('Delete') }}
                                </button>
                            </template>

                            <!-- Visitor Actions -->
                            <template v-else>
                                <!-- Paused: disabled with message -->
                                <button v-if="pet.status === 'paused'"
                                    class="btn btn-secondary flex-fill py-2 fw-semibold" disabled>
                                    <i class="bi bi-pause-circle me-2"></i>{{ $t('Adoption Paused') }}
                                </button>
                                <!-- Adopted: disabled with message -->
                                <button v-else-if="pet.status === 'adopted'"
                                    class="btn btn-secondary flex-fill py-2 fw-semibold" disabled>
                                    <i class="bi bi-house-heart me-2"></i>{{ $t('Already Adopted') }}
                                </button>
                                <!-- Normal apply button -->
                                <button v-else @click="handleApply" class="btn btn-primary flex-fill py-2 fw-semibold">
                                    <i class="bi bi-envelope-heart me-2"></i>{{ $t('Apply Adoption') }}
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Right Column: Main Info -->
                    <div class="d-flex flex-column gap-3">
                        <!-- Basic Info Card -->
                        <div class="card shadow-sm border-0 pet-detail__card-hover">
                            <div class="card-body p-4">
                                <h3 class="fs-5 fw-bold text-primary mb-3 d-flex align-items-center">
                                    <i class="bi bi-info-circle-fill me-2"></i>{{ $t('Basic Info') }}
                                </h3>
                                <div class="row row-cols-2 g-3">
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Pet Name') }}</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark fs-5">{{ pet.name }}</span>
                                                <span
                                                    :class="['pet-detail__status-badge', `pet-detail__status-badge--${pet.status}`]">
                                                    {{ getStatusLabel(pet.status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Pet Type') }}</span>
                                            <span class="fw-semibold text-dark">{{ pet.type }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Gender') }}</span>
                                            <span class="fw-semibold text-dark">
                                                <i
                                                    :class="['bi', pet.gender === 'male' ? 'bi-gender-male text-primary' : 'bi-gender-female text-danger']"></i>
                                                {{ pet.gender === 'male' ? $t('Male') : $t('Female') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Age') }}</span>
                                            <span class="fw-semibold text-dark">{{ pet.age }} {{ $t('years old')
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Color') }}</span>
                                            <span class="fw-semibold text-dark">{{ pet.color }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Fur Type') }}</span>
                                            <span class="fw-semibold text-dark">{{ pet.fur_type }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Neuter Status')
                                                }}</span>
                                            <span class="fw-semibold"
                                                :class="pet.is_neuter ? 'text-success' : 'text-warning'">
                                                {{ pet.is_neuter ? $t('Neutered') : $t('Not Neutered') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Is Stray Animal')
                                                }}</span>
                                            <span class="fw-semibold text-dark">{{ pet.is_stray ? $t('Yes') : $t('No')
                                                }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Info Card -->
                        <div class="card shadow-sm border-0 pet-detail__card-hover">
                            <div class="card-body p-4">
                                <h3 class="fs-5 fw-bold text-primary mb-3 d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill me-2"></i>{{ $t('Location Info') }}
                                </h3>
                                <div class="row row-cols-2 g-3">
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('City') }}</span>
                                            <span class="fw-semibold text-dark">{{ pet.city }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">{{ $t('Town') }}</span>
                                            <span class="fw-semibold text-dark">{{ pet.town }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="pet.sendable_cities && pet.sendable_cities.length > 0" class="mt-3">
                                    <span class="small text-secondary fw-medium d-block mb-2">{{ $t('Sendable Cities')
                                        }}</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span v-for="city in pet.sendable_cities" :key="city" class="pet-detail__tag">
                                            {{ city }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Collapsible Sections -->
                        <div class="card shadow-sm border-0 pet-detail__card-hover">
                            <div class="card-body p-4">
                                <div class="accordion accordion-flush" id="petDetailAccordion">
                                    <!-- Adoption Description -->
                                    <div class="accordion-item border-0 mb-2">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button
                                                class="accordion-button pet-detail__accordion-button rounded-2 fw-semibold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                aria-expanded="true" aria-controls="collapseOne">
                                                <i class="bi bi-card-text me-2"></i>{{ $t('Adoption Description') }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                            aria-labelledby="headingOne" data-bs-parent="#petDetailAccordion">
                                            <div class="accordion-body px-0">
                                                <p class="text-secondary lh-lg mb-0" style="white-space: pre-wrap;">
                                                    {{ pet.detail?.adoption_description || $t('None') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Health Description -->
                                    <div class="accordion-item border-0 mb-2">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button
                                                class="accordion-button pet-detail__accordion-button collapsed rounded-2 fw-semibold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                                <i class="bi bi-heart-pulse me-2"></i>{{ $t('Health Description') }}
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            aria-labelledby="headingTwo" data-bs-parent="#petDetailAccordion">
                                            <div class="accordion-body px-0">
                                                <p class="text-secondary lh-lg mb-0" style="white-space: pre-wrap;">
                                                    {{ pet.detail?.health_description || $t('None') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Adoption Condition -->
                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button
                                                class="accordion-button pet-detail__accordion-button collapsed rounded-2 fw-semibold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                aria-expanded="false" aria-controls="collapseThree">
                                                <i class="bi bi-clipboard-check me-2"></i>{{ $t('Adoption Conditions')
                                                }}
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse"
                                            aria-labelledby="headingThree" data-bs-parent="#petDetailAccordion">
                                            <div class="accordion-body px-0">
                                                <p class="text-secondary lh-lg mb-0" style="white-space: pre-wrap;">
                                                    {{ pet.detail?.adoption_condition || $t('None') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comment Section -->
                <PetComment v-if="pet" :pet-id="pet.id" :pet-owner-id="pet.user.id" />
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAdoptStore } from '@/stores/adopt'
import { useAuthStore } from '@/stores/auth'
import { PetInter } from '@/types/pet'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import PetComment from '@/components/adopt/PetComment.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { trans } from 'laravel-vue-i18n'

const $t = trans

const route = useRoute()
const router = useRouter()
const adoptStore = useAdoptStore()
const authStore = useAuthStore()

const pet = ref<PetInter | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const currentImageIndex = ref(0)

// Check if current user is the owner
const isOwner = computed(() => {
    if (!pet.value) return false
    return authStore.isOwner(pet.value.user.id)
})

// Use computed to make petId reactive to route changes
const petId = computed(() => route.params.id as string)

async function fetchPetDetail() {
    loading.value = true
    error.value = null
    pet.value = null
    currentImageIndex.value = 0

    try {
        const data = await adoptStore.fetchPetById(petId.value)
        pet.value = data
    } catch (err: any) {
        error.value = err.response?.data?.message || $t('Unable to load pet details')
        console.error('Failed to fetch pet details:', err)
    } finally {
        loading.value = false
    }
}

async function handleApply() {
    if (!pet.value) return
    if (await authStore.checkAuth($t('Login to submit an adoption application!'))) {
        router.push(`/adopt/${pet.value.id}/apply`)
    }
}

async function handleDelete() {
    if (!pet.value) return

    const result = await Swal.fire({
        title: $t('Are you sure you want to delete?'),
        text: $t('This action cannot be undone!'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: $t('Yes, delete it!'),
        cancelButtonText: $t('Cancel')
    })

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/adopt/${pet.value.id}`)

            await Swal.fire({
                icon: 'success',
                title: $t('Deleted successfully'),
                text: $t('Pet data has been deleted'),
                timer: 1500,
                showConfirmButton: false
            })

            if (window.history.state?.back) {
                router.back()
            } else {
                router.push('/adopt')
            }
        } catch (error) {
            console.error('Delete failed:', error)
            Swal.fire({
                icon: 'error',
                title: $t('Delete failed'),
                text: $t('Please try again later'),
            })
        }
    }
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

function getStatusLabel(status: string) {
    const labels: Record<string, string> = {
        'available': 'Status.Available',
        'paused': 'Status.Paused',
        'pending': 'Status.Pending',
        'adopted': 'Status.Adopted'
    }
    return labels[status] ? $t(labels[status]) : status
}

// Watch for route param changes
watch(() => route.params.id, (newId, oldId) => {
    if (newId && newId !== oldId) {
        fetchPetDetail()
    }
})

onMounted(() => {
    authStore.fetchUser()
    fetchPetDetail()
})
</script>

<style scoped>
/* Layout */
.pet-detail__wrapper {
    max-width: 1200px;
}

.pet-detail {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 3rem;
    align-items: start;
}

/* Image Gallery - Custom aspect ratio and styling */
.pet-detail__main-image {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

/* Thumbnail buttons */
.pet-detail__thumbnail {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    border: 3px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
    background: none;
}

.pet-detail__thumbnail:hover {
    border-color: var(--color-denim-blue);
    transform: translateY(-2px);
}

.pet-detail__thumbnail.active {
    border-color: var(--color-denim-blue);
    box-shadow: 0 2px 8px rgba(66, 91, 118, 0.3);
}

/* Card hover effect */
.pet-detail__card-hover {
    transition: all 0.3s ease;
}

.pet-detail__card-hover:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-2px);
}

/* Tag styling (gradient background) */
.pet-detail__tag {
    background: linear-gradient(135deg, #b8c5d6 0%, #d1d9e6 100%);
    color: var(--color-denim-blue);
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

/* Status Badge Colors */
.pet-detail__status-badge--available {
    background-color: #48bb78;
}

.pet-detail__status-badge--paused {
    background-color: #ed8936;
}

.pet-detail__status-badge--pending {
    background-color: #4299e1;
}

.pet-detail__status-badge--adopted {
    background-color: #805ad5;
}

.pet-detail__status-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 550;
    color: #fff;
}

/* Accordion custom button styling */
.pet-detail__accordion-button {
    background: var(--color-fog-gray);
    color: var(--color-denim-blue);
    box-shadow: none;
}

.pet-detail__accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #b8c5d6 0%, #d1d9e6 100%);
    color: var(--color-denim-blue);
}

.pet-detail__accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.25);
}

/* Owner link hover effect */
.pet-detail__owner-link {
    color: var(--color-denim-blue);
    transition: all 0.2s ease;
}

.pet-detail__owner-link:hover {
    color: var(--color-denim-blue-dark);
    transform: translateX(4px);
}

/* Responsive */
@media (max-width: 768px) {
    .pet-detail {
        grid-template-columns: 1fr;
    }
}
</style>
