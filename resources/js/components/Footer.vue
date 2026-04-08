<template>
    <footer class="site-footer">
        <div class="site-footer__inner">
            <div class="site-footer__brand">
                <RouterLink to="/" class="site-footer__logo">{{ $t('App Name') }}</RouterLink>
                <p class="site-footer__tagline">{{ $t('footer.Tagline') }}</p>
            </div>
            
            <div class="site-footer__right">
                <nav class="site-footer__links" aria-label="Footer navigation">
                    <RouterLink to="/about">{{ $t('Website Concept') }}</RouterLink>
                    <RouterLink to="/terms">{{ $t('Terms of Service') }}</RouterLink>
                    <RouterLink to="/privacy">{{ $t('Privacy Policy') }}</RouterLink>
                </nav>
                <p class="site-footer__copyright">
                    © {{ new Date().getFullYear() }} {{ $t('App Name') }}．{{ $t('footer.Rights') }}
                </p>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts" name="SiteFooter">
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.site-footer {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: #e2e8f0;
    padding: 2rem 1rem;
    margin-top: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.site-footer__inner {
    max-width: 1140px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.site-footer__brand {
    text-align: left;
}

.site-footer__logo {
    font-size: 1.4rem;
    font-weight: 900;
    color: #e2e8f0;
    text-decoration: none;
    letter-spacing: -0.01em;
    transition: color 0.2s ease;
}

.site-footer__logo:hover {
    color: #fff;
}

.site-footer__tagline {
    font-size: 0.85rem;
    color: #cbd5e0;
    margin: 0.25rem 0 0;
}

.site-footer__right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.75rem;
}

.site-footer__links {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.site-footer__links a {
    color: #e2e8f0;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.site-footer__links a:hover {
    color: #fff;
    text-decoration: underline;
}

.site-footer__copyright {
    font-size: 0.8rem;
    color: #a0aec0;
    margin: 0;
}

@media (max-width: 768px) {
    .site-footer__inner {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }

    .site-footer__brand {
        text-align: center;
    }

    .site-footer__right {
        align-items: center;
    }

    .site-footer__links {
        justify-content: center;
        gap: 1rem;
    }
}
</style>