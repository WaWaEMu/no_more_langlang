<template>
    <Navbar />
    <Content :title="$t('Website Concept')">
        <template #content>
            <div class="about__content">
                <!-- Website Concept Section -->
                <div class="about__section mb-5">
                    <h3 class="about__section-title">
                        <i class="bi bi-lightbulb me-2"></i>{{ $t('About.Concept.Title') }}
                    </h3>
                    <div class="about__card">
                        <p class="about__text" v-html="$t('About.Concept.Intro')"></p>
                        <p class="about__text">{{ $t('About.Concept.List') }}</p>
                        <ul class="about__list">
                            <li>{{ $t('About.Concept.Feature1') }}</li>
                            <li>{{ $t('About.Concept.Feature2') }}</li>
                            <li>{{ $t('About.Concept.Feature3') }}</li>
                            <li>{{ $t('About.Concept.Feature4') }}</li>
                        </ul>
                        <p class="about__text mb-0">
                            {{ $t('About.Concept.Footer') }}
                        </p>
                    </div>
                </div>

                <!-- Why Section -->
                <div class="about__section mb-5">
                    <h3 class="about__section-title">
                        <i class="bi bi-question-circle me-2"></i>{{ $t('About.Why.Title') }}
                    </h3>
                    <div class="about__card">
                        <p class="about__text mb-0">
                            {{ $t('About.Why.Content') }}
                        </p>
                    </div>
                </div>

                <!-- Who is it for Section -->
                <div class="about__section mb-5">
                    <h3 class="about__section-title">
                        <i class="bi bi-people me-2"></i>{{ $t('About.Who.Title') }}
                    </h3>
                    <div class="about__card">
                        <p class="about__text">{{ $t('About.Who.List') }}</p>
                        <ul class="about__list">
                            <li>{{ $t('About.Who.Item1') }}</li>
                            <li>{{ $t('About.Who.Item2') }}</li>
                            <li>{{ $t('About.Who.Item3') }}</li>
                            <li>{{ $t('About.Who.Item4') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Tech & Collaboration Section -->
                <div class="about__section mb-5">
                    <h3 class="about__section-title">
                        <i class="bi bi-code-slash me-2"></i>{{ $t('About.Tech.Title') }}
                    </h3>
                    <div class="about__card">
                        <p class="about__text" v-html="$t('About.Tech.Content')"></p>
                        <p class="about__text mb-0">
                            {{ $t('About.Tech.Invite') }}
                        </p>
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

.about__list {
    list-style: none;
    padding-left: 1rem;
    margin-bottom: 1.5rem;
}

.about__list li {
    position: relative;
    padding-left: 1.5rem;
    margin-bottom: 0.5rem;
    color: #4a5568;
    font-size: 1.05rem;
}

.about__list li::before {
    content: "•";
    position: absolute;
    left: 0;
    color: #3182ce;
    font-weight: bold;
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
