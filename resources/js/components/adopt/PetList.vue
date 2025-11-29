<template>
    <div class="container d-flex gap-5 mt-5">
        <div v-for="pet in petList" class="pet-list__pet border">
            <div>
                <img :src="`/storage/${pet.images[0].path}`" alt="" class="pet-list__img">
            </div>
            <div>
                <div class="ms-3">
                    <h3 class="fs-4 mt-3">{{ pet.title }}</h3>
                    <div>
                        <span>{{ pet.city }}</span>
                        <span>{{ pet.town }}</span>
                    </div>
                    <div class="pet-list__date me-2 my-1 text-end">
                        <span class="me-1">刊登日期</span>
                        <span>{{ formatDate(pet.created_at) }}</span>
                    </div>
                    <div class="pet-list__date me-2 my-1 text-end">
                        <span class="me-1">發文者</span>
                        <RouterLink :to="`/user/profile/${pet.user.id}`">{{ pet.user.name }}</RouterLink>
                    </div>
                </div>
                <div class="pet-list__profile">
                    <div class="d-flex">
                        <div class="pet-list__profile--left text-nowrap">
                            <div class="py-2">
                                <span class="ms-3 me-2 fw-bold">名字</span>
                                <span>{{ pet.name }}</span>
                            </div>
                            <div class="py-2">
                                <span class="ms-3 me-2 fw-bold">性別</span>
                                <span>{{ pet.gender === 'male' ? '男生' : '女生' }}</span>
                            </div>
                            <div class="py-2">
                                <span class="ms-3 me-2 fw-bold">大約年齡</span>
                                <span>{{ pet.age }} 歲</span>
                            </div>
                            <div class="py-2">
                                <span class="ms-3 me-2 fw-bold">結紮狀態</span>
                                <span>{{ pet.is_neuter ? '已結紮' : '未結紮' }}</span>
                            </div>
                        </div>
                        <div
                            class="pet-list__profile--right d-flex flex-column align-items-center justify-content-center pe-2">
                            <div class="fs-5">
                                <span>{{ pet.type }}</span>
                            </div>
                            <div class="mx-2">
                                <span>{{ pet.color }}</span>
                                <span> | </span>
                                <span>{{ pet.fur_type }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="py-2 mx-3">
                            <span class="me-2 fw-bold">可送養縣市</span>
                            <span v-for="(cityObj, index) in pet.sendable_cities" :key="index">
                                {{ cityObj.city }}
                                <span v-if="index < pet.sendable_cities.length - 1">、</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    petList: any
}>()

function formatDate(dateStr: string) {
    return dateStr ? dateStr.split('T')[0] : ''
}

</script>

<style scoped>
.pet-list__pet {
    border-radius: 2%;
    max-width: 250px;
}

.pet-list__img {
    border-radius: 5px 5px 0 0;
}

.pet-list__date {
    font-size: 0.8rem;
}

.pet-list__profile {
    border-top: 1px dotted #a8a8a8;
    font-size: 0.85rem;
}

.pet-list__profile--left {
    width: 57%;
    border-right: 1px dotted #a8a8a8;
}

.pet-list__profile--left div {
    border-bottom: 1px dotted #a8a8a8;
}

.pet-list__profile--right {
    width: 43%;
    border-bottom: 1px dotted #a8a8a8;
}
</style>
