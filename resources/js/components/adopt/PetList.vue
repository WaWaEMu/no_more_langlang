<template>
    <div class="pet-list">
        <div v-for="pet in petList" :key="pet.id" class="pet-list__card">
            <div class="pet-list__card--image-wrapper">
                <img :src="`/storage/${pet.images[0].path}`" :alt="pet.name" class="pet-list__card--image">
                <div class="pet-list__card--badge">
                    {{ pet.type }}
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
                    <span class="pet-list__card--tag" :class="{ 'pet-list__card--tag-highlight': pet.is_neuter }">
                        {{ pet.is_neuter ? '已結紮' : '未結紮' }}
                    </span>
                </div>

                <div class="pet-list__card--footer">
                    <div class="pet-list__card--user">
                        <span class="pet-list__card--label">發文者</span>
                        <RouterLink :to="`/user/profile/${pet.user.id}`" class="pet-list__card--user-link">
                            {{ pet.user.name }}
                        </RouterLink>
                    </div>
                    <div class="pet-list__card--date">
                        {{ formatDate(pet.created_at) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'

defineProps<{
    petList: any[]
}>()

function formatDate(dateStr: string) {
    return dateStr ? dateStr.split('T')[0] : ''
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
    box-shadow: 0 2px 12px rgba(99, 102, 241, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e0e7ff;
    display: flex;
    flex-direction: column;
}

.pet-list__card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.15);
    border-color: #818cf8;
}

.pet-list__card--image-wrapper {
    position: relative;
    padding-top: 100%;
    /* 1:1 Aspect Ratio (Square) */
    overflow: hidden;
    background-color: #f3f4f6;
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

.pet-list__card:hover .pet-list__card--image {
    transform: scale(1.05);
}

.pet-list__card--badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(4px);
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #4f46e5;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
    color: #1f2937;
    margin: 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    width: 100%;
}

.pet-list__card--location {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
    white-space: nowrap;
    align-self: flex-end;
}

.pet-list__card--location i {
    color: #818cf8;
}

.pet-list__card--info {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.25rem;
    padding: 0.5rem 0;
    border-top: 1px solid #e0e7ff;
    border-bottom: 1px solid #e0e7ff;
}

.pet-list__card--info-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.125rem;
}

.pet-list__card--label {
    font-size: 0.7rem;
    color: #6b7280;
}

.pet-list__card--value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    white-space: nowrap;
}

.pet-list__card--tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}

.pet-list__card--tag {
    background: #f3f4f6;
    color: #4b5563;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.pet-list__card--tag-highlight {
    background: #e0e7ff;
    color: #4f46e5;
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

.pet-list__card--user-link {
    color: #6366f1;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.pet-list__card--user-link:hover {
    color: #f59e0b;
}

.pet-list__card--date {
    color: #9ca3af;
    font-size: 0.8125rem;
}
</style>
