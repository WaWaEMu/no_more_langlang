<template>
    <Navbar />
    <div>
        <RouterLink to="/adopt/add">登記送養</RouterLink>
    </div>
</template>

<script setup lang="ts" name="Adopt">
    import Navbar from '@/components/Navbar.vue'
    import { RouterLink } from 'vue-router';
</script>
