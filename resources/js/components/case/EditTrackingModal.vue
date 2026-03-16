<template>
    <div class="modal fade" id="editTrackingModal" tabindex="-1" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-clock-history me-2 text-primary"></i>{{ $t('caseDetail.EditTrackingTitle') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <!-- Frequency Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted letter-spacing-1 mb-3">
                            {{ $t('case.TrackingFrequency') }}
                        </label>
                        <div class="edit-tracking__freq-grid">
                            <button type="button" v-for="freq in frequencies" :key="freq.value"
                                class="edit-tracking__freq-btn"
                                :class="{ 'edit-tracking__freq-btn--selected': form.frequency === freq.value }"
                                @click="handleFrequencyChange(freq.value)">
                                <i :class="freq.icon"></i>
                                <span class="edit-tracking__freq-label">{{ $t(freq.label) }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Day Picker -->
                    <div v-if="form.frequency" class="animate__animated animate__fadeIn">
                        <!-- Weekly -->
                        <div v-if="form.frequency === 'weekly'">
                            <label class="form-label fw-bold small text-uppercase text-muted mb-3">
                                {{ $t('case.TrackingDay') }}
                            </label>
                            <div class="edit-tracking__weekday-grid">
                                <button type="button" v-for="day in weekdays" :key="day.value"
                                    class="edit-tracking__weekday-btn"
                                    :class="{ 'edit-tracking__weekday-btn--selected': form.tracking_day === day.value }"
                                    @click="form.tracking_day = day.value">
                                    {{ $t(day.label) }}
                                </button>
                            </div>
                        </div>

                        <!-- Monthly -->
                        <div v-else-if="form.frequency === 'monthly'">
                            <label class="form-label fw-bold small text-uppercase text-muted mb-2">
                                {{ $t('case.TrackingDay') }}
                            </label>
                            <select class="form-select edit-tracking__select" v-model="form.tracking_day">
                                <option :value="null" disabled>{{ $t('Please select') }}</option>
                                <option v-for="d in 31" :key="d" :value="d">{{ d }} 日</option>
                            </select>
                            <p class="edit-tracking__hint mt-2 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ $t('case.TrackingDayShortMonthNote') }}
                            </p>
                        </div>

                        <!-- Quarterly -->
                        <div v-else-if="form.frequency === 'quarterly'">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted mb-3">
                                    {{ $t('case.QuarterStartMonth') }}
                                </label>
                                <div class="edit-tracking__quarter-grid">
                                    <button type="button" v-for="q in quarterOptions" :key="q.startMonth"
                                        class="edit-tracking__quarter-btn"
                                        :class="{ 'edit-tracking__quarter-btn--selected': form.tracking_start_month === q.startMonth }"
                                        @click="form.tracking_start_month = q.startMonth">
                                        <span class="edit-tracking__quarter-label">{{ q.label }}</span>
                                        <span class="edit-tracking__quarter-months">{{ q.months }}</span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold small text-uppercase text-muted mb-2">
                                    {{ $t('case.TrackingDay') }}
                                </label>
                                <select class="form-select edit-tracking__select" v-model="form.tracking_day">
                                    <option :value="null" disabled>{{ $t('Please select') }}</option>
                                    <option v-for="d in 31" :key="d" :value="d">{{ d }} 日</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"
                        :disabled="saving">
                        {{ $t('Cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" :disabled="!isValid || saving"
                        @click="save">
                        <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="bi bi-check-lg me-1"></i>
                        {{ $t('Save Changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed, onMounted } from 'vue'
import { trans } from 'laravel-vue-i18n'
import axios from 'axios'
import { Modal } from 'bootstrap'
import Swal from 'sweetalert2'

const $t = trans

const props = defineProps<{
    caseId: number
    initialConfig: {
        frequency: string
        tracking_day?: number | null
        tracking_start_month?: number | null
    }
}>()

const emit = defineEmits(['updated'])

const modalRef = ref<HTMLElement | null>(null)
let modalInstance: Modal | null = null

const form = reactive({
    frequency: props.initialConfig.frequency,
    tracking_day: props.initialConfig.tracking_day || null,
    tracking_start_month: props.initialConfig.tracking_start_month || null
})

const saving = ref(false)

const frequencies = [
    { value: 'weekly', label: 'case.FreqWeekly', icon: 'bi bi-calendar-event' },
    { value: 'monthly', label: 'case.FreqMonthly', icon: 'bi bi-calendar-month' },
    { value: 'quarterly', label: 'case.FreqQuarterly', icon: 'bi bi-calendar-range' },
    { value: 'none', label: 'case.FreqNone', icon: 'bi bi-bell-slash' },
]

const weekdays = [
    { value: 1, label: 'case.Weekday.1' },
    { value: 2, label: 'case.Weekday.2' },
    { value: 3, label: 'case.Weekday.3' },
    { value: 4, label: 'case.Weekday.4' },
    { value: 5, label: 'case.Weekday.5' },
    { value: 6, label: 'case.Weekday.6' },
    { value: 7, label: 'case.Weekday.7' },
]

const quarterOptions = [
    { startMonth: 1, label: 'Q1 循環', months: '1、4、7、10 月' },
    { startMonth: 2, label: 'Q2 循環', months: '2、5、8、11 月' },
    { startMonth: 3, label: 'Q3 循環', months: '3、6、9、12 月' },
]

const isValid = computed(() => {
    if (!form.frequency) return false
    if (form.frequency === 'none') return true
    if (form.frequency === 'weekly' || form.frequency === 'monthly') {
        return form.tracking_day !== null
    }
    if (form.frequency === 'quarterly') {
        return form.tracking_day !== null && form.tracking_start_month !== null
    }
    return false
})

function handleFrequencyChange(val: string) {
    form.frequency = val
    form.tracking_day = null
    form.tracking_start_month = null
}

async function save() {
    if (!isValid.value) return

    saving.value = true
    try {
        await axios.patch(`/api/adoption-cases/${props.caseId}/tracking-config`, form)

        await Swal.fire({
            icon: 'success',
            title: $t('Update Successful'),
            text: $t('caseDetail.UpdateTrackingSuccess'),
            confirmButtonText: $t('Got it'),
            confirmButtonColor: 'var(--color-denim-blue)',
        })

        emit('updated')
        modalInstance?.hide()
    } catch (error) {
        console.error('Failed to update tracking config:', error)

        Swal.fire({
            icon: 'error',
            title: $t('Error'),
            text: $t('caseDetail.UpdateTrackingError'),
            confirmButtonText: $t('Got it'),
            confirmButtonColor: 'var(--color-denim-blue)',
        })
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    if (modalRef.value) {
        modalInstance = new Modal(modalRef.value)
    }
})

function show() {
    // Reset form to initial props when showing
    form.frequency = props.initialConfig.frequency
    form.tracking_day = props.initialConfig.tracking_day || null
    form.tracking_start_month = props.initialConfig.tracking_start_month || null
    modalInstance?.show()
}

defineExpose({ show })
</script>

<style scoped>
.edit-tracking__freq-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.edit-tracking__freq-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.edit-tracking__freq-btn:hover {
    border-color: var(--color-denim-blue-light);
    background: #f1f5f9;
}

.edit-tracking__freq-btn--selected {
    border-color: var(--color-denim-blue);
    background: #eff6ff;
    color: var(--color-denim-blue);
}

.edit-tracking__freq-label {
    font-size: 0.85rem;
    font-weight: 600;
}

.edit-tracking__weekday-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.4rem;
}

.edit-tracking__weekday-btn {
    padding: 0.5rem 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    transition: all 0.2s ease;
}

.edit-tracking__weekday-btn:hover {
    border-color: var(--color-denim-blue-light);
    color: var(--color-denim-blue);
}

.edit-tracking__weekday-btn--selected {
    background: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
    color: white !important;
}

.edit-tracking__select {
    border-radius: 10px;
    padding: 0.75rem;
    font-weight: 500;
}

.edit-tracking__quarter-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.5rem;
}

.edit-tracking__quarter-btn {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    transition: all 0.2s ease;
}

.edit-tracking__quarter-btn--selected {
    border-color: var(--color-denim-blue);
    background: #eff6ff;
    color: var(--color-denim-blue);
    font-weight: 600;
}

.edit-tracking__quarter-label {
    font-size: 0.9rem;
}

.edit-tracking__quarter-months {
    font-size: 0.75rem;
    opacity: 0.7;
}

.edit-tracking__hint {
    font-size: 0.75rem;
    color: #94a3b8;
}

.letter-spacing-1 {
    letter-spacing: 0.05em;
}
</style>
