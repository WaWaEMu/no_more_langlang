<template>
    <nav v-if="lastPage > 1" aria-label="Page navigation"
        class="pagination-container d-flex justify-content-center mt-5">
        <ul class="pagination">
            <!-- Previous Page -->
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" aria-label="Previous" @click.prevent="changePage(currentPage - 1)">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <!-- Page Numbers -->
            <li v-for="page in displayedPages" :key="page" class="page-item"
                :class="{ active: page === currentPage, disabled: page === '...' }">
                <a v-if="page !== '...'" class="page-link" href="#" @click.prevent="changePage(page as number)">
                    {{ page }}
                </a>
                <span v-else class="page-link">...</span>
            </li>

            <!-- Next Page -->
            <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <a class="page-link" href="#" aria-label="Next" @click.prevent="changePage(currentPage + 1)">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    currentPage: number
    lastPage: number
}>()

const emit = defineEmits<{
    (e: 'change-page', page: number): void
}>()

const displayedPages = computed(() => {
    const pages: (number | string)[] = []
    const range = 2 // Number of pages to show before and after current page

    for (let i = 1; i <= props.lastPage; i++) {
        if (
            i === 1 ||
            i === props.lastPage ||
            (i >= props.currentPage - range && i <= props.currentPage + range)
        ) {
            pages.push(i)
        } else if (
            (i === props.currentPage - range - 1 && i > 1) ||
            (i === props.currentPage + range + 1 && i < props.lastPage)
        ) {
            pages.push('...')
        }
    }

    return pages
})

function changePage(page: number) {
    if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
        emit('change-page', page)
    }
}
</script>

<style scoped>
.pagination-container {
    margin-bottom: 3rem;
}

.pagination {
    gap: 0.5rem;
}

.page-link {
    border-radius: 8px !important;
    border: 1px solid #e2e8f0;
    color: #4a5568;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.page-link:hover {
    background-color: #edf2f7;
    color: #2d3748;
    border-color: #cbd5e0;
}

.page-item.active .page-link {
    background-color: #3182ce;
    border-color: #3182ce;
    color: white;
    box-shadow: 0 4px 6px rgba(49, 130, 206, 0.3);
}

.page-item.disabled .page-link {
    background-color: #f7fafc;
    color: #a0aec0;
    cursor: not-allowed;
}

.page-item:first-child .page-link,
.page-item:last-child .page-link {
    border-radius: 8px !important;
}
</style>
