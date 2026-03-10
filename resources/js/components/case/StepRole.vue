<template>
    <div class="create-case__body">
        <h3 class="create-case__subtitle">{{ $t('case.SelectRole') }}</h3>
        <p class="create-case__hint">{{ $t('case.SelectRoleHint') }}</p>

        <div class="create-case__role-grid">
            <button type="button" class="create-case__role-card"
                :class="{ 'create-case__role-card--selected': modelValue === 'owner' }"
                @click="$emit('update:modelValue', 'owner')">
                <div class="create-case__role-icon">
                    <i class="bi bi-house-heart-fill"></i>
                </div>
                <h4 class="create-case__role-title">{{ $t('case.RoleOwner') }}</h4>
                <p class="create-case__role-desc">{{ $t('case.RoleOwnerDesc') }}</p>
            </button>

            <button type="button" class="create-case__role-card"
                :class="{ 'create-case__role-card--selected': modelValue === 'adopter' }"
                @click="$emit('update:modelValue', 'adopter')">
                <div class="create-case__role-icon">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <h4 class="create-case__role-title">{{ $t('case.RoleAdopter') }}</h4>
                <p class="create-case__role-desc">{{ $t('case.RoleAdopterDesc') }}</p>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    modelValue: string
}>()
defineEmits(['update:modelValue'])
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

.create-case__role-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.create-case__role-card {
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.75rem 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
    width: 100%;
}

.create-case__role-card:hover {
    border-color: var(--color-denim-blue);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44, 82, 130, 0.1);
}

.create-case__role-card--selected {
    border-color: var(--color-denim-blue);
    background: rgba(44, 82, 130, 0.04);
    box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.12);
}

.create-case__role-icon {
    font-size: 2rem;
    color: var(--color-denim-blue);
    margin-bottom: 0.75rem;
}

.create-case__role-title {
    font-size: 1rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.create-case__role-desc {
    font-size: 0.8rem;
    color: #718096;
    margin: 0;
    line-height: 1.5;
}

@media (max-width: 576px) {
    .create-case__role-grid {
        grid-template-columns: 1fr;
    }

    .create-case__body {
        padding: 1.25rem;
    }
}
</style>
