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

        <!-- Grouped by Date -->
        <div v-else class="d-flex flex-column gap-4 text-start">
            <div v-for="group in groupedEntries" :key="group.date">

                <!-- Date Header -->
                <div class="diary-timeline__date-header mb-3">
                    <span class="diary-timeline__date-label">{{ group.dateLabel }}</span>
                    <div class="diary-timeline__date-line"></div>
                </div>

                <!-- 2-Column Grid -->
                <div class="diary-timeline__grid">
                    <div v-for="entry in group.entries" :key="entry.id" class="diary-timeline__card">

                        <!-- Photo (Square) - Click to toggle inner carousel if multiple, or just image -->
                        <div class="diary-timeline__photo-area">
                            <template v-if="entry.photos.length === 1">
                                <img :src="`/storage/${entry.photos[0]}`" :alt="entry.content || 'diary'"
                                    class="diary-timeline__photo" @click="openImage(`/storage/${entry.photos[0]}`)">
                            </template>
                            <template v-else>
                                <div :id="`carousel-${entry.id}`" class="carousel slide h-100" data-bs-ride="false">
                                    <div class="carousel-indicators">
                                        <button v-for="(_, idx) in entry.photos" :key="idx" type="button"
                                            :data-bs-target="`#carousel-${entry.id}`" :data-bs-slide-to="idx"
                                            :class="{ active: idx === 0 }"></button>
                                    </div>
                                    <div class="carousel-inner h-100">
                                        <div v-for="(photo, idx) in entry.photos" :key="idx" class="carousel-item h-100"
                                            :class="{ active: idx === 0 }">
                                            <img :src="`/storage/${photo}`" class="diary-timeline__photo"
                                                @click="openImage(`/storage/${photo}`)">
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button"
                                        :data-bs-target="`#carousel-${entry.id}`" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        :data-bs-target="`#carousel-${entry.id}`" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                </div>
                            </template>

                            <!-- Photo count badge -->
                            <span v-if="entry.photos.length > 1" class="diary-timeline__photo-badge">
                                <i class="bi bi-images"></i> {{ entry.photos.length }}
                            </span>
                        </div>

                        <!-- Info Area -->
                        <div class="diary-timeline__info">
                            <!-- Meta -->
                            <div class="diary-timeline__meta mb-2">
                                <span class="diary-timeline__author">
                                    <i class="bi bi-person-fill me-1"></i>{{ entry.author?.name }}
                                </span>
                                <span class="diary-timeline__time">
                                    <i class="bi bi-clock me-1"></i>{{ formatTime(entry.created_at) }}
                                </span>
                            </div>

                            <!-- Text with Pre-wrap & Read More -->
                            <div class="diary-timeline__text-wrap mb-2">
                                <p v-if="entry.content" class="diary-timeline__text"
                                    :class="{ 'diary-timeline__text--collapsed': !isTextExpanded(entry.id) }">
                                    {{ entry.content }}
                                </p>
                                <p v-else class="diary-timeline__text diary-timeline__text--empty">
                                    {{ $t('diary.NoContent') }}
                                </p>
                                <button v-if="shouldShowReadMore(entry.content)"
                                    @click.stop="toggleTextExpand(entry.id)" class="diary-timeline__more-btn">
                                    {{ isTextExpanded(entry.id) ? $t('diary.ReadLess') : '...' + $t('diary.ReadMore') }}
                                </button>
                            </div>

                            <!-- Interaction Bar -->
                            <div class="diary-timeline__footer border-top pt-2 mt-auto">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span v-if="entry.location" class="diary-timeline__tag">
                                        <i class="bi bi-geo-alt-fill"></i> {{ entry.location }}
                                    </span>
                                    <button class="diary-timeline__comment-btn" @click="toggleComments(entry.id)">
                                        <i class="bi bi-chat-fill"></i>
                                        <span class="ms-1">{{ entry.comments.length }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Inline Expandable Comments -->
                            <div v-if="showCommentsId === entry.id" class="diary-timeline__comments-area mt-3">
                                <div v-for="comment in entry.comments" :key="comment.id"
                                    class="diary-timeline__comment">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-person-circle diary-timeline__comment-avatar"></i>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small truncate">{{ comment.author?.name }}</span>
                                                <span class="text-muted x-small">{{ formatTime(comment.created_at)
                                                    }}</span>
                                            </div>
                                            <p class="mb-0 small text-break">{{ comment.content }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reply Input -->
                                <div class="diary-timeline__reply-group mt-2">
                                    <input v-model="replyTexts[entry.id]" type="text"
                                        class="form-control form-control-sm rounded-pill"
                                        :placeholder="$t('diary.ReplyPlaceholder')"
                                        @keyup.enter="submitComment(entry.id)">
                                    <button @click="submitComment(entry.id)"
                                        class="btn btn-sm btn-primary rounded-pill px-2"
                                        :disabled="!replyTexts[entry.id]?.trim() || submittingComment === entry.id">
                                        <i class="bi bi-send-fill" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
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

interface DateGroup {
    date: string
    dateLabel: string
    entries: DiaryEntry[]
}

const props = defineProps<{
    caseId: number
    refreshKey?: number
}>()

const entries = ref<DiaryEntry[]>([])
const loading = ref(false)
const showCommentsId = ref<number | null>(null)
const expandedTextIds = ref<Set<number>>(new Set())
const replyTexts = reactive<Record<number, string>>({})
const submittingComment = ref<number | null>(null)

const weekdays = ['日', '一', '二', '三', '四', '五', '六']

const groupedEntries = computed<DateGroup[]>(() => {
    const groups: Record<string, DiaryEntry[]> = {}
    for (const entry of entries.value) {
        const date = new Date(entry.created_at)
        const key = date.toLocaleDateString('zh-TW', { year: 'numeric', month: '2-digit', day: '2-digit' })
        if (!groups[key]) groups[key] = []
        groups[key].push(entry)
    }
    return Object.entries(groups).map(([date, ents]) => {
        const d = new Date(ents[0].created_at)
        return {
            date,
            dateLabel: `${date}（${weekdays[d.getDay()]}）`,
            entries: ents,
        }
    })
})

function toggleComments(id: number) {
    showCommentsId.value = showCommentsId.value === id ? null : id
}

function isTextExpanded(id: number) {
    return expandedTextIds.value.has(id)
}

function toggleTextExpand(id: number) {
    if (expandedTextIds.value.has(id)) {
        expandedTextIds.value.delete(id)
    } else {
        expandedTextIds.value.add(id)
    }
}

function shouldShowReadMore(content: string | null) {
    if (!content) return false
    return content.length > 40 || (content.match(/\n/g) || []).length > 2
}

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
        const res = await axios.post(`/api/diary-entries/${entryId}/comments`, { content: text })
        const entry = entries.value.find(e => e.id === entryId)
        if (entry) entry.comments.push(res.data.data)
        replyTexts[entryId] = ''
    } catch (error) {
        console.error('Failed to submit comment:', error)
    } finally {
        submittingComment.value = null
    }
}

function formatTime(dateStr: string) {
    return new Date(dateStr).toLocaleTimeString('zh-TW', { hour: '2-digit', minute: '2-digit' })
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

/* ===== Date Header ===== */
.diary-timeline__date-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.diary-timeline__date-label {
    font-size: 0.825rem;
    font-weight: 700;
    color: #475569;
}

.diary-timeline__date-line {
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

/* ===== Grid Layout ===== */
.diary-timeline__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
}

@media (max-width: 900px) {
    .diary-timeline__grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 680px) {
    .diary-timeline__grid {
        grid-template-columns: 1fr;
    }
}

/* ===== Card ===== */
.diary-timeline__card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* ===== Photo Area (Fixed 1:1) ===== */
.diary-timeline__photo-area {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    background: #f1f5f9;
    overflow: hidden;
}

.diary-timeline__photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    cursor: pointer;
}

.diary-timeline__photo-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
    backdrop-filter: blur(4px);
}

/* Carousel Overlay Buttons (More manifest) */
.carousel,
.carousel-inner,
.carousel-item {
    height: 100%;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 32px;
    height: 32px;
    background-color: rgba(255, 255, 255, 0.9);
    background-image: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.carousel-control-prev-icon::after {
    content: '‹';
    color: #334155;
    font-size: 24px;
    font-weight: bold;
}

.carousel-control-next-icon::after {
    content: '›';
    color: #334155;
    font-size: 24px;
    font-weight: bold;
}

/* ===== Info Area ===== */
.diary-timeline__info {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.diary-timeline__meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #94a3b8;
}

.diary-timeline__text {
    font-size: 0.9rem;
    color: #1e293b;
    line-height: 1.6;
    white-space: pre-wrap;
    /* Preserve line breaks */
    margin-bottom: 0;
}

.diary-timeline__text--collapsed {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.diary-timeline__more-btn {
    border: none;
    background: none;
    padding: 0;
    font-size: 0.75rem;
    color: var(--color-denim-blue);
    font-weight: 700;
    margin-top: 4px;
}

/* ===== Interaction Bar ===== */
.diary-timeline__footer {
    border-color: #f1f5f9 !important;
}

.diary-timeline__tag {
    font-size: 0.85rem;
    color: #6366f1;
    background: #eef2ff;
    padding: 2px 10px;
    border-radius: 20px;
    font-weight: 600;
}

.diary-timeline__tag i {
    font-size: 0.9rem;
}

.diary-timeline__comment-btn {
    background: none;
    border: none;
    color: #64748b;
    font-size: 0.95rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: background 0.2s;
}

.diary-timeline__comment-btn i {
    font-size: 1.1rem;
    color: #94a3b8;
}

.diary-timeline__comment-btn:hover {
    background: #f1f5f9;
    color: var(--color-denim-blue);
}

/* ===== Inline Comments Area ===== */
.diary-timeline__comments-area {
    background: #f8fafc;
    border-radius: 8px;
    padding: 0.75rem;
    animation: fadeIn 0.2s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.diary-timeline__comment {
    margin-bottom: 0.75rem;
}

.diary-timeline__comment-avatar {
    font-size: 1.2rem;
    color: #cbd5e1;
}

.x-small {
    font-size: 0.65rem;
}

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.diary-timeline__reply-group {
    display: flex;
    gap: 0.5rem;
}

.diary-timeline__reply-group .form-control {
    font-size: 0.8rem;
}
</style>
