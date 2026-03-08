<template>
    <Navbar />
    <Content>
        <template #content>
            <div class="not-found">
                <div class="not-found__card">
                    <!-- Animated Paw Icon -->
                    <div class="not-found__icon-wrapper">
                        <i class="bi bi-emoji-frown not-found__icon"></i>
                    </div>

                    <h2 class="not-found__code">404</h2>
                    <p class="not-found__title">{{ $t('Page Not Found') }}</p>
                    <p class="not-found__desc">{{ $t('404.Description') }}</p>

                    <div class="not-found__actions">
                        <RouterLink to="/adopt" class="btn btn-primary btn-lg not-found__btn">
                            <i class="bi bi-house-heart me-2"></i>{{ $t('404.GoHome') }}
                        </RouterLink>
                        <button @click="$router.back()"
                            class="btn btn-outline-secondary btn-lg not-found__btn not-found__btn--outline">
                            <i class="bi bi-arrow-left me-2"></i>{{ $t('404.GoBack') }}
                        </button>
                    </div>

                    <div class="not-found__paws">
                        <i class="bi bi-suit-heart-fill not-found__paw not-found__paw--1"></i>
                        <i class="bi bi-suit-heart-fill not-found__paw not-found__paw--2"></i>
                        <i class="bi bi-suit-heart-fill not-found__paw not-found__paw--3"></i>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="NotFound">
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.not-found {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    padding: 2rem;
}

.not-found__card {
    text-align: center;
    max-width: 480px;
    position: relative;
}

.not-found__icon-wrapper {
    margin-bottom: 1.5rem;
}

.not-found__icon {
    font-size: 4rem;
    color: var(--color-denim-blue);
    opacity: 0.6;
    animation: float 3s ease-in-out infinite;
}

.not-found__code {
    font-size: 6rem;
    font-weight: 800;
    color: var(--color-denim-blue);
    line-height: 1;
    margin-bottom: 0.5rem;
    letter-spacing: -2px;
    opacity: 0.85;
}

.not-found__title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.75rem;
}

.not-found__desc {
    font-size: 1.05rem;
    color: #718096;
    line-height: 1.7;
    margin-bottom: 2rem;
}

.not-found__actions {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    flex-wrap: wrap;
}

.not-found__btn {
    border-radius: 50px;
    padding: 0.6rem 1.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.not-found__btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44, 82, 130, 0.25);
}

.not-found__btn--outline {
    border-color: #cbd5e0;
    color: #4a5568;
}

.not-found__btn--outline:hover {
    background-color: var(--color-fog-gray);
    border-color: #a0aec0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Floating hearts decoration */
.not-found__paws {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.not-found__paw {
    position: absolute;
    color: var(--color-denim-blue);
    opacity: 0.08;
}

.not-found__paw--1 {
    font-size: 2rem;
    top: 10%;
    left: 5%;
    animation: float 4s ease-in-out infinite;
}

.not-found__paw--2 {
    font-size: 1.5rem;
    top: 20%;
    right: 8%;
    animation: float 5s ease-in-out infinite 1s;
}

.not-found__paw--3 {
    font-size: 1.75rem;
    bottom: 15%;
    left: 12%;
    animation: float 4.5s ease-in-out infinite 0.5s;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-10px);
    }
}

@media (max-width: 576px) {
    .not-found__code {
        font-size: 4.5rem;
    }

    .not-found__title {
        font-size: 1.25rem;
    }

    .not-found__actions {
        flex-direction: column;
    }
}
</style>
