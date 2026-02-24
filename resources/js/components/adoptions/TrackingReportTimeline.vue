<template>
    <div class="report-timeline">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <small class="text-muted ms-2">{{ $t('Loading...') }}</small>
        </div>

        <!-- Empty State -->
        <div v-else-if="reports.length === 0" class="text-center py-4">
            <i class="bi bi-journal-text display-6 text-muted opacity-50"></i>
            <p class="text-muted small mt-2 mb-0">{{ $t('No Reports Yet') }}</p>
        </div>

        <!-- Timeline -->
        <div v-else class="report-timeline__list">
            <div v-for="report in reports" :key="report.id" class="report-timeline__item mb-3">
                <div class="d-flex align-items-start gap-3">
                    <!-- Timeline Dot -->
                    <div class="report-timeline__dot mt-1"></div>

                    <!-- Report Content -->
                    <div class="report-timeline__content flex-grow-1 p-3 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="fw-semibold text-dark">
                                {{ report.reporter?.name }}
                            </small>
                            <small class="text-muted">
                                {{ formatDate(report.reported_at) }}
                            </small>
                        </div>
                        <p class="mb-0 small text-dark report-timeline__text">{{ report.content }}</p>

                        <!-- Report Images -->
                        <div v-if="report.images && report.images.length > 0" class="d-flex gap-2 mt-2 flex-wrap">
                            <img v-for="(img, idx) in report.images" :key="idx"
                                :src="`/storage/${img}`"
                                class="rounded-3 object-fit-cover report-timeline__image"
                                @click="openImage(`/storage/${img}`)">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { TrackingReport } from '@/types/pet'

interface Props {
    caseId: number
    refreshKey?: number
}

const props = defineProps<Props>()

const reports = ref<TrackingReport[]>([])
const loading = ref(false)

async function fetchReports() {
    loading.value = true
    try {
        const res = await axios.get(`/api/adoption-cases/${props.caseId}/reports`)
        reports.value = res.data.data
    } catch (error) {
        console.error('Failed to fetch reports:', error)
    } finally {
        loading.value = false
    }
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    })
}

function openImage(src: string) {
    window.open(src, '_blank')
}

// Fetch reports on mount
onMounted(fetchReports)

// Re-fetch when refreshKey changes (after a new report is submitted)
watch(() => props.refreshKey, fetchReports)
</script>

<style scoped>
.report-timeline__list {
    position: relative;
    padding-left: 8px;
}

.report-timeline__list::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background-color: #dee2e6;
}

.report-timeline__dot {
    width: 12px;
    height: 12px;
    min-width: 12px;
    background-color: var(--color-denim-blue);
    border-radius: 50%;
    position: relative;
    z-index: 1;
}

.report-timeline__content {
    background-color: var(--color-fog-gray);
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.report-timeline__content:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.report-timeline__text {
    white-space: pre-wrap;
    word-break: break-word;
    line-height: 1.6;
}

.report-timeline__image {
    width: 80px;
    height: 80px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.report-timeline__image:hover {
    transform: scale(1.1);
}
</style>
