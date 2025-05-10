<template>
    <Navbar />
    <div>
        <RouterLink to="/adopt/new">登記送養</RouterLink>
    </div>
</template>

<script setup lang="ts" name="AdoptIndex">
    import Navbar from '@/components/Navbar.vue'
    import { RouterLink } from 'vue-router';
</script>
