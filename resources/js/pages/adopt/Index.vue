<template>
    <Navbar />
    <Content :title="$t('Adopt')">
        <template #content>
            <div>
                <PetSearch />
                <PetList :pet-list="activePets" />
                <Pagination :current-page="currentPage" :last-page="lastPage" @change-page="fetchPets" />
            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="Adopt">
import Navbar from '@/components/Navbar.vue'
import PetSearch from '@/components/search/PetSearch.vue'
import Content from '@/components/Content.vue'
import PetList from '@/components/adopt/PetList.vue'
import Pagination from '@/components/common/Pagination.vue'
import { onMounted } from 'vue'
import { useAdoptStore } from '@/stores/adopt'
import { storeToRefs } from 'pinia'
import { trans } from 'laravel-vue-i18n'

const $t = trans
const adoptStore = useAdoptStore()
const { activePets, currentPage, lastPage } = storeToRefs(adoptStore)
const { fetchPets } = adoptStore

onMounted(async () => {
    await fetchPets()
})
</script>

<style></style>
