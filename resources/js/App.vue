<script setup lang="ts">
import { onBeforeMount } from 'vue';

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
    <RouterView></RouterView>
</template>