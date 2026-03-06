<template>
    <Navbar />
    <Content :title="$t('Adoption Templates')">
        <template v-slot:content>
            <div class="template-mgr">
                <!-- Header Actions -->
                <div class="template-mgr__header">
                    <div class="template-mgr__header-info">
                        <p class="template-mgr__desc">
                            {{ $t('form_template.description') }}
                        </p>
                    </div>
                    <button class="btn btn-primary" @click="openCreateModal">
                        <i class="bi bi-plus-lg me-2"></i>{{ $t('Create Template') }}
                    </button>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Template List -->
                <div v-else-if="templates.length > 0" class="template-mgr__list">
                    <div v-for="tpl in templates" :key="tpl.id" class="template-mgr__card">
                        <div class="template-mgr__card-header">
                            <div class="template-mgr__card-title">
                                <h5>{{ tpl.name }}</h5>
                                <span v-if="tpl.is_default" class="badge bg-primary">
                                    {{ $t('Default') }}
                                </span>
                            </div>
                            <div class="template-mgr__card-meta">
                                <span class="text-muted">
                                    {{ tpl.schema?.length || 0 }} {{ $t('fields') }}
                                </span>
                            </div>
                        </div>

                        <!-- Field Preview -->
                        <div class="template-mgr__card-preview">
                            <div v-for="(field, fi) in (tpl.schema || []).slice(0, 3)" :key="fi"
                                class="template-mgr__preview-field">
                                <i :class="fieldTypeIcon(field.type)"></i>
                                <span>{{ field.label }}</span>
                                <span v-if="field.required" class="template-mgr__required-badge">*</span>
                            </div>
                            <div v-if="(tpl.schema || []).length > 3" class="template-mgr__preview-more">
                                +{{ tpl.schema.length - 3 }} {{ $t('more fields') }}
                            </div>
                        </div>

                        <div class="template-mgr__card-actions">
                            <button class="btn btn-sm btn-outline-primary" @click="openEditModal(tpl)">
                                <i class="bi bi-pencil me-1"></i>{{ $t('Edit') }}
                            </button>
                            <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(tpl)">
                                <i class="bi bi-trash me-1"></i>{{ $t('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="template-mgr__empty">
                    <div class="template-mgr__empty-box">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <h5>{{ $t('No templates yet') }}</h5>
                        <p>{{ $t('form_template.empty_hint') }}</p>
                    </div>
                </div>

                <!-- Create / Edit Modal -->
                <Teleport to="body">
                    <div v-if="showModal" class="template-mgr__overlay" @click.self="closeModal">
                        <div class="template-mgr__modal">
                            <div class="template-mgr__modal-header">
                                <h4>{{ isEditing ? $t('Edit Template') : $t('Create Template') }}</h4>
                                <button class="btn-close" @click="closeModal"></button>
                            </div>

                            <div class="template-mgr__modal-body">
                                <!-- Template Name -->
                                <div class="mb-3">
                                    <label class="template-mgr__label">{{ $t('Template Name') }}</label>
                                    <input type="text" class="form-control template-mgr__input" v-model="form.name"
                                        :placeholder="$t('e.g. Standard Adoption Form')">
                                </div>

                                <!-- Is Default -->
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input template-mgr__switch" type="checkbox" id="isDefault"
                                        v-model="form.is_default">
                                    <label class="form-check-label template-mgr__switch-label" for="isDefault">
                                        {{ $t('Set as default template') }}
                                    </label>
                                </div>

                                <!-- Base Fields Reference -->
                                <div class="template-mgr__base-fields">
                                    <div class="template-mgr__base-header" @click="showBaseFields = !showBaseFields">
                                        <div class="template-mgr__base-title">
                                            <i class="bi bi-lock-fill"></i>
                                            <span>{{ $t('base_fields.title') }}</span>
                                        </div>
                                        <i class="bi" :class="showBaseFields ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                    </div>
                                    <Transition name="collapse">
                                        <div v-if="showBaseFields" class="template-mgr__base-list">
                                            <div v-for="field in baseFields" :key="field.key"
                                                class="template-mgr__base-item">
                                                <i :class="field.icon"></i>
                                                <span>{{ field.label }}</span>
                                                <span v-if="field.required" class="template-mgr__base-required">*</span>
                                                <span class="template-mgr__base-type">{{ field.typeName }}</span>
                                            </div>
                                        </div>
                                    </Transition>
                                </div>

                                <!-- Custom Fields Builder -->
                                <div class="mb-3">
                                    <div class="template-mgr__custom-header">
                                        <label class="template-mgr__label mb-0">{{ $t('custom_fields.title') }}</label>
                                        <small class="text-muted">{{ $t('custom_fields.hint') }}</small>
                                    </div>
                                    <AdoptionFormBuilder v-model="form.schema" />
                                </div>
                            </div>

                            <div class="template-mgr__modal-footer">
                                <button class="btn btn-secondary" @click="closeModal">
                                    {{ $t('Cancel') }}
                                </button>
                                <button class="btn btn-primary" @click="saveTemplate" :disabled="saving">
                                    <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ isEditing ? $t('Save Changes') : $t('Create') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </Teleport>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="AdoptionTemplateManager">
import { ref, reactive, onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import AdoptionFormBuilder from '@/components/adopt/AdoptionFormBuilder.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

interface FormTemplate {
    id: number
    name: string
    schema: any[]
    is_default: boolean
    created_at: string
    updated_at: string
}

const templates = ref<FormTemplate[]>([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref<number | null>(null)
const showBaseFields = ref(false)

const form = reactive({
    name: '',
    schema: [] as any[],
    is_default: false,
})

// The 7 fixed base fields that every application always has
const baseFields = [
    { key: 'name', label: '暱稱', icon: 'bi bi-person', typeName: '文字', required: true },
    { key: 'phone', label: '電話', icon: 'bi bi-telephone', typeName: '文字', required: true },
    { key: 'line_id', label: 'Line ID', icon: 'bi bi-chat-dots', typeName: '文字', required: false },
    { key: 'housing', label: '居住環境', icon: 'bi bi-house-door', typeName: '下拉選單', required: true },
    { key: 'experience', label: '養寵經驗', icon: 'bi bi-star', typeName: '下拉選單', required: true },
    { key: 'family', label: '家人/室友是否同意', icon: 'bi bi-people', typeName: '單選', required: true },
    { key: 'message', label: '自我介紹與動機', icon: 'bi bi-chat-square-text', typeName: '多行文字', required: true },
]

// Suggested custom fields to pre-populate for new templates
const defaultSuggestedSchema = [
    { label: '家中是否有樓梯（考量動物年齡偏大）', type: 'radio', required: false, options: ['有', '沒有'] },
    { label: '目前家中是否有其他寵物？（若有，請簡述個性、社會化程度）', type: 'textarea', required: true },
]

onMounted(() => {
    fetchTemplates()
})

async function fetchTemplates() {
    loading.value = true
    try {
        const res = await axios.get('/api/user/adoption-templates')
        templates.value = res.data.data
    } catch {
        Swal.fire({ icon: 'error', title: '載入失敗', text: '請稍後再試' })
    } finally {
        loading.value = false
    }
}

function openCreateModal() {
    isEditing.value = false
    editingId.value = null
    form.name = ''
    form.schema = JSON.parse(JSON.stringify(defaultSuggestedSchema))
    form.is_default = false
    showBaseFields.value = false
    showModal.value = true
}

function openEditModal(tpl: FormTemplate) {
    isEditing.value = true
    editingId.value = tpl.id
    form.name = tpl.name
    form.schema = JSON.parse(JSON.stringify(tpl.schema || []))
    form.is_default = tpl.is_default
    showBaseFields.value = false
    showModal.value = true
}

function closeModal() {
    showModal.value = false
}

async function saveTemplate() {
    if (!form.name.trim()) {
        Swal.fire({ icon: 'warning', title: '請輸入範本名稱' })
        return
    }

    saving.value = true
    try {
        if (isEditing.value && editingId.value) {
            await axios.put(`/api/user/adoption-templates/${editingId.value}`, {
                name: form.name,
                schema: form.schema,
                is_default: form.is_default,
            })
            await Swal.fire({
                icon: 'success', title: '更新成功', timer: 1500, showConfirmButton: false
            })
        } else {
            await axios.post('/api/user/adoption-templates', {
                name: form.name,
                schema: form.schema,
                is_default: form.is_default,
            })
            await Swal.fire({
                icon: 'success', title: '建立成功', timer: 1500, showConfirmButton: false
            })
        }
        closeModal()
        await fetchTemplates()
    } catch (err: any) {
        const msg = err?.response?.data?.message || '操作失敗，請稍後再試'
        Swal.fire({ icon: 'error', title: '錯誤', text: msg })
    } finally {
        saving.value = false
    }
}

async function confirmDelete(tpl: FormTemplate) {
    const result = await Swal.fire({
        icon: 'warning',
        title: '確認刪除',
        text: `確定要刪除範本「${tpl.name}」嗎？使用此範本的寵物將不再關聯任何範本。`,
        showCancelButton: true,
        confirmButtonText: '刪除',
        cancelButtonText: '取消',
        confirmButtonColor: '#e53e3e',
    })

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/user/adoption-templates/${tpl.id}`)
            await Swal.fire({
                icon: 'success', title: '已刪除', timer: 1500, showConfirmButton: false
            })
            await fetchTemplates()
        } catch {
            Swal.fire({ icon: 'error', title: '刪除失敗', text: '請稍後再試' })
        }
    }
}

function fieldTypeIcon(type: string): string {
    const icons: Record<string, string> = {
        text: 'bi bi-input-cursor-text',
        textarea: 'bi bi-textarea-t',
        select: 'bi bi-menu-button-wide',
        radio: 'bi bi-record-circle',
        checkbox: 'bi bi-check2-square',
    }
    return icons[type] || 'bi bi-question-circle'
}
</script>

<style scoped>
.template-mgr__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 2rem;
    gap: 1.5rem;
}

.template-mgr__header-info {
    flex: 1;
}

.template-mgr__desc {
    color: #64748b;
    margin: 0;
    line-height: 1.6;
}

.template-mgr__list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.25rem;
}

.template-mgr__card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.15s;
}

.template-mgr__card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.template-mgr__card-header {
    padding: 1.25rem 1.25rem 0.75rem;
}

.template-mgr__card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.template-mgr__card-title h5 {
    margin: 0;
    font-weight: 700;
    color: #2d3748;
    font-size: 1.05rem;
}

.template-mgr__card-meta {
    margin-top: 0.25rem;
    font-size: 0.85rem;
}

.template-mgr__card-preview {
    padding: 0 1.25rem;
    margin-bottom: 0.5rem;
}

.template-mgr__preview-field {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.75rem;
    background: #f1f5f9;
    border-radius: 6px;
    margin-bottom: 0.35rem;
    font-size: 0.88rem;
    color: #4a5568;
}

.template-mgr__preview-field i {
    color: #3182ce;
    font-size: 0.9rem;
}

.template-mgr__required-badge {
    color: #e53e3e;
    font-weight: 700;
    margin-left: auto;
}

.template-mgr__preview-more {
    font-size: 0.82rem;
    color: #a0aec0;
    padding: 0.25rem 0.75rem;
}

.template-mgr__card-actions {
    display: flex;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem 1.25rem;
}

/* Empty State */
.template-mgr__empty {
    padding: 2rem 0;
}

.template-mgr__empty-box {
    text-align: center;
    padding: 5rem 2rem;
    color: #94a3b8;
    background: #f1f5f9;
    border: 2px dashed #e2e8f0;
    border-radius: 16px;
}

.template-mgr__empty i {
    font-size: 3.5rem;
    margin-bottom: 1.25rem;
    display: block;
    opacity: 0.5;
}

.template-mgr__empty h5 {
    color: #64748b;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.template-mgr__empty p {
    font-size: 0.95rem;
    margin: 0;
}

/* Modal */
.template-mgr__overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
    padding: 1rem;
}

.template-mgr__modal {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 640px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.template-mgr__modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.template-mgr__modal-header h4 {
    margin: 0;
    font-weight: 700;
    color: #2d3748;
}

.template-mgr__modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}

.template-mgr__modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
}

/* Base Fields Reference */
.template-mgr__base-fields {
    background: #cbd5e1;
    border: 1px solid #94a3b8;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.template-mgr__base-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.25rem;
    cursor: pointer;
    user-select: none;
    transition: background 0.15s;
    background: #cbd5e1;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.template-mgr__base-header:hover {
    background: #94a3b8;
}

.template-mgr__base-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: #334155;
}

.template-mgr__base-title i {
    color: #475569;
    font-size: 0.9rem;
}

.template-mgr__base-list {
    padding: 0 1rem 0.75rem;
}

.template-mgr__base-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.6rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    margin-bottom: 0.3rem;
    font-size: 0.85rem;
    color: #64748b;
}

.template-mgr__base-item i {
    color: #94a3b8;
    font-size: 0.8rem;
    width: 16px;
    text-align: center;
}

.template-mgr__base-required {
    color: #e53e3e;
    font-weight: 700;
}

.template-mgr__base-type {
    margin-left: auto;
    font-size: 0.75rem;
    color: #a0aec0;
    background: #f1f5f9;
    padding: 0.1rem 0.5rem;
    border-radius: 4px;
}

/* Custom Fields Header */
.template-mgr__custom-header {
    display: flex;
    align-items: baseline;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

/* Collapse Transition */
.collapse-enter-active,
.collapse-leave-active {
    transition: all 0.25s ease;
    overflow: hidden;
}

.collapse-enter-from,
.collapse-leave-to {
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.collapse-enter-to,
.collapse-leave-from {
    opacity: 1;
    max-height: 500px;
}

/* Form Styles */
.template-mgr__label {
    display: block;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
}

.template-mgr__input {
    border-radius: 8px;
    padding: 0.6rem 1rem;
    border-color: #e2e8f0;
}

.template-mgr__input:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.1);
}

/* Custom Brand Switches */
.template-mgr__switch:checked {
    background-color: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
}

.template-mgr__switch:focus {
    border-color: var(--color-denim-blue);
    box-shadow: 0 0 0 0.25rem rgba(44, 82, 130, 0.15);
}

.btn-primary {
    background: #3182ce;
    border-color: #3182ce;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
}

.btn-primary:hover {
    background: #2c5282;
    border-color: #2c5282;
}

@media (max-width: 768px) {
    .template-mgr__list {
        grid-template-columns: 1fr;
    }

    .template-mgr__header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }

    .template-mgr__header .btn {
        width: 100%;
    }

    .template-mgr__modal {
        max-height: 90vh;
    }
}
</style>
