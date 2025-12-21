<template>
    <Navbar />
    <Content :title="pet ? pet.title : '載入中...'">
        <template #content>
            <div class="pet-detail__wrapper mx-auto">
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
                    <div class="mt-3">
                        <RouterLink to="/adopt" class="btn btn-primary">返回列表</RouterLink>
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
                                    <i class="bi bi-person-fill me-2"></i>發文者
                                </h3>
                                <div class="d-flex flex-column gap-2">
                                    <RouterLink :to="`/user/profile/${pet.user.id}`"
                                        class="pet-detail__owner-link text-decoration-none fw-semibold fs-6 d-inline-flex align-items-center">
                                        <i class="bi bi-person-circle me-2"></i>{{ pet.user.name }}
                                    </RouterLink>
                                    <span class="text-muted small d-flex align-items-center">
                                        <i class="bi bi-calendar me-1"></i>發布於 {{ formatDate(pet.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <RouterLink to="/adopt" class="btn btn-outline-secondary flex-fill py-2 fw-semibold">
                                <i class="bi bi-arrow-left me-2"></i>返回列表
                            </RouterLink>

                            <!-- Owner Actions -->
                            <template v-if="isOwner">
                                <button @click="handleDelete" class="btn btn-danger flex-fill py-2 fw-semibold">
                                    <i class="bi bi-trash me-2"></i>刪除
                                </button>
                            </template>

                            <!-- Visitor Actions -->
                            <template v-else>
                                <button class="btn btn-primary flex-fill py-2 fw-semibold">
                                    <i class="bi bi-envelope-heart me-2"></i>申請領養
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
                                    <i class="bi bi-info-circle-fill me-2"></i>基本資訊
                                </h3>
                                <div class="row row-cols-2 g-3">
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">動物名稱</span>
                                            <span class="fw-semibold text-dark">{{ pet.name }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">動物類別</span>
                                            <span class="fw-semibold text-dark">{{ pet.type }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">性別</span>
                                            <span class="fw-semibold text-dark">
                                                <i
                                                    :class="['bi', pet.gender === 'male' ? 'bi-gender-male text-primary' : 'bi-gender-female text-danger']"></i>
                                                {{ pet.gender === 'male' ? '男生' : '女生' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">年齡</span>
                                            <span class="fw-semibold text-dark">{{ pet.age }} 歲</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">毛色</span>
                                            <span class="fw-semibold text-dark">{{ pet.color }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">毛型</span>
                                            <span class="fw-semibold text-dark">{{ pet.fur_type }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">結紮狀態</span>
                                            <span class="fw-semibold"
                                                :class="pet.is_neuter ? 'text-success' : 'text-warning'">
                                                {{ pet.is_neuter ? '已結紮' : '未結紮' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">是否為流浪動物</span>
                                            <span class="fw-semibold text-dark">{{ pet.is_stray ? '是' : '否' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Info Card -->
                        <div class="card shadow-sm border-0 pet-detail__card-hover">
                            <div class="card-body p-4">
                                <h3 class="fs-5 fw-bold text-primary mb-3 d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill me-2"></i>地點資訊
                                </h3>
                                <div class="row row-cols-2 g-3">
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">所在縣市</span>
                                            <span class="fw-semibold text-dark">{{ pet.city }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-secondary fw-medium">所在鄉鎮</span>
                                            <span class="fw-semibold text-dark">{{ pet.town }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="pet.sendable_cities && pet.sendable_cities.length > 0" class="mt-3">
                                    <span class="small text-secondary fw-medium d-block mb-2">可送養縣市範圍</span>
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
                                                <i class="bi bi-card-text me-2"></i>送養說明
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                            aria-labelledby="headingOne" data-bs-parent="#petDetailAccordion">
                                            <div class="accordion-body px-0">
                                                <p class="text-secondary lh-lg mb-0" style="white-space: pre-wrap;">
                                                    {{ pet.detail?.adoption_description || '無' }}
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
                                                <i class="bi bi-heart-pulse me-2"></i>健康狀態說明
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            aria-labelledby="headingTwo" data-bs-parent="#petDetailAccordion">
                                            <div class="accordion-body px-0">
                                                <p class="text-secondary lh-lg mb-0" style="white-space: pre-wrap;">
                                                    {{ pet.detail?.health_description || '無' }}
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
                                                <i class="bi bi-clipboard-check me-2"></i>領養條件
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse"
                                            aria-labelledby="headingThree" data-bs-parent="#petDetailAccordion">
                                            <div class="accordion-body px-0">
                                                <p class="text-secondary lh-lg mb-0" style="white-space: pre-wrap;">
                                                    {{ pet.detail?.adoption_condition || '無' }}
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
                <div v-if="pet" class="mt-5 pet-detail__comment-section">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="fs-5 fw-bold text-primary mb-4 d-flex align-items-center">
                                <i class="bi bi-chat-dots-fill me-2"></i>問與答 ({{ comments.length }})
                            </h3>

                            <!-- Post Comment Form -->
                            <div v-if="currentUser" class="mb-5">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle pet-detail__comment-avatar d-flex align-items-center justify-content-center"
                                            style="width: 48px; height: 48px;">
                                            <i class="bi bi-person-fill text-secondary fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <textarea v-model="newComment"
                                            class="form-control border-0 pet-detail__comment-input p-3" rows="3"
                                            placeholder="對這隻動物感興趣嗎？留言詢問送養者吧..." style="resize: none;"></textarea>
                                        <div class="d-flex justify-content-end mt-2">
                                            <button @click="handlePostComment"
                                                :disabled="!newComment.trim() || postingComment"
                                                class="btn btn-primary px-4 py-2 fw-semibold rounded-pill">
                                                <span v-if="postingComment"
                                                    class="spinner-border spinner-border-sm me-2"></span>
                                                發布留言
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="pet-detail__comment-login-prompt text-center py-4 mb-5">
                                <i class="bi bi-lock-fill text-primary fs-2 mb-3 d-block opacity-50"></i>
                                <p class="text-secondary mb-3 fw-medium">登入後即可留言詢問送養者</p>
                                <RouterLink :to="`/auth/login?redirect=${route.fullPath}`"
                                    class="btn btn-primary px-4 rounded-pill fw-semibold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>立即登入
                                </RouterLink>
                            </div>

                            <!-- Comment List -->
                            <div class="d-flex flex-column gap-4">
                                <div v-for="comment in comments" :key="comment.id" class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle pet-detail__comment-avatar d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-secondary fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-dark">{{ comment.user.name }}</span>
                                            <span v-if="comment.user_id === pet.user.id"
                                                class="badge pet-detail__owner-badge text-primary border border-primary-subtle rounded-pill"
                                                style="font-size: 0.7rem;">送養者</span>
                                            <span class="text-muted small ms-auto">{{ formatDate(comment.created_at)
                                                }}</span>
                                        </div>
                                        <div class="pet-detail__comment-bubble p-3 rounded-3 text-secondary"
                                            style="white-space: pre-wrap;">{{ comment.content }}</div>

                                        <!-- Reply Button (Owner Only) -->
                                        <div v-if="isOwner && comment.user_id !== currentUser?.id" class="mt-2">
                                            <button @click="handleReplyClick(comment.id)"
                                                class="btn btn-sm btn-link pet-detail__comment-reply-btn text-decoration-none p-0">
                                                <i class="bi bi-reply me-1"></i>回覆
                                            </button>
                                        </div>

                                        <!-- Reply Form -->
                                        <div v-if="replyingTo === comment.id" class="mt-3">
                                            <div class="d-flex gap-2">
                                                <div class="flex-shrink-0">
                                                    <div class="rounded-circle pet-detail__comment-avatar d-flex align-items-center justify-content-center"
                                                        style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person-fill text-secondary fs-6"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <textarea v-model="replyContent"
                                                        class="form-control border-0 pet-detail__comment-input p-2"
                                                        rows="2" :placeholder="`回覆 @${comment.user.name}...`"
                                                        style="resize: none; font-size: 0.9rem;"></textarea>
                                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                                        <button @click="handleCancelReply"
                                                            class="btn btn-sm btn-outline-secondary px-3 rounded-pill">取消</button>
                                                        <button @click="handlePostReply(comment.id)"
                                                            :disabled="!replyContent.trim() || postingReply"
                                                            class="btn btn-sm btn-primary px-3 rounded-pill">
                                                            <span v-if="postingReply"
                                                                class="spinner-border spinner-border-sm me-1"></span>
                                                            發布回覆
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Nested Replies -->
                                        <div v-if="comment.replies && comment.replies.length > 0" class="mt-3">
                                            <div v-for="reply in comment.replies" :key="reply.id"
                                                class="pet-detail__comment-reply d-flex gap-2">
                                                <div class="flex-shrink-0">
                                                    <div class="rounded-circle pet-detail__comment-avatar d-flex align-items-center justify-content-center"
                                                        style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person text-secondary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{
                                                            reply.user.name
                                                        }}</span>
                                                        <span v-if="reply.user_id === pet.user.id"
                                                            class="badge pet-detail__owner-badge text-primary border border-primary-subtle rounded-pill"
                                                            style="font-size: 0.65rem;">送養者</span>
                                                        <span class="text-muted small ms-auto">{{
                                                            formatDate(reply.created_at) }}</span>
                                                    </div>
                                                    <div class="pet-detail__comment-bubble p-2 rounded-3 text-secondary"
                                                        style="white-space: pre-wrap; font-size: 0.9rem;">{{
                                                            reply.content }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="comments.length === 0" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-left-text d-block fs-1 mb-3 opacity-25"></i>
                                    目前還沒有留言，快來成為第一個詢問的人吧！
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAdoptStore } from '@/stores/adopt'
import { PetInter } from '@/types/pet'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()
const adoptStore = useAdoptStore()

const pet = ref<PetInter | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const currentImageIndex = ref(0)
const currentUser = ref<{ id: number, name: string } | null>(null)
const comments = ref<any[]>([])
const newComment = ref('')
const postingComment = ref(false)
const replyingTo = ref<number | null>(null)
const replyContent = ref('')
const postingReply = ref(false)

// Check if current user is the owner
const isOwner = computed(() => {
    if (!pet.value || !currentUser.value) return false
    return pet.value.user.id === currentUser.value.id
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
        error.value = err.response?.data?.message || '無法載入寵物詳細資料'
        console.error('Failed to fetch pet details:', err)
    } finally {
        loading.value = false
    }
}

async function fetchCurrentUser() {
    try {
        const response = await axios.get('/api/user')
        currentUser.value = response.data
    } catch (error) {
        currentUser.value = null
    }
}

async function fetchComments() {
    try {
        const response = await axios.get(`/api/adopt/${petId.value}/comments`)
        comments.value = response.data
    } catch (error) {
        console.error('Failed to fetch comments:', error)
    }
}

async function handlePostComment() {
    if (!newComment.value.trim() || postingComment.value) return

    postingComment.value = true
    try {
        const response = await axios.post(`/api/adopt/${petId.value}/comments`, {
            content: newComment.value
        })
        comments.value.unshift(response.data)
        newComment.value = ''
        Swal.fire({
            icon: 'success',
            title: '留言成功',
            timer: 1500,
            showConfirmButton: false
        })
    } catch (error: any) {
        console.error('Failed to post comment:', error)
        Swal.fire({
            icon: 'error',
            title: '留言失敗',
            text: error.response?.data?.message || '請稍後再試'
        })
    } finally {
        postingComment.value = false
    }
}

function handleReplyClick(commentId: number) {
    replyingTo.value = commentId
    replyContent.value = ''
}

function handleCancelReply() {
    replyingTo.value = null
    replyContent.value = ''
}

async function handlePostReply(parentId: number) {
    if (!replyContent.value.trim() || postingReply.value) return

    postingReply.value = true
    try {
        const response = await axios.post(`/api/adopt/${petId.value}/comments`, {
            content: replyContent.value,
            parent_id: parentId
        })

        // Find the parent comment and add the reply to its replies array
        const parentComment = comments.value.find(c => c.id === parentId)
        if (parentComment) {
            if (!parentComment.replies) {
                parentComment.replies = []
            }
            parentComment.replies.push(response.data)
        }

        replyContent.value = ''
        replyingTo.value = null

        Swal.fire({
            icon: 'success',
            title: '回覆成功',
            timer: 1500,
            showConfirmButton: false
        })
    } catch (error: any) {
        console.error('Failed to post reply:', error)
        Swal.fire({
            icon: 'error',
            title: '回覆失敗',
            text: error.response?.data?.message || '請稍後再試'
        })
    } finally {
        postingReply.value = false
    }
}

async function handleDelete() {
    if (!pet.value) return

    const result = await Swal.fire({
        title: '確定要刪除嗎？',
        text: "刪除後將無法復原！",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '是的，刪除它！',
        cancelButtonText: '取消'
    })

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/adopt/${pet.value.id}`)

            await Swal.fire({
                icon: 'success',
                title: '刪除成功',
                text: '寵物資料已刪除',
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
                title: '刪除失敗',
                text: '請稍後再試',
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

// Watch for route param changes
watch(() => route.params.id, (newId, oldId) => {
    if (newId && newId !== oldId) {
        fetchPetDetail()
    }
})

onMounted(() => {
    fetchCurrentUser()
    fetchPetDetail()
    fetchComments()
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

/* Comment Section */
.pet-detail__comment-section {
    max-width: 960px;
    margin-left: auto;
    margin-right: auto;
}

.pet-detail__comment-input {
    background-color: var(--color-fog-gray);
    border-radius: 12px;
}

.pet-detail__comment-input:focus {
    background-color: var(--color-fog-gray);
    outline: none;
    box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.15);
}

.pet-detail__comment-bubble {
    background-color: var(--color-fog-gray);
}

.pet-detail__comment-avatar {
    background-color: var(--color-fog-gray);
}

.pet-detail__comment-login-prompt {
    background-color: var(--color-fog-gray);
    border-radius: 12px;
}

.pet-detail__comment-reply-btn {
    color: var(--color-denim-blue);
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.pet-detail__comment-reply-btn:hover {
    color: var(--color-denim-blue-dark);
    transform: translateX(2px);
}

.pet-detail__comment-reply {
    border-left: 2px solid var(--color-fog-gray);
    padding-left: 1rem;
}

.pet-detail__owner-badge {
    background-color: #ebf8ff;
}

/* Responsive */
@media (max-width: 768px) {
    .pet-detail {
        grid-template-columns: 1fr;
    }
}
</style>
