<template>
    <h1 class="fs-2 mx-auto mt-5 pt-4 px-4 content__title">{{ title }}</h1>
    <div class="mx-auto d-flex px-4 content">
        <div class="py-5 w-100">
            <slot name="content"></slot>
        </div>
        <!-- <div class="w-25 py-5">
            <slot name="sidebar"></slot>
        </div> -->
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
    color: #2d3748;
    font-weight: 700;
}
</style>
