<template>
    <div class="foster-venue-map">
        <div id="map" class="foster-venue-map__container"></div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, watch, onBeforeUnmount } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { FosterVenueInter } from '@/types/fosterVenue'
import { trans } from 'laravel-vue-i18n'

const props = defineProps<{
    venues: FosterVenueInter[]
}>()

const emit = defineEmits<{
    (e: 'select', venue: FosterVenueInter): void
}>()

let map: L.Map | null = null
let markerGroup: L.LayerGroup | null = null

// Fix Leaflet default icon issues in build tools
const defaultIcon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
})

/**
 * Initialize the map
 */
const initMap = () => {
    // Default center to Taiwan
    map = L.map('map', {
        center: [23.6, 121.0],
        zoom: 7,
        zoomControl: false // We can add it to the top-right later
    })

    // Add Tile Layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map)

    // Add Zoom Control to the top-right
    L.control.zoom({ position: 'topright' }).addTo(map)

    // Group for markers to allow easy clearing
    markerGroup = L.layerGroup().addTo(map)
}

/**
 * Update markers on the map based on props.venues
 */
const updateMarkers = () => {
    if (!map || !markerGroup) return

    // Clear existing markers
    markerGroup.clearLayers()

    props.venues.forEach(venue => {
        if (venue.latitude && venue.longitude) {
            const marker = L.marker([venue.latitude, venue.longitude], { icon: defaultIcon })
                .bindPopup(`
                    <div class="foster-venue-map__popup">
                        <h6 class="fw-bold mb-1">${venue.name}</h6>
                        <p class="small text-muted mb-1">${venue.address}</p>
                        <div class="d-flex gap-1 flex-wrap mt-1">
                            ${venue.type.map(t => `<span class="badge bg-primary">${trans(`venue.type.${t}`)}</span>`).join('')}
                        </div>
                    </div>
                `)
            
            marker.on('click', () => {
                emit('select', venue)
            })

            markerGroup?.addLayer(marker)
        }
    })

    // Auto fit bounds if there are venues
    if (props.venues.length > 0 && markerGroup.getLayers().length > 0) {
        const bounds = L.featureGroup(markerGroup.getLayers() as L.Marker[]).getBounds()
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 })
    }
}

onMounted(() => {
    initMap()
    updateMarkers()
})

// Watch for venue data changes
watch(() => props.venues, () => {
    updateMarkers()
}, { deep: true })

onBeforeUnmount(() => {
    if (map) {
        map.remove()
    }
})
</script>

<style scoped lang="css">
.foster-venue-map {
    width: 100%;
    height: 100%;
    position: relative;
}

.foster-venue-map__container {
    width: 100%;
    height: 100%;
    border-radius: 8px; /* Following your RULES.md: border-radius: 8px */
    z-index: 1;
}

/* Customizing the popup style to match your UI */
:deep(.leaflet-popup-content-wrapper) {
    border-radius: 8px;
    padding: 4px;
}

.foster-venue-map__popup {
    min-width: 150px;
}
</style>
