<template>
    <Navbar />
    <Content :title="$t('Foster Venue Map')">
        <template #content>
            <div class="foster-venue">

                <!-- Subtitle -->
                <p class="foster-venue__subtitle">{{ $t('foster-venue.Hero.Sub') }}</p>

                <!-- Filter Bar: BMW-style icon chips -->
                <div class="foster-venue__filter-section">

                    <!-- Row 1: City & District Select -->
                    <div class="foster-venue__city-bar">
                        <span class="foster-venue__filter-label">地點</span>
                        <div class="d-flex gap-2">
                            <AppSelect 
                                v-model="filters.city" 
                                :options="cities" 
                                icon="bi bi-geo-alt"
                                placeholder="全部縣市"
                                clearLabel="全部縣市"
                                rounded="sharp"
                                @change="handleCityChange"
                            />
                            
                            <!-- District Select (Shows only when a city is selected) -->
                            <AppSelect 
                                v-if="filters.city"
                                v-model="filters.district" 
                                :options="districts" 
                                placeholder="全部區域"
                                clearLabel="全部區域"
                                rounded="sharp"
                                @change="fetchVenues"
                            />
                        </div>
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <span class="foster-venue__count" v-if="!loading">
                                共 <strong>{{ venues.length }}</strong> 間商家
                            </span>
                            <button class="foster-venue__reset-btn" @click="resetFilters"
                                v-if="filters.city || filters.district || filters.type || filters.pet_type">
                                <i class="bi bi-x-lg me-1"></i>{{ $t('Reset Filters') }}
                            </button>
                        </div>
                    </div>

                    <!-- Row 2: Type Chips -->
                    <div class="foster-venue__chip-row">
                        <button v-for="chip in typeChips" :key="chip.value" class="foster-venue__chip"
                            :class="{ 'foster-venue__chip--active': filters.type === chip.value }"
                            @click="selectType(chip.value)">
                            <i :class="chip.icon" class="foster-venue__chip-icon"></i>
                            <span class="foster-venue__chip-label">{{ $t(chip.label) }}</span>
                        </button>
                    </div>

                    <!-- Row 3: Pet Type Chips (Text Only) -->
                    <div class="foster-venue__chip-row foster-venue__chip-row--sm">
                        <button v-for="pet in petChips" :key="pet.value"
                            class="foster-venue__chip foster-venue__chip--sm"
                            :class="{ 'foster-venue__chip--active': filters.pet_type === pet.value }"
                            @click="selectPetType(pet.value)">
                            <span class="foster-venue__chip-label">{{ $t(pet.label) }}</span>
                        </button>
                    </div>

                </div>

                <!-- Main Grid -->
                <div class="row g-4">
                    <!-- Left: Card Grid (Primary) -->
                    <div class="col-lg-8">
                        <div class="foster-venue__card-grid" :class="{ 'foster-venue--loading': loading }">

                            <!-- Loading Skeleton -->
                            <template v-if="loading">
                                <div v-for="n in 6" :key="n"
                                    class="foster-venue-page__card foster-venue-page__card--skeleton">
                                    <div class="placeholder-glow">
                                        <span class="placeholder col-3 mb-2 d-block rounded-1"></span>
                                        <span class="placeholder col-8 mb-1 d-block rounded-1"></span>
                                        <span class="placeholder col-12 d-block rounded-1"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty State -->
                            <div v-else-if="venues.length === 0" class="foster-venue__empty">
                                <i class="bi bi-map foster-venue__empty-icon"></i>
                                <p class="foster-venue__empty-text">{{ $t('No venues found') }}</p>
                            </div>

                            <!-- Venue Cards (2-column grid) -->
                            <template v-else>
                                <div v-for="venue in venues" :key="venue.id" class="foster-venue-page__card"
                                    :class="{ 'foster-venue-page__card--active': selectedVenue?.id === venue.id }"
                                    @click="handleVenueSelect(venue)">
                                    <div class="foster-venue-page__card-type">
                                        <span class="foster-venue-page__type-badge"
                                            :class="`foster-venue-page__type-badge--${venue.type}`">
                                            <i :class="typeIcon(venue.type)" class="me-1"></i>{{
                                                $t(`venue.type.${venue.type}`) }}
                                        </span>
                                    </div>

                                    <h6 class="foster-venue-page__card-name">{{ venue.name }}</h6>
                                    <p class="foster-venue-page__card-address">
                                        <i class="bi bi-geo-alt me-1"></i>{{ venue.address }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                        <div class="d-flex gap-1 flex-wrap">
                                            <span v-for="pType in venue.pet_types" :key="pType"
                                                class="foster-venue-page__pet-badge">
                                                {{ $t(`venue.pet_type.${pType}`) }}
                                            </span>
                                        </div>
                                        <a v-if="venue.website_url" :href="venue.website_url" target="_blank"
                                            rel="noopener noreferrer" class="foster-venue-page__website-btn"
                                            @click.stop>
                                            <i class="bi bi-arrow-up-right-square"></i>
                                        </a>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>

                    <!-- Right: Map (Auxiliary) -->
                    <div class="col-lg-4">
                        <div class="foster-venue__map-container">
                            <FosterVenueMap :venues="venues" @select="handleVenueSelect" />
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </Content>
</template>

<script setup lang="ts" name="FosterVenueIndex">
import Navbar from '@/components/Navbar.vue'
import Content from '@/components/Content.vue'
import FosterVenueMap from '@/components/foster-venue/FosterVenueMap.vue'
import { onMounted, ref, computed } from 'vue'
import { useFosterVenueStore } from '@/stores/fosterVenue'
import { storeToRefs } from 'pinia'
import { trans } from 'laravel-vue-i18n'
import { FosterVenueInter } from '@/types/fosterVenue'
import { TYPE_CHIPS, PET_CHIPS } from '@/../data/fosterVenues'
import { areas } from '@/../data/areas'
import { getTypeIcon } from '@/utils/fosterVenue'
import AppSelect from '@/components/common/AppSelect.vue'

const $t = trans
const store = useFosterVenueStore()
const { venues, loading, filters } = storeToRefs(store)
const { fetchVenues, resetFilters } = store

const selectedVenue = ref<FosterVenueInter | null>(null)

// Extract flat list of cities from areas object
const cities = Object.values(areas).flatMap(region => Object.keys(region))

// Dynamically compute districts based on selected city
const districts = computed(() => {
    if (!filters.value.city) return []
    for (const region in areas) {
        if (areas[region][filters.value.city]) {
            return areas[region][filters.value.city]
        }
    }
    return []
})
const typeChips = TYPE_CHIPS
const petChips = PET_CHIPS
const typeIcon = getTypeIcon

function handleCityChange() {
    filters.value.district = ''
    fetchVenues()
}

function selectType(value: string) {
    filters.value.type = value
    fetchVenues()
}

function selectPetType(value: string) {
    filters.value.pet_type = value
    fetchVenues()
}

function handleVenueSelect(venue: FosterVenueInter) {
    selectedVenue.value = venue
}

onMounted(async () => {
    await fetchVenues()
})
</script>

<style scoped>
/* =========================================
   Page Base
   ========================================= */
.foster-venue__subtitle {
    font-size: 0.95rem;
    color: #94a3b8;
    letter-spacing: 0.01em;
    margin-bottom: 1.5rem;
}

/* =========================================
   Filter Section
   ========================================= */
.foster-venue__filter-section {
    border: 1.5px solid #94a3b8;
    border-radius: 3px;
    background: #fff;
    margin-bottom: 2.5rem;
    position: relative;
    z-index: 10;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
}

/* City bar */
.foster-venue__city-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.85rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.foster-venue__filter-label {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #94a3b8;
    white-space: nowrap;
}

.foster-venue__city-icon {
    color: #94a3b8;
    font-size: 0.9rem;
}

.foster-venue__count {
    font-size: 0.85rem;
    color: #94a3b8;
    white-space: nowrap;
}

.foster-venue__reset-btn {
    display: inline-flex;
    align-items: center;
    background: none;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 0.25rem 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    color: #94a3b8;
    cursor: pointer;
    transition: color 0.15s, border-color 0.15s;
    white-space: nowrap;
}

.foster-venue__reset-btn:hover {
    color: #1a202c;
    border-color: #94a3b8;
}

/* Chip rows */
.foster-venue__chip-row {
    display: flex;
    align-items: center;
    gap: 0;
    padding: 0 1rem;
    border-bottom: 1px solid #f1f5f9;
    overflow-x: auto;
}

.foster-venue__chip-row--sm {
    background: #fafafa;
}

.foster-venue__chip {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    padding: 0.9rem 1.25rem;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    transition: border-color 0.2s, color 0.2s;
    white-space: nowrap;
    color: #94a3b8;
    margin-bottom: -1px;
}

.foster-venue__chip:hover {
    color: #1a202c;
    border-bottom-color: #cbd5e1;
}

.foster-venue__chip--active {
    color: #1a202c !important;
    border-bottom-color: #1a202c !important;
}

.foster-venue__chip--sm {
    flex-direction: row;
    gap: 0.4rem;
    padding: 0.6rem 1.1rem;
}

.foster-venue__chip-icon {
    font-size: 1.35rem;
    line-height: 1;
}

.foster-venue__custom-icon {
    width: 1.35rem;
    height: 1.35rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.foster-venue__chip--sm .foster-venue__custom-icon {
    width: 1.1rem;
    height: 1.1rem;
}

.foster-venue__svg {
    width: 100%;
    height: 100%;
}

.foster-venue__chip-label {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.04em;
}

/* =========================================
   Card Grid (2-column)
   ========================================= */
.foster-venue__card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (max-width: 768px) {
    .foster-venue__card-grid {
        grid-template-columns: 1fr;
    }
}

/* =========================================
   Map Container (Auxiliary)
   ========================================= */
.foster-venue__map-container {
    aspect-ratio: 1 / 1;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    position: sticky;
    top: 5rem;
}

/* =========================================
   Venue Card
   ========================================= */
.foster-venue-page__card {
    background-color: #fff;
    border: 1.5px solid #f1f5f9;
    border-radius: 3px;
    padding: 1rem 1rem 0.85rem;
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
}

.foster-venue-page__card:hover {
    border-color: #bfdbfe;
    box-shadow: 0 4px 16px rgba(44, 82, 130, 0.08);
    transform: translateY(-1px);
}

.foster-venue-page__card--active {
    border-color: var(--color-denim-blue) !important;
    background-color: #ebf4ff;
    box-shadow: 0 4px 20px rgba(44, 82, 130, 0.15);
}

.foster-venue-page__card-type {
    margin-bottom: 0.4rem;
}

.foster-venue-page__card-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.25rem;
    line-height: 1.4;
}

.foster-venue-page__card-address {
    font-size: 0.85rem;
    color: #94a3b8;
    margin-bottom: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* =========================================
   Type Badges (Restored Colors)
   ========================================= */
.foster-venue-page__type-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    display: inline-block;
}

.foster-venue-page__type-badge--restaurant {
    background: #fef9c3;
    color: #a16207;
}

.foster-venue-page__type-badge--shelter {
    background: #dbeafe;
    color: #1d4ed8;
}

.foster-venue-page__type-badge--pet_store {
    background: #d1fae5;
    color: #065f46;
}

.foster-venue-page__type-badge--hair_salon {
    background: #fce7f3;
    color: #9d174d;
}

.foster-venue-page__type-badge--board_game {
    background: #ede9fe;
    color: #5b21b6;
}

.foster-venue-page__type-badge--vet_clinic {
    background: #fee2e2;
    color: #991b1b;
}

/* =========================================
   Pet Badge & Website Link (Restored)
   ========================================= */
.foster-venue-page__pet-badge {
    font-size: 0.75rem;
    background-color: #f0f9ff;
    color: #0369a1;
    border: 1px solid #bae6fd;
    border-radius: 20px;
    padding: 1px 7px;
    font-weight: 500;
}

.foster-venue-page__website-btn {
    color: #94a3b8;
    font-size: 1rem;
    text-decoration: none;
    flex-shrink: 0;
    transition: color 0.15s;
}

.foster-venue-page__website-btn:hover {
    color: var(--color-denim-blue);
}

/* =========================================
   Empty State
   ========================================= */
.foster-venue__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
}

.foster-venue__empty-icon {
    font-size: 2.5rem;
    color: #e2e8f0;
    margin-bottom: 0.75rem;
}

.foster-venue__empty-text {
    font-size: 0.85rem;
    color: #94a3b8;
    margin: 0;
}

/* =========================================
   Map Container
   (moved above, combined with card grid)
   ========================================= */

/* =========================================
   Loading
   ========================================= */
.foster-venue--loading {
    opacity: 0.5;
    pointer-events: none;
}
</style>
