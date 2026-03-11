<template>
    <div class="case-diary-tab__card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="case-diary-tab__title mb-0">
                <i class="bi bi-camera me-2"></i>{{ $t('diary.SectionTitle') }}
            </h5>
            <button v-if="status === 'active' && role === 'adopter'"
                class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#diaryEntryModal">
                <i class="bi bi-plus-lg me-1"></i>
                {{ $t('diary.AddEntry') }}
            </button>
        </div>

        <DiaryTimeline :case-id="caseId" :refresh-key="refreshKey" />

        <!-- Diary Entry Modal -->
        <DiaryEntryForm v-if="role === 'adopter'" :case-id="caseId" :pet-name="petName" :pet-image="petImage"
            @submitted="onSubmitted" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { trans } from 'laravel-vue-i18n'
import DiaryTimeline from '@/components/diary/DiaryTimeline.vue'
import DiaryEntryForm from '@/components/diary/DiaryEntryForm.vue'

const $t = trans

interface Props {
    caseId: number
    role: 'owner' | 'adopter'
    status: string
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
.case-diary-tab__card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.case-diary-tab__title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-denim-blue);
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-denim-blue);
    display: inline-block;
}
</style>
