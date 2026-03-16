<template>
    <div class="create-case__body">
        <h3 class="create-case__subtitle">{{ $t('case.TrackingConfig') }}</h3>
        <p class="create-case__hint">{{ $t('case.TrackingConfigHint') }}</p>

        <!-- Counterpart Search -->
        <div class="create-case__form-group">
            <label class="create-case__label">
                {{ modelValue.role === 'owner' ? $t('case.Adopter') : $t('case.Owner') }}
                <span class="create-case__label-hint">{{ $t('case.CounterpartHint') }}</span>
            </label>

            <!-- Selected User -->
            <div v-if="selectedCounterpart" class="create-case__selected-user">
                <div class="create-case__selected-user-info">
                    <i class="bi bi-person-check-fill"></i>
                    <div>
                        <span class="fw-semibold">{{ selectedCounterpart.name }}</span>
                        <small class="text-muted d-block">{{ selectedCounterpart.email }}</small>
                    </div>
                </div>
                <button type="button" class="create-case__selected-user-remove" @click="$emit('remove-counterpart')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Email Lookup Input -->
            <div v-else class="create-case__lookup-wrap">
                <div class="create-case__lookup-row">
                    <div class="create-case__lookup-input-wrap">
                        <i class="bi bi-envelope create-case__lookup-icon"></i>
                        <input type="email" class="form-control create-case__input create-case__lookup-input"
                            :value="searchQuery"
                            @input="$emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
                            @keyup.enter="$emit('lookup-user')" :placeholder="$t('case.LookupPlaceholder')" />
                    </div>
                    <button type="button" class="btn btn-primary create-case__lookup-btn" :disabled="lookupLoading"
                        @click="$emit('lookup-user')">
                        <span v-if="lookupLoading" class="spinner-border spinner-border-sm"></span>
                        <i v-else class="bi bi-search"></i>
                        {{ $t('case.LookupButton') }}
                    </button>
                </div>
                <p v-if="lookupError" class="create-case__lookup-error">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ lookupError }}
                </p>
            </div>
        </div>

        <div class="create-case__form-group">
            <label class="create-case__label">{{ $t('case.TrackingFrequency') }}</label>
            <div class="create-case__freq-grid">
                <button type="button" v-for="freq in frequencies" :key="freq.value" class="create-case__freq-btn"
                    :class="{ 'create-case__freq-btn--selected': modelValue.frequency === freq.value }"
                    @click="handleFrequencyChange(freq.value)">
                    <i :class="freq.icon"></i>
                    <span class="create-case__freq-label">{{ $t(freq.label) }}</span>
                    <span class="create-case__freq-desc">{{ $t(freq.desc) }}</span>
                </button>
            </div>
        </div>

        <!-- Day Picker (conditionally shown after selecting a frequency) -->
        <div v-if="modelValue.frequency" class="create-case__form-group">
            <!-- Weekly: Weekday buttons -->
            <div v-if="modelValue.frequency === 'weekly'">
                <label class="create-case__label">
                    {{ $t('case.TrackingDay') }}
                    <span class="create-case__label-hint">
                        {{ $t(`case.TrackingDayHint.${modelValue.frequency}`) }}
                    </span>
                </label>
                <div class="create-case__weekday-grid">
                    <button type="button" v-for="day in weekdays" :key="day.value" class="create-case__weekday-btn"
                        :class="{ 'create-case__weekday-btn--selected': modelValue.tracking_day === day.value }"
                        @click="$emit('update:modelValue', { ...modelValue, tracking_day: day.value })">
                        {{ $t(day.label) }}
                    </button>
                </div>
            </div>

            <!-- Monthly: Day dropdown -->
            <div v-else-if="modelValue.frequency === 'monthly'">
                <label class="create-case__label">
                    {{ $t('case.TrackingDay') }}
                    <span class="create-case__label-hint">
                        {{ $t(`case.TrackingDayHint.${modelValue.frequency}`) }}
                    </span>
                </label>
                <div class="create-case__day-select-wrap">
                    <select class="form-select create-case__input" :value="modelValue.tracking_day"
                        @change="$emit('update:modelValue', { ...modelValue, tracking_day: Number(($event.target as HTMLSelectElement).value) })">
                        <option :value="undefined" disabled>{{ $t('Please select') }}</option>
                        <option v-for="d in 31" :key="d" :value="d">{{ d }} 日</option>
                    </select>
                    <p class="create-case__day-hint">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ $t('case.TrackingDayShortMonthNote') }}
                    </p>
                </div>
            </div>

            <!-- Quarterly: Start month + Day dropdown -->
            <div v-else-if="modelValue.frequency === 'quarterly'">
                <div class="mb-4">
                    <label class="create-case__label mb-3">
                        {{ $t('case.QuarterStartMonth') }}
                        <span class="create-case__label-hint">
                            {{ $t(`case.TrackingDayHint.${modelValue.frequency}`) }}
                        </span>
                    </label>
                    <div class="create-case__quarter-grid">
                        <button type="button" v-for="q in quarterOptions" :key="q.startMonth"
                            class="create-case__quarter-btn"
                            :class="{ 'create-case__quarter-btn--selected': modelValue.tracking_start_month === q.startMonth }"
                            @click="$emit('update:modelValue', { ...modelValue, tracking_start_month: q.startMonth })">
                            <span class="create-case__quarter-label">{{ q.label }}</span>
                            <span class="create-case__quarter-months">{{ q.months }}</span>
                        </button>
                    </div>
                </div>

                <div class="create-case__day-select-wrap">
                    <label class="create-case__label mb-2">{{ $t('case.TrackingDay') }}</label>
                    <select class="form-select create-case__input" :value="modelValue.tracking_day"
                        @change="$emit('update:modelValue', { ...modelValue, tracking_day: Number(($event.target as HTMLSelectElement).value) })">
                        <option :value="undefined" disabled>{{ $t('Please select') }}</option>
                        <option v-for="d in 31" :key="d" :value="d">{{ d }} 日</option>
                    </select>
                    <p class="create-case__day-hint">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ $t('case.TrackingDayShortMonthNote') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="create-case__summary">
            <h4 class="create-case__summary-title">
                <i class="bi bi-clipboard-check me-2"></i>{{ $t('case.Summary') }}
            </h4>
            <div class="create-case__summary-grid">
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">{{ $t('case.YourRole') }}</span>
                    <span class="create-case__summary-value">
                        {{ modelValue.role === 'owner' ? $t('case.RoleOwner') : $t('case.RoleAdopter') }}
                    </span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">
                        {{ modelValue.role === 'owner' ? $t('case.Adopter') : $t('case.Owner') }}
                    </span>
                    <span class="create-case__summary-value">
                        {{ selectedCounterpart?.name || $t('case.NotSpecified') }}
                    </span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">{{ $t('case.PetName') }}</span>
                    <span class="create-case__summary-value">{{ modelValue.pet_name }}</span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">{{ $t('case.PetType') }}</span>
                    <span class="create-case__summary-value">
                        {{$t(petTypes.find(t => t.value === modelValue.pet_type)?.label || '')}}
                    </span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">性別</span>
                    <span class="create-case__summary-value">
                        {{ modelValue.gender === 'male' ? '男生' : modelValue.gender === 'female' ? '女生' : '-' }}
                    </span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">年紀</span>
                    <span class="create-case__summary-value">{{ modelValue.age || '-' }}</span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">花紋</span>
                    <span class="create-case__summary-value">{{ modelValue.color || '-' }}</span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">毛型</span>
                    <span class="create-case__summary-value">{{ modelValue.fur_type || '-' }}</span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">結紮</span>
                    <span class="create-case__summary-value">
                        {{ modelValue.is_neuter === true ? '已結紮' : modelValue.is_neuter === false ? '未結紮' : '-' }}
                    </span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">地點</span>
                    <span class="create-case__summary-value">
                        {{ modelValue.city && modelValue.town ? `${modelValue.city} ${modelValue.town}` : '-' }}
                    </span>
                </div>
                <div class="create-case__summary-item">
                    <span class="create-case__summary-key">{{ $t('case.TrackingFrequency') }}</span>
                    <span class="create-case__summary-value">
                        {{$t(frequencies.find(f => f.value === modelValue.frequency)?.label || '')}}
                    </span>
                </div>
                <div class="create-case__summary-item" v-if="modelValue.tracking_day">
                    <span class="create-case__summary-key">{{ $t('case.TrackingDay') }}</span>
                    <span class="create-case__summary-value">
                        <template v-if="modelValue.frequency === 'weekly'">
                            {{ $t('case.FreqWeekly') }}{{ $t(`case.Weekday.${modelValue.tracking_day}`) }}
                        </template>
                        <template v-else-if="modelValue.frequency === 'quarterly' && modelValue.tracking_start_month">
                            {{ getQuarterlyCycleText(modelValue.tracking_start_month) }} {{ modelValue.tracking_day }} 日
                        </template>
                        <template v-else>
                            {{ modelValue.tracking_day }} 日
                        </template>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
interface User {
    id: number
    name: string
    email: string
}

const props = defineProps<{
    modelValue: {
        role: string
        pet_name: string
        pet_type: string
        gender: string
        age: string
        color: string
        fur_type: string
        is_neuter: boolean | null
        is_stray: boolean | null
        city: string
        town: string
        frequency: string
        tracking_day: number | null
        tracking_start_month: number | null
    }
    searchQuery: string
    lookupLoading: boolean
    lookupError: string
    selectedCounterpart: User | null
}>()

const emit = defineEmits([
    'update:modelValue',
    'update:searchQuery',
    'lookup-user',
    'remove-counterpart'
])

function handleFrequencyChange(newFreq: string) {
    // Reset tracking_day and tracking_start_month when frequency changes
    emit('update:modelValue', {
        ...props.modelValue,
        frequency: newFreq,
        tracking_day: null,
        tracking_start_month: null,
    })
}

function getQuarterlyCycleText(startMonth: number): string {
    const months = [startMonth, startMonth + 3, startMonth + 6, startMonth + 9]
        .map(m => ((m - 1) % 12) + 1)
    return months.join('、') + ' 月'
}

const quarterOptions = [
    { startMonth: 1, label: 'Q1', months: '1、4、7、10 月' },
    { startMonth: 2, label: 'Q2', months: '2、5、8、11 月' },
    { startMonth: 3, label: 'Q3', months: '3、6、9、12 月' },
]

const petTypes = [
    { value: 'dog', label: 'dog', icon: 'bi bi-emoji-smile' },
    { value: 'cat', label: 'cat', icon: 'bi bi-emoji-heart-eyes' },
]

const frequencies = [
    { value: 'weekly', label: 'case.FreqWeekly', desc: 'case.FreqWeeklyDesc', icon: 'bi bi-calendar-week' },
    { value: 'monthly', label: 'case.FreqMonthly', desc: 'case.FreqMonthlyDesc', icon: 'bi bi-calendar-month' },
    { value: 'quarterly', label: 'case.FreqQuarterly', desc: 'case.FreqQuarterlyDesc', icon: 'bi bi-calendar3' },
    { value: 'none', label: 'case.FreqNone', desc: 'case.FreqNoneDesc', icon: 'bi bi-bell-slash' },
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
</script>
<style scoped>
.create-case__body {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
}

.create-case__subtitle {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.create-case__hint {
    color: #718096;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.create-case__form-group {
    margin-bottom: 1.5rem;
}

.create-case__label {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.create-case__label-hint {
    font-weight: 400;
    font-size: 0.75rem;
    color: #a0aec0;
    margin-left: 0.5rem;
}

.create-case__input {
    width: 100%;
    border-radius: 10px;
    padding: 0.625rem 1rem;
    border: 1.5px solid #e2e8f0;
    transition: border-color 0.2s;
}

.create-case__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.1);
    outline: none;
}

.create-case__lookup-wrap {
    position: relative;
}

.create-case__lookup-row {
    display: flex;
    gap: 0.5rem;
    align-items: stretch;
}

.create-case__lookup-input-wrap {
    flex: 1;
    position: relative;
}

.create-case__lookup-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    z-index: 1;
}

.create-case__lookup-input {
    padding-left: 2.25rem !important;
}

.create-case__lookup-btn {
    white-space: nowrap;
    border-radius: 10px;
    padding: 0.625rem 1rem;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.create-case__lookup-error {
    color: #e53e3e;
    font-size: 0.8rem;
    margin-top: 0.5rem;
    margin-bottom: 0;
}

.create-case__selected-user {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f0f4f8;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
}

.create-case__selected-user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.create-case__selected-user-info i {
    font-size: 1.5rem;
    color: var(--color-denim-blue);
}

.create-case__selected-user-remove {
    background: transparent;
    border: none;
    color: #a0aec0;
    cursor: pointer;
    padding: 0.25rem;
    font-size: 0.8rem;
    transition: color 0.2s;
}

.create-case__selected-user-remove:hover {
    color: #e53e3e;
}

.create-case__freq-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.create-case__freq-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 1.25rem 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: #f7fafc;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.create-case__freq-btn:hover {
    border-color: var(--color-denim-blue);
}

.create-case__freq-btn--selected {
    border-color: var(--color-denim-blue);
    background: rgba(44, 82, 130, 0.04);
}

.create-case__freq-btn i {
    font-size: 1.5rem;
    color: var(--color-denim-blue);
}

.create-case__freq-label {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9rem;
}

.create-case__freq-desc {
    font-size: 0.75rem;
    color: #a0aec0;
}

/* Weekday Picker */
.create-case__weekday-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}

.create-case__weekday-btn {
    padding: 0.6rem 0;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background: #f7fafc;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    color: #4a5568;
    transition: all 0.2s ease;
    text-align: center;
}

.create-case__weekday-btn:hover {
    border-color: var(--color-denim-blue);
    color: var(--color-denim-blue);
}

.create-case__weekday-btn--selected {
    border-color: var(--color-denim-blue);
    background: rgba(44, 82, 130, 0.08);
    color: var(--color-denim-blue);
}

/* Day of Month Select */
.create-case__day-select-wrap {
    max-width: 280px;
}

.create-case__day-hint {
    font-size: 0.75rem;
    color: #a0aec0;
    margin-top: 0.5rem;
    margin-bottom: 0;
}

/* Quarter Picker */
.create-case__quarter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.create-case__quarter-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 1rem 0.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: #f7fafc;
    cursor: pointer;
    transition: all 0.2s ease;
}

.create-case__quarter-btn:hover {
    border-color: var(--color-denim-blue);
}

.create-case__quarter-btn--selected {
    border-color: var(--color-denim-blue);
    background: rgba(44, 82, 130, 0.04);
}

.create-case__quarter-label {
    font-weight: 700;
    font-size: 1rem;
    color: var(--color-denim-blue);
}

.create-case__quarter-months {
    font-size: 0.7rem;
    color: #a0aec0;
}

.create-case__summary {
    margin-top: 1.5rem;
    background: #f7fafc;
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid #e2e8f0;
}

.create-case__summary-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--color-denim-blue);
    margin-bottom: 1rem;
}

.create-case__summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.create-case__summary-item {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.create-case__summary-key {
    font-size: 0.75rem;
    color: #a0aec0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.create-case__summary-value {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9rem;
}

@media (max-width: 576px) {

    .create-case__freq-grid,
    .create-case__summary-grid {
        grid-template-columns: 1fr;
    }

    .create-case__body {
        padding: 1.25rem;
    }
}
</style>
