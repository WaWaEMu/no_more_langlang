<template>
    <div class="pet-list">
        <RouterLink v-for="pet in petList" :key="pet.id" :to="`/adopt/${pet.id}`" class="pet-list__card">
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
    background: #f7fafc;
    color: #4a5568;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.pet-list__card--tag-highlight {
    background: #ebf8ff;
    color: #2c5282;
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
