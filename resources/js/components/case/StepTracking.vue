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
                    @click="$emit('update:modelValue', { ...modelValue, frequency: freq.value })">
                    <i :class="freq.icon"></i>
                    <span class="create-case__freq-label">{{ $t(freq.label) }}</span>
                    <span class="create-case__freq-desc">{{ $t(freq.desc) }}</span>
                </button>
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

defineProps<{
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
    }
    searchQuery: string
    lookupLoading: boolean
    lookupError: string
    selectedCounterpart: User | null
}>()

defineEmits([
    'update:modelValue',
    'update:searchQuery',
    'lookup-user',
    'remove-counterpart'
])

const petTypes = [
    { value: 'dog', label: 'dog', icon: 'bi bi-emoji-smile' },
    { value: 'cat', label: 'cat', icon: 'bi bi-emoji-heart-eyes' },
]

const frequencies = [
    { value: 'weekly', label: 'case.FreqWeekly', desc: 'case.FreqWeeklyDesc', icon: 'bi bi-calendar-week' },
    { value: 'monthly', label: 'case.FreqMonthly', desc: 'case.FreqMonthlyDesc', icon: 'bi bi-calendar-month' },
    { value: 'quarterly', label: 'case.FreqQuarterly', desc: 'case.FreqQuarterlyDesc', icon: 'bi bi-calendar3' },
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
    grid-template-columns: repeat(3, 1fr);
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
