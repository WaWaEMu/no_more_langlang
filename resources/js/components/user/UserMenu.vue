<template>
    <div class="user-menu">
        <!-- Loading State -->
        <div v-if="authLoading" class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">載入中...</span>
        </div>

        <!-- Not Logged In -->
        <div v-else-if="!isLoggedIn" class="user-menu__auth-buttons">
            <RouterLink to="/auth/login" class="user-menu__btn user-menu__btn--login">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                登入
            </RouterLink>
            <RouterLink to="/auth/register" class="user-menu__btn user-menu__btn--register">
                <i class="bi bi-person-plus me-2"></i>
                註冊
            </RouterLink>
        </div>

        <!-- Logged In -->
        <div v-else class="user-menu__wrapper" ref="userMenuRef">
            <button @click="toggleUserMenu" class="user-menu__button">
                <div class="user-menu__avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <span class="user-menu__name">{{ user?.name || '使用者' }}</span>
                <i class="bi bi-chevron-down user-menu__chevron"
                    :class="{ 'user-menu__chevron--open': isMenuOpen }"></i>
            </button>

            <!-- Dropdown Menu -->
            <Transition name="dropdown">
                <div v-if="isMenuOpen" class="user-menu__dropdown">
                    <div class="user-menu__dropdown-header">
                        <div class="user-menu__dropdown-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="user-menu__dropdown-info">
                            <div class="user-menu__dropdown-name">{{ user?.name }}</div>
                            <div class="user-menu__dropdown-email">{{ user?.email }}</div>
                        </div>
                    </div>

                    <div class="user-menu__dropdown-divider"></div>

                    <ul class="user-menu__dropdown-list">
                        <li>
                            <RouterLink :to="`/user/profile/${user?.id}`" class="user-menu__dropdown-item"
                                @click="closeMenu">
                                <i class="bi bi-person-fill"></i>
                                <span>個人資料</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/my-pets" class="user-menu__dropdown-item" @click="closeMenu">
                                <i class="bi bi-heart-fill"></i>
                                <span>我的送養</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/favorites" class="user-menu__dropdown-item" @click="closeMenu">
                                <i class="bi bi-bookmark-heart-fill"></i>
                                <span>收藏清單</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/settings" class="user-menu__dropdown-item" @click="closeMenu">
                                <i class="bi bi-gear-fill"></i>
                                <span>帳號設定</span>
                            </RouterLink>
                        </li>
                    </ul>

                    <div class="user-menu__dropdown-divider"></div>

                    <button @click="handleLogout" class="user-menu__dropdown-item user-menu__dropdown-item--logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>登出</span>
                    </button>
                </div>
            </Transition>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const router = useRouter()

// Auth state
const isLoggedIn = ref(false)
const authLoading = ref(true)
const user = ref<{ id: number; name: string; email: string } | null>(null)

// Menu state
const isMenuOpen = ref(false)
const userMenuRef = ref<HTMLElement | null>(null)

async function checkAuthStatus() {
    authLoading.value = true
    try {
        const response = await axios.get('/api/user')
        user.value = response.data
        isLoggedIn.value = true
    } catch (error) {
        isLoggedIn.value = false
        user.value = null
    } finally {
        authLoading.value = false
    }
}

function toggleUserMenu() {
    isMenuOpen.value = !isMenuOpen.value
}

function closeMenu() {
    isMenuOpen.value = false
}

async function handleLogout() {
    closeMenu()

    try {
        await axios.post('/logout')

        await Swal.fire({
            icon: 'success',
            title: '登出成功',
            text: '您已成功登出',
            timer: 1500,
            showConfirmButton: false
        })

        isLoggedIn.value = false
        user.value = null
        router.push('/adopt')
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: '登出失敗',
            text: '請稍後再試'
        })
    }
}

// Click outside to close menu
function handleClickOutside(event: MouseEvent) {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target as Node)) {
        closeMenu()
    }
}

onMounted(() => {
    checkAuthStatus()
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.user-menu {
    display: flex;
    align-items: center;
    padding: 1rem 0;
}

/* Auth Buttons (Not Logged In) */
.user-menu__auth-buttons {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.user-menu__btn {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.user-menu__btn--login {
    color: #2c5282;
    background: rgba(255, 255, 255, 0.5);
    border-color: rgba(44, 82, 130, 0.3);

    &:hover {
        background: rgba(255, 255, 255, 0.8);
        border-color: #2c5282;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(44, 82, 130, 0.2);
    }
}

.user-menu__btn--register {
    color: #fff;
    background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
    border-color: transparent;

    &:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(49, 130, 206, 0.4);
    }
}

/* User Button (Logged In) */
.user-menu__wrapper {
    position: relative;
}

.user-menu__button {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.5);
    border: 2px solid rgba(44, 82, 130, 0.3);
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #2c5282;
    font-weight: 600;

    &:hover {
        background: rgba(255, 255, 255, 0.8);
        border-color: #2c5282;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(44, 82, 130, 0.2);
    }
}

.user-menu__avatar {
    font-size: 1.75rem;
    line-height: 1;
    color: #3182ce;
}

.user-menu__name {
    font-size: 0.9rem;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-menu__chevron {
    font-size: 0.75rem;
    transition: transform 0.3s ease;
}

.user-menu__chevron--open {
    transform: rotate(180deg);
}

/* Dropdown Menu */
.user-menu__dropdown {
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    min-width: 280px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    z-index: 1000;
}

.user-menu__dropdown-header {
    padding: 1.25rem;
    background: linear-gradient(135deg, #b8c5d6 0%, #d1d9e6 100%);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-menu__dropdown-avatar {
    font-size: 2.5rem;
    color: #2c5282;
    line-height: 1;
}

.user-menu__dropdown-info {
    flex: 1;
    min-width: 0;
}

.user-menu__dropdown-name {
    font-weight: 700;
    color: #2c5282;
    font-size: 1rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-menu__dropdown-email {
    font-size: 0.8rem;
    color: #4a5568;
    margin-top: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-menu__dropdown-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #e2e8f0, transparent);
    margin: 0.5rem 0;
}

.user-menu__dropdown-list {
    list-style: none;
    padding: 0.5rem 0;
    margin: 0;
}

.user-menu__dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    color: #4a5568;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 0.9rem;

    i {
        font-size: 1.1rem;
        color: #3182ce;
        min-width: 1.25rem;
    }

    &:hover {
        background: linear-gradient(to right, rgba(49, 130, 206, 0.1), rgba(44, 82, 130, 0.05));
        color: #2c5282;
        padding-left: 1.5rem;
    }
}

.user-menu__dropdown-item--logout {
    margin-top: 0.5rem;
    color: #dc2626;
    font-weight: 600;

    i {
        color: #dc2626;
    }

    &:hover {
        background: linear-gradient(to right, rgba(220, 38, 38, 0.1), rgba(220, 38, 38, 0.05));
        color: #b91c1c;
    }
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
    .user-menu__name {
        display: none;
    }

    .user-menu__dropdown {
        right: -1rem;
    }
}
</style>
