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
                        <div class="notification-dropdown__item-icon" :class="getIconTypeClass(notification.type)">
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
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

interface Notification {
    id: number
    type: 'new_adoption_application' | 'new_comment' | 'comment_reply' | 'adoption_application_status'
    message: string
    is_read: boolean
    read_at?: string
    created_at: string
    data?: {
        pet_id?: number
        pet_name?: string
        application_id?: number
        comment_id?: number
        parent_comment_id?: number
    }
}

const router = useRouter()

// State
const isOpen = ref(false)
const notificationRef = ref<HTMLElement | null>(null)
const notifications = ref<Notification[]>([])
const isLoading = ref(false)
const unreadCountOnly = ref(0) // For showing badge before loading full list

// Computed
const unreadCount = computed(() => {
    // If notifications are loaded, use actual count
    if (notifications.value.length > 0) {
        return notifications.value.filter(n => !n.is_read).length
    }
    // Otherwise use the count-only value
    return unreadCountOnly.value
})

// Methods
async function fetchUnreadCount() {
    try {
        const response = await axios.get('/api/notifications/unread-count')
        unreadCountOnly.value = response.data.count
    } catch (error) {
        console.error('Failed to fetch unread count:', error)
    }
}

async function fetchNotifications() {
    try {
        isLoading.value = true
        const response = await axios.get('/api/notifications')
        notifications.value = response.data
        // Update count based on actual data
        unreadCountOnly.value = response.data.filter((n: Notification) => !n.is_read).length
    } catch (error) {
        console.error('Failed to fetch notifications:', error)
    } finally {
        isLoading.value = false
    }
}

function toggleDropdown() {
    isOpen.value = !isOpen.value
    if (isOpen.value && notifications.value.length === 0) {
        fetchNotifications()
    }
}

function closeDropdown() {
    isOpen.value = false
}

function getIconClass(type: string): string {
    if (type === 'new_adoption_application' || type === 'adoption_application_status') {
        return 'bi-heart-fill'
    }
    return 'bi-chat-fill' // for new_comment and comment_reply
}

function getIconTypeClass(type: string): string {
    if (type === 'new_adoption_application' || type === 'adoption_application_status') {
        return 'notification-dropdown__item-icon--adoption_application'
    }
    return 'notification-dropdown__item-icon--comment'
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

async function handleNotificationClick(notification: Notification) {
    // Mark as read if not already
    if (!notification.is_read) {
        await markAsRead(notification.id)
    }

    closeDropdown()

    // Navigate based on notification type
    if ((notification.type === 'new_adoption_application' || notification.type === 'adoption_application_status') && notification.data?.application_id) {
        // Navigate to applications page with highlight
        await router.push({
            path: '/user/applications',
            query: { highlight: notification.data.application_id }
        })
    } else if (notification.data?.pet_id) {
        const petId = notification.data.pet_id
        const commentId = notification.data.comment_id

        // Navigate to pet detail page
        await router.push(`/adopt/${petId}`)

        // If there's a comment_id, scroll to the comment after a delay
        if (commentId) {
            await nextTick()
            // Wait for page load and comments to render
            setTimeout(() => {
                const commentElement = document.getElementById(`comment-${commentId}`)
                if (commentElement) {
                    commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
                    // Add highlight effect
                    commentElement.style.transition = 'background-color 0.3s ease'
                    commentElement.style.backgroundColor = 'rgba(66, 91, 118, 0.15)'
                    setTimeout(() => {
                        commentElement.style.backgroundColor = ''
                    }, 2000)
                }
            }, 500)
        }
    }
}

async function markAsRead(notificationId: number) {
    try {
        await axios.post(`/api/notifications/${notificationId}/read`)

        // Update local state
        const notification = notifications.value.find(n => n.id === notificationId)
        if (notification) {
            notification.is_read = true
            notification.read_at = new Date().toISOString()
        }

        // Update count
        unreadCountOnly.value = Math.max(0, unreadCountOnly.value - 1)
    } catch (error) {
        console.error('Failed to mark notification as read:', error)
    }
}

async function markAllAsRead() {
    try {
        await axios.post('/api/notifications/read-all')

        // Update local state
        notifications.value.forEach(n => {
            n.is_read = true
            n.read_at = new Date().toISOString()
        })

        // Reset count to 0
        unreadCountOnly.value = 0
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
    }
}

// Click outside to close
function handleClickOutside(event: MouseEvent) {
    if (notificationRef.value && !notificationRef.value.contains(event.target as Node)) {
        closeDropdown()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    // Fetch unread count immediately to show badge
    fetchUnreadCount()
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
    .notification-dropdown {
        position: static;
    }

    .notification-dropdown__menu {
        width: auto;
        left: 1rem;
        right: 1rem;
        max-width: none;
        margin-top: 0.5rem;
    }

    .notification-dropdown__item {
        padding: 0.875rem 1rem;
    }
}

@media (max-width: 375px) {
    .notification-dropdown__menu {
        left: 0.5rem;
        right: 0.5rem;
        width: auto;
    }

    .notification-dropdown__header {
        padding: 0.875rem 1rem;
    }

    .notification-dropdown__title {
        font-size: 1rem;
    }

    .notification-dropdown__mark-all {
        font-size: 0.8rem;
    }

    .notification-dropdown__item {
        padding: 0.75rem 0.875rem;
        gap: 0.75rem;
    }

    .notification-dropdown__item-icon {
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }

    .notification-dropdown__item-message {
        font-size: 0.85rem;
    }
}
</style>
