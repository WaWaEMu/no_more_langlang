<template>
    <Navbar />
    <Content :title="$t('Create Tracking Case')">
        <template #content>
            <div class="create-case">
                <!-- Progress Steps -->
                <div class="create-case__progress">
                    <div v-for="(stepItem, index) in steps" :key="index" class="create-case__step" :class="{
                        'create-case__step--active': step === index + 1,
                        'create-case__step--done': step > index + 1
                    }">
                        <div class="create-case__step-circle">
                            <i v-if="step > index + 1" class="bi bi-check-lg"></i>
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        <span class="create-case__step-label">{{ $t(stepItem.label) }}</span>
                    </div>
                    <div class="create-case__progress-line">
                        <div class="create-case__progress-fill"
                            :style="{ width: ((step - 1) / (steps.length - 1)) * 100 + '%' }"></div>
                    </div>
                </div>

                <!-- Steps Content -->
                <Transition name="step-fade" mode="out-in">
                    <StepRole v-if="step === 1" v-model="form.role" />
                    <StepPetInfo v-else-if="step === 2" v-model="form" :image-preview="imagePreview"
                        @file-change="handleFileChange" @drop-file="handleDrop" @remove-image="removeImage" />
                    <StepTracking v-else-if="step === 3" v-model="form" v-model:search-query="searchQuery"
                        :lookup-loading="lookupLoading" :lookup-error="lookupError"
                        :selected-counterpart="selectedCounterpart" @lookup-user="lookupUser"
                        @remove-counterpart="removeCounterpart" />
                </Transition>

                <!-- Navigation Buttons -->
                <div class="create-case__actions">
                    <button v-if="step > 1" type="button" class="btn create-case__btn create-case__btn--back"
                        @click="step--">
                        <i class="bi bi-arrow-left me-1"></i>{{ $t('case.Back') }}
                    </button>
                    <div v-else></div>

                    <button v-if="step < steps.length" type="button" class="btn btn-primary create-case__btn"
                        :disabled="!canProceed" @click="step++">
                        {{ $t('case.Next') }}<i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <button v-else type="button" class="btn btn-primary create-case__btn"
                        :disabled="submitting || !canProceed" @click="submitCase">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="bi bi-check-lg me-1"></i>
                        {{ $t('case.Submit') }}
                    </button>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="CreateCase">
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import StepRole from '@/components/case/StepRole.vue'
import StepPetInfo from '@/components/case/StepPetInfo.vue'
import StepTracking from '@/components/case/StepTracking.vue'
import { useManualCaseCreation } from '@/composables/useManualCaseCreation'

const steps = [
    { label: 'case.StepRole' },
    { label: 'case.StepPet' },
    { label: 'case.StepTracking' },
]

const {
    step,
    submitting,
    imagePreview,
    searchQuery,
    lookupLoading,
    lookupError,
    selectedCounterpart,
    form,
    canProceed,
    handleFileChange,
    handleDrop,
    removeImage,
    lookupUser,
    removeCounterpart,
    submitCase
} = useManualCaseCreation()
</script>

<style scoped>
/* ── Progress Bar ── */
.create-case {
    max-width: 680px;
    margin: 0 auto;
}

.create-case__progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 2.5rem;
    padding: 0 1rem;
}

.create-case__progress-line {
    position: absolute;
    top: 18px;
    left: 60px;
    right: 60px;
    height: 3px;
    background: #dee2e8;
    z-index: 0;
    border-radius: 2px;
}

.create-case__progress-fill {
    height: 100%;
    background: var(--color-denim-blue);
    border-radius: 2px;
    transition: width 0.4s ease;
}

.create-case__step {
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 1;
    gap: 0.5rem;
}

.create-case__step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    background: #e2e8f0;
    color: #a0aec0;
    transition: all 0.3s ease;
}

.create-case__step--active .create-case__step-circle {
    background: var(--color-denim-blue);
    color: white;
    box-shadow: 0 0 0 4px rgba(44, 82, 130, 0.15);
}

.create-case__step--done .create-case__step-circle {
    background: #38a169;
    color: white;
}

.create-case__step-label {
    font-size: 0.75rem;
    color: #a0aec0;
    font-weight: 500;
    white-space: nowrap;
}

.create-case__step--active .create-case__step-label {
    color: var(--color-denim-blue);
    font-weight: 600;
}

.create-case__step--done .create-case__step-label {
    color: #38a169;
}

/* ── Actions ── */
.create-case__actions {
    display: flex;
    justify-content: space-between;
    margin-top: 1.5rem;
}

.create-case__btn {
    padding: 0.625rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
}

.create-case__btn--back {
    background: transparent;
    color: #718096;
    border: 1.5px solid #e2e8f0;
}

.create-case__btn--back:hover {
    background: #f7fafc;
    color: #4a5568;
}

/* ── Step Transition ── */
.step-fade-enter-active,
.step-fade-leave-active {
    transition: all 0.3s ease;
}

.step-fade-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

.step-fade-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}

/* ── Responsive ── */
@media (max-width: 576px) {
    .create-case__step-label {
        display: none;
    }
}
</style>
