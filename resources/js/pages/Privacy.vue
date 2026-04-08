<template>
    <Navbar />
    <Content :title="$t('Privacy Policy')">
        <template #content>
            <div class="legal__content">
                <div class="legal__card shadow-sm">
                    <p class="legal__updated">{{ $t('Last Updated') }}：2026-04-08</p>

                    <div class="legal__section">
                        <h2 class="legal__section-title">{{ $t('privacy.Section1.Title') }}</h2>
                        <p>{{ $t('privacy.Section1.Body') }}</p>
                    </div>

                <div class="legal__section">
                    <h2 class="legal__section-title">{{ $t('privacy.Section2.Title') }}</h2>
                    <p>{{ $t('privacy.Section2.Body') }}</p>
                    <ul>
                        <li>{{ $t('privacy.Section2.Item1') }}</li>
                        <li>{{ $t('privacy.Section2.Item2') }}</li>
                        <li>{{ $t('privacy.Section2.Item3') }}</li>
                        <li>{{ $t('privacy.Section2.Item4') }}</li>
                    </ul>
                </div>

                <div class="legal__section">
                    <h2 class="legal__section-title">{{ $t('privacy.Section3.Title') }}</h2>
                    <p>{{ $t('privacy.Section3.Body') }}</p>
                    <ul>
                        <li>{{ $t('privacy.Section3.Item1') }}</li>
                        <li>{{ $t('privacy.Section3.Item2') }}</li>
                        <li>{{ $t('privacy.Section3.Item3') }}</li>
                    </ul>
                </div>

                <div class="legal__section">
                    <h2 class="legal__section-title">{{ $t('privacy.Section4.Title') }}</h2>
                    <p>{{ $t('privacy.Section4.Body') }}</p>
                </div>

                <div class="legal__section">
                    <h2 class="legal__section-title">{{ $t('privacy.Section5.Title') }}</h2>
                    <p>{{ $t('privacy.Section5.Body') }}</p>
                </div>

                    <div class="legal__section">
                        <h2 class="legal__section-title">{{ $t('privacy.Section6.Title') }}</h2>
                        <p>{{ $t('privacy.Section6.Body') }}</p>
                    </div>
                </div>

                <div class="legal__contact">
                    <i class="bi bi-envelope me-2"></i>
                    <RouterLink to="/about">{{ $t('Contact Us') }}</RouterLink>
                </div>
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Privacy">
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.legal__content {
    max-width: 800px;
}

.legal__updated {
    color: #718096;
    font-size: 0.85rem;
    margin-bottom: 2rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-fog-gray);
    display: inline-block;
}

.legal__card {
    background: #fff;
    padding: 3rem;
    border-radius: 1rem;
    border: 1px solid #e2e8f0;
    margin-bottom: 2rem;
}

.legal__section {
    margin-bottom: 2.5rem;
}

.legal__section:last-of-type {
    margin-bottom: 0;
}

.legal__section-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #1a202c;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.legal__section-title::before {
    content: "";
    display: inline-block;
    width: 4px;
    height: 1.25rem;
    background-color: var(--color-denim-blue);
    margin-right: 0.75rem;
    border-radius: 2px;
}

.legal__section p,
.legal__section li {
    color: #2d3748;
    line-height: 1.8;
    font-size: 1.05rem;
}

.legal__section ul {
    padding-left: 1.5rem;
    margin-top: 0.5rem;
}

.legal__section li {
    margin-bottom: 0.4rem;
}

.legal__contact {
    margin-top: 2rem;
    padding: 1rem 1.5rem;
    background: #ebf4ff;
    border-radius: 8px;
    border: 1px solid #bee3f8;
    color: var(--color-denim-blue);
    font-size: 0.95rem;
}

.legal__contact a {
    color: var(--color-denim-blue);
    font-weight: 600;
}
</style>
