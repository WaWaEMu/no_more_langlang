<template>
    <Navbar />
    <div class="landing">
        <!-- Section 1: Hero -->
        <header class="landing__hero py-5">
            <div class="container py-lg-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6 text-center text-lg-start">
                        <h1 class="landing__hero-title mb-4">{{ $t('landing.Hero.Title') }}</h1>
                        <p class="landing__hero-sub mb-3">{{ $t('landing.Hero.Sub') }}</p>
                        <ul class="landing__hero-list list-unstyled mb-5 text-start d-inline-block">
                            <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i>{{
                                $t('landing.Hero.Point1') }}</li>
                            <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i>{{
                                $t('landing.Hero.Point2') }}</li>
                            <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i>{{
                                $t('landing.Hero.Point3') }}</li>
                        </ul>
                        <div class="d-flex gap-2 gap-md-3 flex-wrap justify-content-center justify-content-lg-start">
                            <button @click="handleStart"
                                class="btn btn-primary btn-lg px-3 px-md-4 py-2 py-md-3 fw-bold shadow landing__hero-btn">
                                <i class="bi bi-rocket-takeoff me-2"></i>{{ $t('landing.Hero.CTA.Start') }}
                            </button>
                            <RouterLink to="/adopt"
                                class="btn btn-outline-primary btn-lg px-3 px-md-4 py-2 py-md-3 fw-bold landing__hero-btn">
                                {{ $t('landing.Hero.CTA.Adopt') }}
                            </RouterLink>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-flex justify-content-center">
                        <!-- UI Mockup Illustration - Visualizing the 'Tool' aspect -->
                        <div class="landing__mockup-wrap">
                            <div class="landing__mockup shadow-lg rounded-4 p-4 bg-white">
                                <div
                                    class="mockup-header d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle rounded-circle p-2 me-3 d-flex align-items-center justify-content-center overflow-hidden"
                                            style="width: 48px; height: 48px; background-color: #ebf4ff;">
                                            <img :src="'/images/landing/huahua_cover.jpg'" alt="Hua Hua"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="text-start">
                                            <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{
                                                $t('landing.Mockup.CaseTitle') }}
                                            </div>
                                            <div class="text-muted x-small text-start">{{
                                                $t('landing.Mockup.LastUpdate')
                                            }}</div>
                                        </div>
                                    </div>
                                    <span
                                        class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2"
                                        style="background-color: #f0fff4; border-color: #c6f6d5; color: #38a169;">
                                        <i class="bi bi-check-circle-fill me-1"></i> {{ $t('landing.Mockup.Status') }}
                                    </span>
                                </div>

                                <div class="mockup-body text-start">
                                    <!-- Tracking Info (Reflecting implement tracking schedule) -->
                                    <div class="mockup-section mb-4 p-3 rounded-3"
                                        style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="x-small fw-bold text-secondary text-uppercase mb-1">{{
                                            $t('landing.Mockup.NextTracking') }}</div>
                                        <div class="fw-bold text-primary">
                                            <i class="bi bi-calendar-event me-2"></i>{{
                                                $t('landing.Mockup.TrackingDate') }}
                                        </div>
                                    </div>

                                    <div class="mockup-diary mb-4">
                                        <div class="x-small fw-bold text-secondary text-uppercase mb-2">{{
                                            $t('landing.Mockup.DiaryTitle') }}
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <div class="mockup-img rounded-3 border overflow-hidden">
                                                    <img :src="'/images/landing/diary_1.jpg'" alt="Diary 1"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="mockup-img rounded-3 border overflow-hidden">
                                                    <img :src="'/images/landing/diary_2.jpg'" alt="Diary 2"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="mockup-img rounded-3 border overflow-hidden">
                                                    <img :src="'/images/landing/diary_3.jpg'" alt="Diary 3"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mockup-footer d-flex flex-wrap gap-2">
                                        <div class="mockup-pill"
                                            style="font-size: 0.75rem; background: #ebf4ff; padding: 6px 12px; border-radius: 20px; border: 1px solid #bee3f8; color: #2c5282;">
                                            <i class="bi bi-bell-fill me-1"></i> {{
                                                $t('landing.Mockup.Pill1') }}
                                        </div>
                                        <div class="mockup-pill"
                                            style="font-size: 0.75rem; background: #f8fafc; padding: 6px 12px; border-radius: 20px; border: 1px solid #e2e8f0; color: #64748b;">
                                            <i class="bi bi-hdd-fill me-1"></i> {{
                                                $t('landing.Mockup.Pill2') }}
                                        </div>
                                        <div class="mockup-pill"
                                            style="font-size: 0.75rem; background: #f0fff4; padding: 6px 12px; border-radius: 20px; border: 1px solid #c6f6d5; color: #2f855a;">
                                            <i class="bi bi-file-earmark-text-fill me-1 text-success"></i> {{
                                                $t('landing.Mockup.Pill3')
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Section 2: Features -->
        <section class="landing__features py-5 bg-white border-top border-bottom">
            <div class="container py-5">
                <div class="row g-4 gx-lg-5">
                    <div class="col-md-6 col-lg-3 text-center text-md-start">
                        <div class="landing__feature-icon mb-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $t('landing.Feature1.Title') }}</h4>
                        <p class="text-muted small">{{ $t('landing.Feature1.Sub') }}</p>
                    </div>
                    <div class="col-md-6 col-lg-3 text-center text-md-start">
                        <div class="landing__feature-icon mb-3 text-success">
                            <i class="bi bi-camera"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $t('landing.Feature2.Title') }}</h4>
                        <p class="text-muted small">{{ $t('landing.Feature2.Sub') }}</p>
                    </div>
                    <div class="col-md-6 col-lg-3 text-center text-md-start">
                        <div class="landing__feature-icon mb-3 text-info">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $t('landing.Feature3.Title') }}</h4>
                        <p class="text-muted small">{{ $t('landing.Feature3.Sub') }}</p>
                    </div>
                    <div class="col-md-6 col-lg-3 text-center text-md-start">
                        <div class="landing__feature-icon mb-3 text-primary">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $t('landing.Feature4.Title') }}</h4>
                        <p class="text-muted small">{{ $t('landing.Feature4.Sub') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Steps -->
        <section class="landing__steps py-5">
            <div class="container py-5">
                <h2 class="text-center fw-bold mb-5">{{ $t('landing.Step.Title') }}</h2>
                <div class="row text-center mt-4">
                    <div class="col-md-4 landing__step-item mb-4 mb-md-0">
                        <div class="landing__step-num mb-3">1</div>
                        <h5 class="fw-bold">{{ $t('landing.Step1') }}</h5>
                    </div>
                    <div class="col-md-4 landing__step-item mb-4 mb-md-0">
                        <div class="landing__step-num mb-3">2</div>
                        <h5 class="fw-bold">{{ $t('landing.Step2') }}</h5>
                    </div>
                    <div class="col-md-4 landing__step-item">
                        <div class="landing__step-num mb-3">3</div>
                        <h5 class="fw-bold">{{ $t('landing.Step3') }}</h5>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 4: Bottom CTA -->
        <section class="landing__cta py-5 bg-primary text-white text-center">
            <div class="container py-4">
                <h3 class="mb-4">{{ $t('landing.CTA.Bottom') }}</h3>
                <button @click="handleStart" class="btn btn-light btn-lg px-5 py-3 fw-bold text-primary shadow">
                    {{ $t('landing.Hero.CTA.Start') }}
                </button>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import Navbar from '@/components/Navbar.vue'
import { RouterLink, useRouter } from 'vue-router'
import { trans } from 'laravel-vue-i18n'
import { useAuthStore } from '@/stores/auth'

const $t = trans
const router = useRouter()
const authStore = useAuthStore()

async function handleStart() {
    if (await authStore.checkAuth($t('Login to manage cases'))) {
        router.push('/user/adoptions')
    }
}
</script>

<style scoped>
.landing {
    overflow-x: hidden;
}

.landing__hero {
    background-color: var(--color-fog-gray);
    position: relative;
    z-index: 1;
}

.landing__hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    line-height: 1.2;
    color: var(--color-denim-blue-dark);
}

.landing__hero-sub {
    font-size: 1.25rem;
    color: #4b5563;
    line-height: 1.8;
}

/* Mockup UI */
.landing__mockup-wrap {
    position: relative;
    padding: 20px;
    width: 100%;
    max-width: 520px;
}

.landing__mockup {
    transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
    transition: transform 0.5s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.landing__mockup:hover {
    transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
}

@media (max-width: 991px) {
    .landing__mockup-wrap {
        max-width: 420px;
    }

    .landing__mockup {
        transform: none !important;
    }
}

.mockup-title {
    font-weight: 700;
    color: #1a202c;
}

.mockup-img {
    aspect-ratio: 1/1;
    background-image: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
}

.mockup-stat {
    font-size: 0.65rem;
    background: #f1f5f9;
    padding: 4px 8px;
    border-radius: 4px;
    color: #475569;
}

.x-small {
    font-size: 0.7rem;
}

/* Features */
.landing__feature-icon {
    font-size: 2.5rem;
    color: var(--color-denim-blue);
}

/* CTA Buttons — use brand colors (global app.css handles .btn-primary) */
.landing__hero :deep(.btn-primary) {
    background-color: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
}

.landing__hero :deep(.btn-primary:hover) {
    background-color: var(--color-denim-blue-dark);
    border-color: var(--color-denim-blue-dark);
}

.landing__hero :deep(.btn-outline-primary) {
    color: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
}

.landing__hero :deep(.btn-outline-primary:hover) {
    background-color: var(--color-denim-blue);
    border-color: var(--color-denim-blue);
    color: #fff;
}

.landing__hero-btn {
    font-size: 0.95rem;
}

@media (min-width: 768px) {
    .landing__hero-btn {
        font-size: 1.25rem;
    }
}

/* Steps */
.landing__step-num {
    width: 32px;
    height: 32px;
    background: var(--color-denim-blue);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-weight: 800;
    font-size: 1rem;
    box-shadow: 0 4px 10px rgba(44, 82, 130, 0.3);
}

@media (min-width: 768px) {
    .landing__step-num {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
}

.landing__step-item {
    position: relative;
}

@media (min-width: 768px) {
    .landing__step-item:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 25px;
        left: 60%;
        width: 80%;
        height: 2px;
        background: #cbd5e1;
        z-index: -1;
    }
}
</style>
