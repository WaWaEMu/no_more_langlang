<template>
    <div class="diary-timeline">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <small class="text-muted ms-2">{{ $t('Loading...') }}</small>
        </div>

        <!-- Empty State -->
        <div v-else-if="entries.length === 0" class="diary-timeline__empty text-center py-5">
            <div class="diary-timeline__empty-icon mb-3">📸</div>
            <h6 class="text-muted fw-bold mb-1">{{ $t('diary.EmptyTitle') }}</h6>
            <p class="text-muted small mb-0">{{ $t('diary.EmptyHint') }}</p>
        </div>

        <!-- Diary Cards -->
        <div v-else class="d-flex flex-column gap-4">
            <div v-for="entry in entries" :key="entry.id"
                class="diary-timeline__card rounded-4 overflow-hidden shadow-sm">

                <!-- Photo Carousel -->
                <div v-if="entry.photos.length === 1" class="diary-timeline__photo-single">
                    <img :src="`/storage/${entry.photos[0]}`" :alt="entry.content || 'diary'"
                        class="diary-timeline__photo-img w-100" @click="openImage(`/storage/${entry.photos[0]}`)">
                </div>
                <div v-else :id="`carousel-${entry.id}`" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-indicators">
                        <button v-for="(_, idx) in entry.photos" :key="idx" type="button"
                            :data-bs-target="`#carousel-${entry.id}`" :data-bs-slide-to="idx"
                            :class="{ active: idx === 0 }" :aria-current="idx === 0 ? 'true' : undefined"></button>
                    </div>
                    <div class="carousel-inner">
                        <div v-for="(photo, idx) in entry.photos" :key="idx" class="carousel-item"
                            :class="{ active: idx === 0 }">
                            <img :src="`/storage/${photo}`" :alt="`photo-${idx}`"
                                class="diary-timeline__photo-img w-100" @click="openImage(`/storage/${photo}`)">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" :data-bs-target="`#carousel-${entry.id}`"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" :data-bs-target="`#carousel-${entry.id}`"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>

                <!-- Entry Info -->
                <div class="p-3">
                    <!-- Location Badge -->
                    <div v-if="entry.location" class="mb-2">
                        <span class="badge rounded-pill diary-timeline__location-badge">
                            <i class="bi bi-geo-alt-fill me-1"></i>{{ entry.location }}
                        </span>
                    </div>

                    <!-- Content -->
                    <p v-if="entry.content" class="diary-timeline__content mb-2 text-dark">
                        {{ entry.content }}
                    </p>

                    <!-- Meta -->
                    <div class="diary-timeline__meta d-flex justify-content-between align-items-center mb-2">
                        <small class="diary-timeline__author text-muted fw-semibold">
                            <i class="bi bi-person me-1"></i>{{ entry.author?.name }}
                        </small>
                        <small class="diary-timeline__date text-muted">
                            {{ formatDate(entry.created_at) }}
                        </small>
                    </div>

                    <!-- Comments Section -->
                    <div class="diary-timeline__comments border-top pt-2 mt-2">
                        <!-- Existing Comments -->
                        <div v-for="comment in entry.comments" :key="comment.id"
                            class="diary-timeline__comment d-flex gap-2 mb-2">
                            <div class="diary-timeline__comment-avatar">
                                <i class="bi bi-person-circle text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold small">{{ comment.author?.name }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">
                                        {{ formatDate(comment.created_at) }}
                                    </span>
                                </div>
                                <p class="mb-0 small text-dark">{{ comment.content }}</p>
                            </div>
                        </div>

                        <!-- Reply Input -->
                        <div class="diary-timeline__reply-group d-flex gap-2 align-items-center mt-2">
                            <input v-model="replyTexts[entry.id]" type="text"
                                class="form-control form-control-sm rounded-pill diary-timeline__reply-input"
                                :placeholder="$t('diary.ReplyPlaceholder')" maxlength="500"
                                @keyup.enter="submitComment(entry.id)">
                            <button @click="submitComment(entry.id)"
                                class="btn btn-sm btn-primary rounded-pill px-3 diary-timeline__reply-btn"
                                :disabled="!replyTexts[entry.id]?.trim() || submittingComment === entry.id">
                                <span v-if="submittingComment === entry.id"
                                    class="spinner-border spinner-border-sm"></span>
                                <i v-else class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import axios from 'axios'
import { trans } from 'laravel-vue-i18n'

const $t = trans

interface DiaryComment {
    id: number
    content: string
    author: { id: number; name: string } | null
    created_at: string
}

interface DiaryEntry {
    id: number
    photos: string[]
    content: string | null
    location: string | null
    author: { id: number; name: string } | null
    comments: DiaryComment[]
    created_at: string
}

interface Props {
    caseId: number
    refreshKey?: number
}

const props = defineProps<Props>()

const entries = ref<DiaryEntry[]>([])
const loading = ref(false)
const replyTexts = reactive<Record<number, string>>({})
const submittingComment = ref<number | null>(null)

async function fetchEntries() {
    loading.value = true
    try {
        const res = await axios.get(`/api/adoption-cases/${props.caseId}/diary`)
        entries.value = res.data.data
    } catch (error) {
        console.error('Failed to fetch diary entries:', error)
    } finally {
        loading.value = false
    }
}

async function submitComment(entryId: number) {
    const text = replyTexts[entryId]?.trim()
    if (!text || submittingComment.value) return

    submittingComment.value = entryId

    try {
        const res = await axios.post(`/api/diary-entries/${entryId}/comments`, {
            content: text
        })

        // Add comment to the entry locally
        const entry = entries.value.find(e => e.id === entryId)
        if (entry) {
            entry.comments.push(res.data.data)
        }
        replyTexts[entryId] = ''
    } catch (error) {
        console.error('Failed to submit comment:', error)
    } finally {
        submittingComment.value = null
    }
}

function formatDate(dateStr: string) {
    const date = new Date(dateStr)
    const now = new Date()
    const diffMs = now.getTime() - date.getTime()
    const diffMin = Math.floor(diffMs / 60000)
    const diffHour = Math.floor(diffMs / 3600000)
    const diffDay = Math.floor(diffMs / 86400000)

    if (diffMin < 1) return $t('diary.JustNow')
    if (diffMin < 60) return `${diffMin} ${$t('diary.MinAgo')}`
    if (diffHour < 24) return `${diffHour} ${$t('diary.HourAgo')}`
    if (diffDay < 7) return `${diffDay} ${$t('diary.DayAgo')}`

    return date.toLocaleDateString('zh-TW', {
        year: 'numeric', month: '2-digit', day: '2-digit'
    })
}

function openImage(src: string) {
    window.open(src, '_blank')
}

onMounted(fetchEntries)
watch(() => props.refreshKey, fetchEntries)
</script>

<style scoped>
.diary-timeline__empty-icon {
    font-size: 3rem;
}

.diary-timeline__card {
    background: white;
    border: 1px solid #e2e8f0;
    transition: all 0.25s ease;
}

.diary-timeline__card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
}

.diary-timeline__photo-single {
    overflow: hidden;
}

.diary-timeline__photo-img {
    height: 280px;
    object-fit: contain;
    background: #f1f5f9;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.diary-timeline__photo-img:hover {
    transform: scale(1.02);
}

/* Carousel custom styling */
.carousel-indicators button {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    opacity: 0.6;
}

.carousel-indicators button.active {
    opacity: 1;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 24px;
    height: 24px;
    background-color: rgba(0, 0, 0, 0.3);
    border-radius: 50%;
    background-size: 12px;
}

.diary-timeline__location-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
}

.diary-timeline__content {
    font-size: 0.9rem;
    line-height: 1.6;
    word-break: break-word;
}

.diary-timeline__comments {
    border-color: #f1f5f9 !important;
}

.diary-timeline__comment-avatar {
    font-size: 1.1rem;
    margin-top: 2px;
}

.diary-timeline__reply-input {
    border: 1px solid #e2e8f0;
    font-size: 0.85rem;
}

.diary-timeline__reply-input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.15rem rgba(44, 82, 130, 0.1);
}

.diary-timeline__reply-btn {
    white-space: nowrap;
    font-size: 0.8rem;
}

@media (max-width: 576px) {
    .diary-timeline__photo-img {
        height: 200px;
    }
}
</style>
