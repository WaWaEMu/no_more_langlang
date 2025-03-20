<template>
    <h1 class="container fs-2 mx-auto my-5 pt-4 content__title">{{ title }}</h1>
    <div class="mx-auto d-flex content">
        <div class="w-75 py-5">
            <slot name="content"></slot>
        </div>
        <div class="w-25 py-5">
            <slot name="sidebar"></slot>
        </div>
    </div>
</template>

<script setup lang="ts" name="Content">
    defineProps(['title'])
</script>

<style scoped>
    .content {
        max-width: 1140px;
    }

    .content__title {
        max-width: 1140px;
    }
</style>
