<template>
    <div class="notification-dropdown" ref="notificationRef">
        <!-- Bell Icon with Badge -->
        <button @click="toggleDropdown" class="notification-dropdown__button">
            <i class="bi bi-bell-fill notification-dropdown__icon"></i>
            <span v-if="unreadCount > 0" class="notification-dropdown__badge">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown Menu -->
        <Transition name="dropdown">
            <div v-if="isOpen" class="notification-dropdown__menu">
                <!-- Header -->
                <div class="notification-dropdown__header">
                    <h6 class="notification-dropdown__title">通知</h6>
                    <button v-if="notifications.length > 0" @click="markAllAsRead"
                        class="notification-dropdown__mark-all">
                        全部標為已讀
                    </button>
                </div>

                <div class="notification-dropdown__divider"></div>

                <!-- Notification List -->
                <div class="notification-dropdown__list">
                    <div v-if="notifications.length === 0" class="notification-dropdown__empty">
                        <i class="bi bi-bell-slash"></i>
                        <p>目前沒有通知</p>
                    </div>

                    <div v-else v-for="notification in notifications" :key="notification.id"
                        @click="handleNotificationClick(notification)" class="notification-dropdown__item"
                        :class="{ 'notification-dropdown__item--unread': !notification.is_read }">
                        <div class="notification-dropdown__item-icon"
                            :class="`notification-dropdown__item-icon--${notification.type}`">
                            <i class="bi" :class="getIconClass(notification.type)"></i>
                        </div>

                        <div class="notification-dropdown__item-content">
                            <p class="notification-dropdown__item-message">{{ notification.message }}</p>
                            <span class="notification-dropdown__item-time">{{ formatTime(notification.created_at)
                                }}</span>
                        </div>

                        <div v-if="!notification.is_read" class="notification-dropdown__item-dot"></div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Notification {
    id: number
    type: 'adoption_application' | 'comment'
    message: string
    is_read: boolean
    created_at: string
    data?: {
        pet_id?: number
        application_id?: number
        comment_id?: number
    }
}

// State
const isOpen = ref(false)
const notificationRef = ref<HTMLElement | null>(null)

// Mock data for demonstration
const notifications = ref<Notification[]>([
    {
        id: 1,
        type: 'adoption_application',
        message: '小明申請領養您的寵物「小白」',
        is_read: false,
        created_at: new Date(Date.now() - 5 * 60 * 1000).toISOString(), // 5 minutes ago
        data: { pet_id: 1, application_id: 1 }
    },
    {
        id: 2,
        type: 'comment',
        message: '小華在「小黑」留言：請問這隻貓咪有結紮嗎？',
        is_read: false,
        created_at: new Date(Date.now() - 30 * 60 * 1000).toISOString(), // 30 minutes ago
        data: { pet_id: 2, comment_id: 5 }
    },
    {
        id: 3,
        type: 'adoption_application',
        message: '阿美申請領養您的寵物「花花」',
        is_read: true,
        created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(), // 2 hours ago
        data: { pet_id: 3, application_id: 2 }
    },
    {
        id: 4,
        type: 'comment',
        message: '大華在「小橘」留言：這隻狗狗真可愛！還在嗎？',
        is_read: true,
        created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(), // 1 day ago
        data: { pet_id: 4, comment_id: 8 }
    }
])

// Computed
const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.is_read).length
})

// Methods
function toggleDropdown() {
    isOpen.value = !isOpen.value
}

function closeDropdown() {
    isOpen.value = false
}

function getIconClass(type: string): string {
    return type === 'adoption_application' ? 'bi-heart-fill' : 'bi-chat-fill'
}

function formatTime(timestamp: string): string {
    const now = Date.now()
    const time = new Date(timestamp).getTime()
    const diff = now - time

    const minutes = Math.floor(diff / (1000 * 60))
    const hours = Math.floor(diff / (1000 * 60 * 60))
    const days = Math.floor(diff / (1000 * 60 * 60 * 24))

    if (minutes < 1) return '剛剛'
    if (minutes < 60) return `${minutes} 分鐘前`
    if (hours < 24) return `${hours} 小時前`
    if (days < 7) return `${days} 天前`
    return new Date(timestamp).toLocaleDateString('zh-TW')
}

function handleNotificationClick(notification: Notification) {
    // Mark as read
    notification.is_read = true

    // TODO: Navigate to related page based on notification type
    console.log('Clicked notification:', notification)

    closeDropdown()
}

function markAllAsRead() {
    notifications.value.forEach(n => {
        n.is_read = true
    })
}

// Click outside to close
function handleClickOutside(event: MouseEvent) {
    if (notificationRef.value && !notificationRef.value.contains(event.target as Node)) {
        closeDropdown()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.notification-dropdown {
    position: relative;
    display: flex;
    align-items: center;
}

/* Bell Button */
.notification-dropdown__button {
    position: relative;
    background: rgba(255, 255, 255, 0.5);
    border: 2px solid rgba(44, 82, 130, 0.3);
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #2c5282;

    &:hover {
        background: rgba(255, 255, 255, 0.8);
        border-color: #2c5282;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(44, 82, 130, 0.2);
    }
}

.notification-dropdown__icon {
    font-size: 1.25rem;
}

/* Badge */
.notification-dropdown__badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-radius: 12px;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0 5px;
    box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
}

/* Dropdown Menu */
.notification-dropdown__menu {
    position: absolute;
    top: calc(100% + 0.75rem);
    right: 0;
    width: 380px;
    max-height: 500px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    z-index: 1000;
    display: flex;
    flex-direction: column;
}

/* Header */
.notification-dropdown__header {
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #b8c5d6 0%, #d1d9e6 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-dropdown__title {
    margin: 0;
    font-weight: 700;
    color: #2c5282;
    font-size: 1.1rem;
}

.notification-dropdown__mark-all {
    background: none;
    border: none;
    color: #3182ce;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;

    &:hover {
        background: rgba(49, 130, 206, 0.1);
        color: #2c5282;
    }
}

.notification-dropdown__divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #e2e8f0, transparent);
    margin: 0;
}

/* Notification List */
.notification-dropdown__list {
    overflow-y: auto;
    max-height: 400px;
}

.notification-dropdown__empty {
    padding: 3rem 2rem;
    text-align: center;
    color: #9ca3af;

    i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }

    p {
        margin: 0;
        font-size: 0.95rem;
    }
}

/* Notification Item */
.notification-dropdown__item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f3f4f6;

    &:last-child {
        border-bottom: none;
    }

    &:hover {
        background: linear-gradient(to right, rgba(49, 130, 206, 0.05), rgba(44, 82, 130, 0.03));
    }
}

.notification-dropdown__item--unread {
    background: rgba(59, 130, 246, 0.05);

    &:hover {
        background: linear-gradient(to right, rgba(49, 130, 206, 0.1), rgba(44, 82, 130, 0.05));
    }
}

/* Item Icon */
.notification-dropdown__item-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.notification-dropdown__item-icon--adoption_application {
    background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(236, 72, 153, 0.15));
    color: #e11d48;
}

.notification-dropdown__item-icon--comment {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.15));
    color: #2563eb;
}

/* Item Content */
.notification-dropdown__item-content {
    flex: 1;
    min-width: 0;
}

.notification-dropdown__item-message {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.5;
}

.notification-dropdown__item--unread .notification-dropdown__item-message {
    font-weight: 600;
    color: #1f2937;
}

.notification-dropdown__item-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* Unread Dot */
.notification-dropdown__item-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #3b82f6;
    flex-shrink: 0;
    margin-top: 0.5rem;
}

/* Dropdown Animation */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.3s ease;
}

.dropdown-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}

/* Responsive */
@media (max-width: 768px) {
    .notification-dropdown__menu {
        width: 320px;
        right: -1rem;
    }

    .notification-dropdown__item {
        padding: 0.875rem 1rem;
    }
}
</style>
