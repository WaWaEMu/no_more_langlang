<template>
    <div class="mt-5 pet-comment-section">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="fs-5 fw-bold text-primary mb-4 d-flex align-items-center">
                    <i class="bi bi-chat-dots-fill me-2"></i>問與答 ({{ comments.length }})
                </h3>

                <!-- Post Comment Form -->
                <div v-if="authStore.user" class="mb-5">
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle pet-comment__avatar d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-person-fill text-secondary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <textarea v-model="newComment" class="form-control border-0 pet-comment__input p-3" rows="3"
                                placeholder="對這隻動物感興趣嗎？留言詢問送養者吧..." style="resize: none;"></textarea>
                            <div class="d-flex justify-content-end mt-2">
                                <button @click="handlePostComment" :disabled="!newComment.trim() || postingComment"
                                    class="btn btn-primary px-4 py-2 fw-semibold rounded-pill">
                                    <span v-if="postingComment" class="spinner-border spinner-border-sm me-2"></span>
                                    發布留言
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="pet-comment__login-prompt text-center py-4 mb-5">
                    <i class="bi bi-lock-fill text-primary fs-2 mb-3 d-block opacity-50"></i>
                    <p class="text-secondary mb-3 fw-medium">登入後即可留言詢問送養者</p>
                    <RouterLink :to="`/auth/login?redirect=${$route.fullPath}`"
                        class="btn btn-primary px-4 rounded-pill fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>立即登入
                    </RouterLink>
                </div>

                <!-- Comment List -->
                <div class="d-flex flex-column gap-4">
                    <div v-for="comment in comments" :key="comment.id" :id="`comment-${comment.id}`"
                        class="d-flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle pet-comment__avatar d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-person text-secondary fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark">{{ comment.user.name }}</span>
                                <span v-if="comment.user_id === petOwnerId"
                                    class="badge pet-comment__owner-badge text-primary border border-primary-subtle rounded-pill"
                                    style="font-size: 0.7rem;">送養者</span>
                                <span class="text-muted small ms-auto">{{ formatDate(comment.created_at) }}</span>
                            </div>
                            <div class="pet-comment__bubble p-3 rounded-3 text-secondary"
                                style="white-space: pre-wrap;">{{ comment.content }}</div>

                            <!-- Reply Button (Owner Only) -->
                            <div v-if="isOwner && comment.user_id !== authStore.user?.id" class="mt-2">
                                <button @click="handleReplyClick(comment.id)"
                                    class="btn btn-sm btn-link pet-comment__reply-btn text-decoration-none p-0">
                                    <i class="bi bi-reply me-1"></i>回覆
                                </button>
                            </div>

                            <!-- Reply Form -->
                            <div v-if="replyingTo === comment.id" class="mt-3">
                                <div class="d-flex gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle pet-comment__avatar d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill text-secondary fs-6"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <textarea v-model="replyContent"
                                            class="form-control border-0 pet-comment__input p-2" rows="2"
                                            :placeholder="`回覆 @${comment.user.name}...`"
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
                                <div v-for="reply in comment.replies" :key="reply.id" :id="`comment-${reply.id}`"
                                    class="pet-comment__reply d-flex gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle pet-comment__avatar d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ reply.user.name }}</span>
                                            <span v-if="reply.user_id === petOwnerId"
                                                class="badge pet-comment__owner-badge text-primary border border-primary-subtle rounded-pill"
                                                style="font-size: 0.65rem;">送養者</span>
                                            <span class="text-muted small ms-auto">{{ formatDate(reply.created_at) }}</span>
                                        </div>
                                        <div class="pet-comment__bubble p-2 rounded-3 text-secondary"
                                            style="white-space: pre-wrap; font-size: 0.9rem;">{{ reply.content }}</div>
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
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useAuthStore } from '@/stores/auth'

interface Props {
    petId: string | number
    petOwnerId: number
}

const props = defineProps<Props>()
const authStore = useAuthStore()

const comments = ref<any[]>([])
const newComment = ref('')
const postingComment = ref(false)
const replyingTo = ref<number | null>(null)
const replyContent = ref('')
const postingReply = ref(false)

// Check if current user is the owner
const isOwner = computed(() => {
    return authStore.isOwner(props.petOwnerId)
})

async function fetchComments() {
    try {
        const response = await axios.get(`/api/adopt/${props.petId}/comments`)
        comments.value = response.data
    } catch (error) {
        console.error('Failed to fetch comments:', error)
    }
}

async function handlePostComment() {
    if (!newComment.value.trim() || postingComment.value) return

    postingComment.value = true
    try {
        const response = await axios.post(`/api/adopt/${props.petId}/comments`, {
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
        const response = await axios.post(`/api/adopt/${props.petId}/comments`, {
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

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

onMounted(() => {
    fetchComments()
})
</script>

<style scoped>
/* Comment Section */
.pet-comment-section {
    max-width: 960px;
    margin-left: auto;
    margin-right: auto;
}

.pet-comment__input {
    background-color: var(--color-fog-gray);
    border-radius: 12px;
}

.pet-comment__input:focus {
    background-color: var(--color-fog-gray);
    outline: none;
    box-shadow: 0 0 0 0.25rem rgba(66, 91, 118, 0.15);
}

.pet-comment__bubble {
    background-color: var(--color-fog-gray);
}

.pet-comment__avatar {
    background-color: var(--color-fog-gray);
}

.pet-comment__login-prompt {
    background-color: var(--color-fog-gray);
    border-radius: 12px;
}

.pet-comment__reply-btn {
    color: var(--color-denim-blue);
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.pet-comment__reply-btn:hover {
    color: var(--color-denim-blue-dark);
    transform: translateX(2px);
}

.pet-comment__reply {
    border-left: 2px solid var(--color-fog-gray);
    padding-left: 1rem;
}

.pet-comment__owner-badge {
    background-color: #ebf8ff;
}
</style>
