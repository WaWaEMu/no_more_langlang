<template>
    <Navbar />
    <Content title="登記送養">
        <template v-slot:content>
            <ApplyForm />
        </template>
    </Content>
</template>

<script setup lang="ts" name="AdoptCreate">
    import Navbar from '@/components/Navbar.vue'
    import Content from '@/components/Content.vue'
    import ApplyForm from '@/components/adopt/ApplyForm.vue';
</script>
