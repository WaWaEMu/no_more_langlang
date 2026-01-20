<template>
    <Navbar />
    <Content :title="$t('Website Concept')">
        <template #content>
            <div class="about__content">
                <!-- About Me Section -->
                <div class="about__section mb-5">
                    <h3 class="about__section-title">
                        <i class="bi bi-person-heart me-2"></i>{{ $t('About Me') }}
                    </h3>
                    <div class="about__card">
                        <p class="about__text">
                            {{ $t('Hi! I am a software engineer...') }}
                        </p>
                        <p class="about__text">
                            {{ $t('I chose to use my skills to help strays...') }}
                        </p>
                        <p class="about__text mb-0">
                            {{ $t('This is why I developed "No More Langlang".') }}
                        </p>
                    </div>
                </div>

                <!-- Tech & Collaboration Section -->
                <!-- <div class="about__section mb-5">
                    <h3 class="about__section-title">
                        <i class="bi bi-code-slash me-2"></i>技術與協作
                    </h3>
                    <div class="about__card">
                        <p class="about__text">
                            諾摩浪浪採用 <strong>Laravel + Vue 3</strong> 開發，是我持續精進技術的個人作品。
                        </p>
                        <p class="about__text">
                            如果你也是軟體工程師，對這個專案有興趣，歡迎一起協作開發！無論是前端、後端，還是有其他想法，都歡迎聯繫我～
                        </p>
                        <p class="about__text mb-0">
                            讓我們一起用程式碼，為浪浪們盡一份心力。🐾
                        </p>
                    </div>
                </div> -->

                <!-- Future Plans Section -->
                <div class="about__section mb-5">
                    <h3 class="about__section-title">
                        <i class="bi bi-rocket-takeoff me-2"></i>{{ $t('Future Plans') }}
                    </h3>
                    <div class="about__card">
                        <p class="about__text">
                            {{ $t('I will continue to maintain this site...') }}
                        </p>
                        <p class="about__text mb-0">
                            <span v-html="$t('I plan to open a free API...').replace('免費的 API', '<strong>免費的 API</strong>')"></span>
                        </p>
                        <!-- <p class="about__text mb-0">
                            希望透過科技的力量，串連起每一份愛心。
                        </p> -->
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="about__section">
                    <h3 class="about__section-title">
                        <i class="bi bi-chat-dots me-2"></i>{{ $t('Contact Me') }}
                    </h3>
                    <div class="about__card about__card--contact">
                        <p class="about__text">
                            {{ $t('Feel free to add my LINE...') }}
                        </p>
                        <div class="about__contact">
                            <i class="bi bi-line me-2"></i>
                            <span class="about__contact-label">LINE ID:</span>
                            <span class="about__contact-value">0938839635</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="About">
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
</script>

<style scoped>
.about__section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
    padding-left: 0.5rem;
    border-left: 4px solid #3182ce;
    display: flex;
    align-items: center;
}

.about__section-title i {
    color: #3182ce;
}

.about__card {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.about__card--contact {
    background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
    border-color: #9ae6b4;
}

.about__text {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #4a5568;
    margin-bottom: 1rem;
}

.about__contact {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    margin-top: 1rem;
    border: 1px solid #9ae6b4;
}

.about__contact i {
    font-size: 1.5rem;
    color: #00C300;
}

.about__contact-label {
    font-weight: 600;
    color: #2d3748;
    margin-right: 0.5rem;
}

.about__contact-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c5282;
    font-family: monospace;
    letter-spacing: 1px;
}

@media (max-width: 768px) {
    .about__card {
        padding: 1.5rem;
    }

    .about__text {
        font-size: 1rem;
    }
}
</style>
