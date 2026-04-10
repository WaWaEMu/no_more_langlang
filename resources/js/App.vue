<script setup lang="ts">
import { onBeforeMount } from 'vue';
import SiteFooter from '@/components/Footer.vue'

onBeforeMount(() => {
    const ua = navigator.userAgent;
    const isLine = /Line/i.test(ua);
    const url = new URL(window.location.href);

    // If in LINE and the external browser parameter is not yet present
    if (isLine && !url.searchParams.has('openExternalBrowser')) {
        url.searchParams.set('openExternalBrowser', '1');
        window.location.replace(url.toString());
    }
});
</script>

<template>
    <div class="app-layout">
        <div class="app-content">
            <RouterView></RouterView>
        </div>
        <SiteFooter />
    </div>
</template>

<style>
.app-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.app-content {
    flex: 1;
    width: 100%;
}
</style>
