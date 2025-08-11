<template>
  <div ref="mapContainer" class="h-full w-full rounded-lg" />
</template>

<script setup lang="ts">
import L from 'leaflet'
import type { LatLngTuple } from 'leaflet'
import { onMounted, onUnmounted, ref, watch } from 'vue'

interface Props {
  center?: LatLngTuple
  zoom?: number
  height?: string
  markers?: Array<{ lat: number; lng: number; popup?: string }>
  className?: string
}

const props = withDefaults(defineProps<Props>(), {
  center: () => [51.505, -0.09], // London as default
  zoom: 13,
  height: '400px',
  markers: () => [],
  className: ''
})

const mapContainer = ref<HTMLDivElement>()
let map: L.Map | null = null
let markerGroup: L.LayerGroup | null = null

const initializeMap = () => {
  if (!mapContainer.value) return

  // Create map
  map = L.map(mapContainer.value).setView(props.center, props.zoom)

  // Add OpenStreetMap tiles
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map)

  // Initialize marker group
  markerGroup = L.layerGroup().addTo(map)

  // Add initial markers
  updateMarkers()
}

const updateMarkers = () => {
  if (!map || !markerGroup) return

  // Clear existing markers
  markerGroup.clearLayers()

  // Add new markers
  props.markers.forEach(marker => {
    const leafletMarker = L.marker([marker.lat, marker.lng])
    
    if (marker.popup) {
      leafletMarker.bindPopup(marker.popup)
    }
    
    leafletMarker.addTo(markerGroup!)
  })
}

// Watch for marker changes
watch(() => props.markers, updateMarkers, { deep: true })

// Watch for center changes
watch(() => props.center, (newCenter) => {
  if (map) {
    map.setView(newCenter, props.zoom)
  }
}, { deep: true })

// Watch for zoom changes
watch(() => props.zoom, (newZoom) => {
  if (map) {
    map.setZoom(newZoom)
  }
})

onMounted(() => {
  initializeMap()
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
})
</script>

<style>
@import 'leaflet/dist/leaflet.css';

/* Fix for default marker icons in bundled environments */
.leaflet-default-icon-path {
  background-image: url('leaflet/dist/images/marker-icon.png');
}
</style>