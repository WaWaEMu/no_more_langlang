<template>
    <div class="form-builder">
        <div class="form-builder__fields">
            <TransitionGroup name="field-list" tag="div" class="form-builder__field-grid">
                <div v-for="(field, index) in fields" :key="field._key" class="form-builder__field-card">
                    <div class="form-builder__field-header">
                        <span class="form-builder__field-number">{{ index + 1 }}</span>
                        <div class="form-builder__field-actions">
                            <button class="form-builder__action-btn form-builder__action-btn--move"
                                :disabled="index === 0" @click="moveField(index, -1)" :title="$t('Move Up')">
                                <i class="bi bi-arrow-up"></i>
                            </button>
                            <button class="form-builder__action-btn form-builder__action-btn--move"
                                :disabled="index === fields.length - 1" @click="moveField(index, 1)"
                                :title="$t('Move Down')">
                                <i class="bi bi-arrow-down"></i>
                            </button>
                            <button class="form-builder__action-btn form-builder__action-btn--delete"
                                @click="removeField(index)" :title="$t('Delete')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-builder__field-body">
                        <!-- Label -->
                        <div class="mb-3">
                            <label class="form-builder__label">{{ $t('Field Label') }}</label>
                            <input type="text" class="form-control form-builder__input" v-model="field.label"
                                :placeholder="$t('e.g. Do your windows have safety nets?')">
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label class="form-builder__label">{{ $t('Field Type') }}</label>
                            <select class="form-select form-builder__input" v-model="field.type">
                                <option value="text">{{ $t('fieldType.text') }}</option>
                                <option value="textarea">{{ $t('fieldType.textarea') }}</option>
                                <option value="select">{{ $t('fieldType.select') }}</option>
                                <option value="radio">{{ $t('fieldType.radio') }}</option>
                                <option value="checkbox">{{ $t('fieldType.checkbox') }}</option>
                            </select>
                        </div>

                        <!-- Options (for select / radio / checkbox) -->
                        <div v-if="hasOptions(field.type)" class="mb-3">
                            <label class="form-builder__label">{{ $t('Options') }}</label>
                            <div v-for="(opt, oi) in field.options" :key="oi" class="form-builder__option-row">
                                <input type="text"
                                    class="form-control form-control-sm form-builder__input form-builder__input--sm"
                                    v-model="field.options[oi]" :placeholder="`${$t('Option')} ${oi + 1}`">
                                <button class="btn btn-sm btn-outline-danger" @click="removeOption(field, oi)">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-secondary" @click="addOption(field)">
                                    <i class="bi bi-plus me-1"></i>{{ $t('Add Option') }}
                                </button>
                            </div>
                        </div>

                        <!-- Required Toggle -->
                        <div class="form-check form-switch">
                            <input class="form-check-input form-builder__checkbox" type="checkbox"
                                :id="`req-${field._key}`" v-model="field.required">
                            <label class="form-check-label form-builder__check-label" :for="`req-${field._key}`">
                                {{ $t('Required Field') }}
                            </label>
                        </div>
                    </div>
                </div>
            </TransitionGroup>

            <!-- Empty State -->
            <div v-if="fields.length === 0" class="form-builder__empty">
                <i class="bi bi-ui-checks-grid"></i>
                <p>{{ $t('No custom fields yet') }}</p>
                <p class="text-muted">{{ $t('Click button below to add your first field') }}</p>
            </div>
        </div>

        <button class="btn btn-outline-primary form-builder__add-btn" @click="addField">
            <i class="bi bi-plus-circle me-2"></i>{{ $t('Add Field') }}
        </button>
    </div>
</template>

<script setup lang="ts" name="AdoptionFormBuilder">
import { ref, watch } from 'vue'

interface SchemaField {
    _key: string
    label: string
    type: string
    required: boolean
    options: string[]
}

const props = defineProps<{
    modelValue: any[]
}>()

const emit = defineEmits(['update:modelValue'])

let keyCounter = 0
function genKey() {
    return `field_${Date.now()}_${keyCounter++}`
}

function toInternal(schema: any[]): SchemaField[] {
    return (schema || []).map(f => ({
        _key: genKey(),
        label: f.label || '',
        type: f.type || 'text',
        required: !!f.required,
        options: f.options || [],
    }))
}

function toExternal(fields: SchemaField[]): any[] {
    return fields.map(f => {
        const obj: any = {
            label: f.label,
            type: f.type,
            required: f.required,
        }
        if (hasOptions(f.type)) {
            obj.options = f.options.filter(o => o.trim() !== '')
        }
        return obj
    })
}

const fields = ref<SchemaField[]>(toInternal(props.modelValue))

watch(() => props.modelValue, (val) => {
    // Only sync from parent if structure truly changed (e.g. reset)
    if (JSON.stringify(toExternal(fields.value)) !== JSON.stringify(val)) {
        fields.value = toInternal(val)
    }
}, { deep: true })

watch(fields, () => {
    emit('update:modelValue', toExternal(fields.value))
}, { deep: true })

function hasOptions(type: string): boolean {
    return ['select', 'radio', 'checkbox'].includes(type)
}

function addField() {
    fields.value.push({
        _key: genKey(),
        label: '',
        type: 'text',
        required: false,
        options: [],
    })
}

function removeField(index: number) {
    fields.value.splice(index, 1)
}

function moveField(index: number, direction: number) {
    const target = index + direction
    if (target < 0 || target >= fields.value.length) return
    const temp = fields.value[index]
    fields.value[index] = fields.value[target]
    fields.value[target] = temp
}

function addOption(field: SchemaField) {
    field.options.push('')
}

function removeOption(field: SchemaField, index: number) {
    field.options.splice(index, 1)
}
</script>

<style scoped>
.form-builder__field-grid {
    display: flex;
    flex-direction: column;
    padding: 0.5rem;
    /* Give room for shadows to avoid clipping */
}

.form-builder__field-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
    margin-bottom: 1.5rem;
}

.form-builder__field-card:last-child {
    margin-bottom: 0;
}

.form-builder__field-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    border-color: #94a3b8;
}

.form-builder__field-header {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.75rem 1.25rem;
    background: #cbd5e1;
    border-bottom: 2px solid #94a3b8;
    border-radius: 11px 11px 0 0;
    /* Match card radius minus border */
}

.form-builder__field-drag {
    color: #a0aec0;
    cursor: grab;
    font-size: 1.2rem;
}

.form-builder__field-number {
    font-weight: 700;
    color: #3182ce;
    font-size: 0.85rem;
    background: #ebf8ff;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-builder__field-actions {
    margin-left: auto;
    display: flex;
    gap: 0.25rem;
}

.form-builder__action-btn {
    border: none;
    background: transparent;
    padding: 0.25rem 0.4rem;
    border-radius: 4px;
    color: #718096;
    cursor: pointer;
    transition: all 0.15s;
}

.form-builder__action-btn:hover {
    background: #e2e8f0;
    color: #2d3748;
}

.form-builder__action-btn--delete:hover {
    background: #fed7d7;
    color: #c53030;
}

.form-builder__action-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.form-builder__field-body {
    padding: 1rem;
}

.form-builder__option-row {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    align-items: center;
}

.form-builder__option-row .form-control-sm {
    flex: 1;
}

.form-builder__empty {
    text-align: center;
    padding: 3rem 1rem;
    color: #a0aec0;
}

.form-builder__empty i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.form-builder__label {
    display: block;
    font-weight: 700;
    color: #334155;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

/* Custom Brand Switches & Inputs */
.form-builder__checkbox:checked {
    background-color: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
}

.form-builder__checkbox:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.25rem rgba(44, 82, 130, 0.15);
}

.form-builder__add-btn {
    width: 100%;
    margin-top: 1.5rem;
    border: 2px dashed #cbd5e1;
    background: #f1f5f9;
    color: #475569;
    padding: 1rem;
    font-weight: 700;
    border-radius: 12px;
    transition: all 0.2s;
}

.form-builder__add-btn:hover {
    background: #e2e8f0;
    border-color: var(--color-denim-blue);
    color: var(--color-denim-blue);
}

.form-builder__input {
    border-radius: 8px;
    border-color: #e2e8f0;
}

.form-builder__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.1);
}
</style>
