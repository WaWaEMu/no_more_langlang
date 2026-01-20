<template>
    <Navbar />
    <Content :title="$t('New Adoption')">
        <template v-slot:content>
            <ApplyForm />
        </template>
    </Content>
</template>

<script setup lang="ts" name="AdoptCreate">
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import ApplyForm from '@/components/adopt/ApplyForm.vue';
import { trans } from 'laravel-vue-i18n'

const $t = trans
</script>
