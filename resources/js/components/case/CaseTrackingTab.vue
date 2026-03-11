<template>
    <div class="case-tracking-tab__card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="case-tracking-tab__title mb-0">
                <i class="bi bi-clock-history me-2"></i>{{ $t('caseDetail.TrackingTimeline') }}
            </h5>
            <button v-if="role === 'adopter' && status === 'active' && hasTrackingConfig"
                class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#trackingReportModal">
                <i class="bi bi-pencil-square me-1"></i>
                {{ $t('Submit Report') }}
            </button>
        </div>

        <TrackingReportTimeline :case-id="caseId" :refresh-key="refreshKey" />

        <!-- Report Form Modal -->
        <TrackingReportForm v-if="role === 'adopter'" :case-id="caseId" :pet-name="petName" :pet-image="petImage"
            @submitted="onSubmitted" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { trans } from 'laravel-vue-i18n'
import TrackingReportTimeline from '@/components/adoptions/TrackingReportTimeline.vue'
import TrackingReportForm from '@/components/adoptions/TrackingReportForm.vue'

const $t = trans

interface Props {
    caseId: number
    role: 'owner' | 'adopter'
    status: string
    hasTrackingConfig: boolean
    petName: string
    petImage: string
}

defineProps<Props>()

const refreshKey = ref(0)

function onSubmitted() {
    refreshKey.value++
}
</script>

<style scoped>
.case-tracking-tab__card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.case-tracking-tab__title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-denim-blue);
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-denim-blue);
    display: inline-block;
}
</style>
