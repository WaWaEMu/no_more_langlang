<template>
    <div class="pet-list">
        <RouterLink v-for="pet in petList" :key="pet.id" :to="`/adopt/${pet.id}`" class="pet-list__card">
            <div class="pet-list__card--image-wrapper">
                <img v-if="pet.images && pet.images.length > 0" :src="`/storage/${pet.images[0].path}`" :alt="pet.name"
                    class="pet-list__card--image">
                <div v-else class="pet-list__card--image pet-list__card--no-image">
                    <i class="bi bi-image"></i>
                </div>
                <div class="pet-list__card--badge">
                    {{ $t(pet.type.toLowerCase()) }}
                </div>
                <div :class="['pet-list__card--status', `pet-list__card--status-${pet.status}`]">
                    {{ getStatusLabel(pet.status) }}
                </div>
                <button class="pet-list__card--favorite" @click.prevent.stop="handleToggleFavorite(pet.id)"
                    :class="{ 'active': isFavorite(pet.id) }">
                    <i class="bi" :class="isFavorite(pet.id) ? 'bi-heart-fill' : 'bi-heart'"></i>
                </button>
                <div v-if="editable" class="pet-list__card--edit-status" @click.prevent.stop>
                    <select :value="pet.status"
                        @change="handleStatusUpdate(pet.id, ($event.target as HTMLSelectElement).value)"
                        class="form-select form-select-sm pet-list__card--status-select">
                        <option v-for="option in statusOptions.items" :key="String(option.value)" :value="option.value"
                            :disabled="option.disabled">
                            {{ $t(option.label) }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="pet-list__card--content">
                <div class="pet-list__card--header">
                    <h3 class="pet-list__card--title">{{ pet.title }}</h3>
                    <div class="pet-list__card--location">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>{{ pet.city }} {{ pet.town }}</span>
                    </div>
                </div>

                <div class="pet-list__card--info">
                    <div class="pet-list__card--info-item">
                        <span class="pet-list__card--label">名字</span>
                        <span class="pet-list__card--value">{{ pet.name }}</span>
                    </div>
                    <div class="pet-list__card--info-item">
                        <span class="pet-list__card--label">性別</span>
                        <span class="pet-list__card--value">
                            <i
                                :class="['bi', pet.gender === 'male' ? 'bi-gender-male text-primary' : 'bi-gender-female text-danger']"></i>
                            {{ pet.gender === 'male' ? '男生' : '女生' }}
                        </span>
                    </div>
                    <div class="pet-list__card--info-item">
                        <span class="pet-list__card--label">年齡</span>
                        <span class="pet-list__card--value">{{ pet.age }} 歲</span>
                    </div>
                </div>

                <div class="pet-list__card--tags">
                    <span class="pet-list__card--tag">{{ pet.color }}</span>
                    <span class="pet-list__card--tag">{{ pet.fur_type }}</span>
                    <span class="pet-list__card--tag"
                        :class="pet.is_neuter ? 'pet-list__card--tag-neutered' : 'pet-list__card--tag-not-neutered'">
                        {{ pet.is_neuter ? '已結紮' : '未結紮' }}
                    </span>
                </div>

                <div class="pet-list__card--footer">
                    <div class="pet-list__card--user">
                        <span class="pet-list__card--label">發文者</span>
                        <span class="pet-list__card--user-name">{{ pet.user.name }}</span>
                    </div>
                    <div class="pet-list__card--date">
                        {{ formatDate(pet.created_at) }}
                    </div>
                </div>
            </div>
        </RouterLink>
    </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { useAdoptStore } from '@/stores/adopt'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'
import axios from 'axios'
import { statusOptions } from '@/../data/options'
import { trans } from 'laravel-vue-i18n'

const $t = trans

const adoptStore = useAdoptStore()
const authStore = useAuthStore()
const { toggleFavorite, isFavorite } = adoptStore

const props = defineProps<{
    petList: any[],
    editable?: boolean
}>()

function formatDate(dateStr: string) {
    return dateStr ? dateStr.split('T')[0] : ''
}

function getStatusLabel(status: string) {
    const labels: Record<string, string> = {
        'available': 'Status.Available',
        'paused': 'Status.Paused',
        'pending': 'Status.Pending',
        'adopted': 'Status.Adopted'
    }
    return labels[status] ? $t(labels[status]) : status
}

async function handleToggleFavorite(petId: number) {
    if (!await authStore.checkAuth('登入後即可收藏您心儀的浪浪！')) return

    if (isFavorite(petId)) {
        Swal.fire({
            title: '移除收藏？',
            text: "確定要將此寵物從收藏清單中移除嗎？",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '是的，移除！',
            cancelButtonText: '取消'
        }).then((result) => {
            if (result.isConfirmed) {
                toggleFavorite(petId)
            }
        })
    } else {
        toggleFavorite(petId)
    }
}

async function handleStatusUpdate(petId: number, newStatus: string) {
    try {
        const res = await axios.put(`/api/adopt/${petId}/status`, { status: newStatus })
        if (res.data.success) {
            const pet = props.petList.find((p: any) => p.id === petId)
            if (pet) pet.status = newStatus
            Swal.fire({
                icon: 'success',
                title: '狀態已更新',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            })
        }
    } catch (error) {
        console.error('Failed to update status:', error)
        Swal.fire({
            icon: 'error',
            title: '更新失敗',
            text: '請稍後再試'
        })
    }
}
</script>

<style scoped>
.pet-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
    padding: 1rem 0;
}

.pet-list__card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
}

.pet-list__card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 4px 10px rgba(0, 0, 0, 0.08);
    border-color: #3182ce;
}

.pet-list__card--image-wrapper {
    position: relative;
    padding-top: 100%;
    /* 1:1 Aspect Ratio (Square) */
    overflow: hidden;
    background-color: #f7fafc;
}

.pet-list__card--image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.pet-list__card--no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e2e8f0;
    color: #a0aec0;
    font-size: 3rem;
}

.pet-list__card:hover .pet-list__card--image {
    transform: scale(1.05);
}

.pet-list__card--badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #2c5282;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.pet-list__card--status {
    position: absolute;
    bottom: 0.75rem;
    right: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 550;
    color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 2;
}

.pet-list__card--status-available {
    background-color: #48bb78;
}

.pet-list__card--status-paused {
    background-color: #ed8936;
}

.pet-list__card--status-pending {
    background-color: #4299e1;
}

.pet-list__card--status-adopted {
    background-color: #805ad5;
}

.pet-list__card--favorite {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #a0aec0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 2;
}

.pet-list__card--favorite:hover {
    transform: scale(1.1);
    background: #fff;
    color: #e53e3e;
}

.pet-list__card--favorite.active {
    color: #e53e3e;
    background: #fff;
}

.pet-list__card--favorite i {
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pet-list__card--edit-status {
    position: absolute;
    bottom: 0.5rem;
    left: 0.5rem;
    right: 0.5rem;
    z-index: 3;
}

.pet-list__card--status-select {
    background-color: #ffffff;
    border: 1px solid #cbd5e0;
    font-size: 0.9rem;
    padding: 0.25rem 0.5rem;
}

.pet-list__card--content {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.pet-list__card--header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.pet-list__card--title {
    font-size: 1rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 100%;
}

.pet-list__card--location {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #718096;
    white-space: nowrap;
    align-self: flex-end;
}

.pet-list__card--location i {
    color: #3182ce;
}

.pet-list__card--info {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.25rem;
    padding: 0.5rem 0;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}

.pet-list__card--info-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.125rem;
}

.pet-list__card--label {
    font-size: 0.7rem;
    color: #718096;
}

.pet-list__card--value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2d3748;
    white-space: nowrap;
}

.pet-list__card--tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}

.pet-list__card--tag {
    background: var(--color-fog-gray, #E6E9EF);
    color: #718096;
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.pet-list__card--tag-neutered {
    background: rgba(44, 82, 130, 0.1);
    color: var(--color-denim-blue, #2c5282);
    font-weight: 600;
}

.pet-list__card--tag-not-neutered {
    background: transparent;
    color: #a0aec0;
    border: 1px dashed #cbd5e0;
}

.pet-list__card--footer {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
}

.pet-list__card--user {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pet-list__card--user-name {
    color: #3182ce;
    font-weight: 600;
}

.pet-list__card--date {
    color: #a0aec0;
    font-size: 0.8125rem;
}
</style>
