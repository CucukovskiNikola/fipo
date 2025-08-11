<template>
  <div class="space-y-4">
    <!-- Search Input -->
    <div>
      <label class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
        Search Location
      </label>
      <div class="flex ">
        <InputText v-model="searchQuery" placeholder="Enter city name or address..." class="w-full pl-10 pr-20"
          @keyup.enter="searchLocation" />
        <Icon name="magnifying-glass" class="w-12 text-[#706f6c] dark:text-[#A1A09A]" />
        <Button @click="searchLocation" :disabled="!searchQuery || isSearching" :loading="isSearching" size="small"
          class="ml-4 w-32" :class="{ 'opacity-50': !searchQuery || isSearching }">
          {{ isSearching ? 'Searching...' : 'Search' }}
        </Button>
      </div>
    </div>

    <!-- Map Container -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
        Select Location on Map
      </label>
      <div class="h-96 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden relative">
        <div ref="mapContainer" class="h-full w-full absolute" />
      </div>
      <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
        Click on the map to select a location. The marker will show your selected point.
      </p>
    </div>

    <!-- Error Message -->
    <div v-if="searchError"
      class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
      <p class="text-sm text-red-600 dark:text-red-400">
        {{ searchError }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import L from 'leaflet'
import { onMounted, onUnmounted, ref, watch } from 'vue'
import Icon from '@/components/Icon.vue'

// PrimeVue Components
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'

interface LocationData {
  city: string
  zipCode: string
  latitude: number
  longitude: number
}

interface Props {
  modelValue?: LocationData | null
  center?: [number, number]
  zoom?: number
}

const props = withDefaults(defineProps<Props>(), {
  center: () => [40.7128, -74.0060], 
  zoom: 10
})

const emit = defineEmits<{
  'update:modelValue': [value: LocationData | null]
}>()

const mapContainer = ref<HTMLDivElement>()
const searchQuery = ref('')
const isSearching = ref(false)
const searchError = ref('')
const selectedLocation = ref<LocationData | null>(props.modelValue || null)

let map: L.Map | null = null
let marker: L.Marker | null = null
let resizeObserver: ResizeObserver | null = null

const initializeMap = () => {
  if (!mapContainer.value) {
    console.error('Map container not found')
    return
  }

  // Check if map is already initialized
  if (map) {
    console.log('Map already initialized, skipping')
    return
  }

  // Check if Leaflet is loaded
  if (typeof L === 'undefined') {
    console.error('Leaflet library not loaded')
    searchError.value = 'Map library not loaded. Please refresh the page.'
    return
  }

  try {
    // Clear any existing error
    searchError.value = ''
    
    // Create map
    console.log('Initializing map with container:', mapContainer.value)
    map = L.map(mapContainer.value).setView(props.center, props.zoom)

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19
    }).addTo(map)

    // Add click handler
    map.on('click', (e) => {
      const { lat, lng } = e.latlng
      setLocation(lat, lng)
    })

    // If there's an initial location, show it
    if (selectedLocation.value && selectedLocation.value.latitude && selectedLocation.value.longitude) {
      setLocation(selectedLocation.value.latitude, selectedLocation.value.longitude, false)
    }

    // Set up resize observer to handle container size changes (if supported)
    if (mapContainer.value && typeof ResizeObserver !== 'undefined') {
      try {
        resizeObserver = new ResizeObserver(() => {
          if (map) {
            setTimeout(() => {
              if (map) {
                map.invalidateSize()
              }
            }, 50)
          }
        })
        resizeObserver.observe(mapContainer.value)
      } catch (error) {
        console.warn('ResizeObserver not available:', error)
      }
    }
  } catch (error) {
    console.error('Failed to initialize map:', error)
    searchError.value = 'Failed to initialize map. Please refresh the page.'
  }
}

const setLocation = async (lat: number, lng: number, reverseGeocode = true) => {
  if (!map) return

  try {
    // Remove existing marker
    if (marker) {
      map.removeLayer(marker)
    }

    // Add new marker
    marker = L.marker([lat, lng]).addTo(map)

    // Center map on location
    map.setView([lat, lng], Math.max(map.getZoom(), 13))

    // Update selected location
    selectedLocation.value = {
      city: selectedLocation.value?.city || '',
      zipCode: selectedLocation.value?.zipCode || '',
      latitude: lat,
      longitude: lng
    }

    // Clear any previous errors
    searchError.value = ''

    // Reverse geocode to get address information
    if (reverseGeocode) {
      try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)

        if (!response.ok) {
          throw new Error('Geocoding service unavailable')
        }

        const data = await response.json()

        if (data && data.address) {
          selectedLocation.value = {
            city: data.address.city || data.address.town || data.address.village || data.address.hamlet || data.address.municipality || '',
            zipCode: data.address.postcode || '',
            latitude: lat,
            longitude: lng
          }
        }
      } catch (error) {
        console.warn('Reverse geocoding failed:', error)
        searchError.value = 'Could not get address details. Please enter city and zip code manually.'
      }
    }

    updateLocation()
  } catch (error) {
    console.error('Failed to set location:', error)
    searchError.value = 'Failed to set location on map.'
  }
}

const searchLocation = async () => {
  if (!searchQuery.value.trim() || isSearching.value || !map) return

  isSearching.value = true
  searchError.value = ''

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery.value)}&limit=5&addressdetails=1`,
      {
        headers: {
          'User-Agent': 'Partner Management App'
        }
      }
    )

    if (!response.ok) {
      throw new Error('Search service unavailable')
    }

    const results = await response.json()

    if (results && results.length > 0) {
      const result = results[0]
      const lat = parseFloat(result.lat)
      const lng = parseFloat(result.lon)

      if (isNaN(lat) || isNaN(lng)) {
        throw new Error('Invalid coordinates received')
      }

      await setLocation(lat, lng, false)

      // Update with search result data
      if (result.address) {
        selectedLocation.value = {
          city: result.address.city || result.address.town || result.address.village || result.address.hamlet || result.address.municipality || result.display_name.split(',')[0],
          zipCode: result.address.postcode || selectedLocation.value?.zipCode || '',
          latitude: lat,
          longitude: lng
        }
        updateLocation()
      }
    } else {
      searchError.value = 'No results found for this search. Please try a different location.'
    }
  } catch (error) {
    console.error('Search failed:', error)
    searchError.value = error instanceof Error ? error.message : 'Search failed. Please try again.'
  } finally {
    isSearching.value = false
  }
}

const updateLocation = () => {
  emit('update:modelValue', selectedLocation.value)
}

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (newValue && newValue !== selectedLocation.value) {
    selectedLocation.value = newValue
    if (newValue.latitude && newValue.longitude) {
      setLocation(newValue.latitude, newValue.longitude, false)
    }
  }
}, { deep: true })

onMounted(() => {
  // Single initialization with proper sizing
  setTimeout(() => {
    initializeMap()
    if (map) {
      // Force the map to recalculate its size after initialization
      setTimeout(() => {
        if (map) {
          map.invalidateSize()
        }
      }, 100)
      
      // Additional invalidation after a longer delay to ensure proper rendering
      setTimeout(() => {
        if (map) {
          map.invalidateSize()
        }
      }, 500)
    }
  }, 100)
})

onUnmounted(() => {
  if (resizeObserver) {
    resizeObserver.disconnect()
    resizeObserver = null
  }
  if (map) {
    map.remove()
    map = null
  }
})
</script>

