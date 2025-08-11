<template>
  <AppLayout>
    <template #header>
      <Heading>Map Demo</Heading>
    </template>

    <div class="space-y-6">
      <!-- Basic Map -->
      <div class="rounded-lg border bg-card p-6">
        <h3 class="mb-4 text-lg font-semibold">Basic OpenStreetMap</h3>
        <div class="h-96">
          <MapComponent 
            :center="[42.3601, -71.0589]" 
            :zoom="12"
            class="h-full"
          />
        </div>
      </div>

      <!-- Map with Markers -->
      <div class="rounded-lg border bg-card p-6">
        <h3 class="mb-4 text-lg font-semibold">Map with Markers</h3>
        <div class="h-96">
          <MapComponent 
            :center="[40.7128, -74.0060]" 
            :zoom="11"
            :markers="markers"
            class="h-full"
          />
        </div>
      </div>

      <!-- Interactive Controls -->
      <div class="rounded-lg border bg-card p-6">
        <h3 class="mb-4 text-lg font-semibold">Interactive Map</h3>
        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
          <div>
            <label class="block text-sm font-medium mb-2">Latitude</label>
            <input
              v-model.number="customCenter[0]"
              type="number"
              step="0.0001"
              class="w-full rounded border px-3 py-2"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Longitude</label>
            <input
              v-model.number="customCenter[1]"
              type="number"
              step="0.0001"
              class="w-full rounded border px-3 py-2"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Zoom Level</label>
            <input
              v-model.number="customZoom"
              type="number"
              min="1"
              max="18"
              class="w-full rounded border px-3 py-2"
            />
          </div>
        </div>
        <div class="h-96">
          <MapComponent 
            :center="customCenter" 
            :zoom="customZoom"
            :markers="customMarkers"
            class="h-full"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import MapComponent from '@/components/MapComponent.vue'
import type { LatLngTuple } from 'leaflet'

// Static markers for NYC
const markers = [
  {
    lat: 40.7589,
    lng: -73.9851,
    popup: 'Times Square<br/>The heart of NYC'
  },
  {
    lat: 40.6892,
    lng: -74.0445,
    popup: 'Statue of Liberty<br/>Symbol of freedom'
  },
  {
    lat: 40.7505,
    lng: -73.9934,
    popup: 'Empire State Building<br/>Art Deco masterpiece'
  }
]

// Interactive map controls
const customCenter = ref<LatLngTuple>([48.8566, 2.3522]) // Paris
const customZoom = ref(12)

const customMarkers = ref([
  {
    lat: 48.8584,
    lng: 2.2945,
    popup: 'Eiffel Tower<br/>La Tour Eiffel'
  },
  {
    lat: 48.8606,
    lng: 2.3376,
    popup: 'Louvre Museum<br/>Musée du Louvre'
  }
])
</script>