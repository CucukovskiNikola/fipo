<template>
  <AppLayout>
    <template #header>
      <Heading title="Partners Map" />
    </template>

    <div class="w-full mx-auto space-y-6 p-6">
      <!-- Map Controls -->
      <div
        class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h3 class="text-lg font-semibold text-[#1b1b18] mb-1">Partner Locations</h3>
            <p class="text-sm text-[#706f6c]">
              Showing {{ partners.length }} partner{{ partners.length === 1 ? '' : 's' }} on the map
            </p>
          </div>
          
          <!-- Category Filter -->
          <div class="flex items-center space-x-4">
            <div>
              <label for="category-filter" class="block text-sm font-medium text-[#706f6c] mb-1">
                Filter by Category:
              </label>
              <Select 
                v-model="selectedCategory" 
                :options="categoryOptions" 
                option-label="name" 
                option-value="id"
                placeholder="All Categories" 
                class="w-48"
                @change="filterMarkers">
                <template #option="{ option }">
                  <div class="flex items-center">
                    <span class="mr-2">{{ option.icon }}</span>
                    {{ option.name }}
                  </div>
                </template>
              </Select>
            </div>
            <Button 
              severity="secondary" 
              outlined 
              @click="resetFilters"
              class="mt-6 min-w-24">
              <template #icon>
                <Icon name="x-mark" class="h-4 w-4" />
              </template>
              Clear Filter
            </Button>
          </div>
        </div>
      </div>

      <!-- Partners Map -->
      <div
        class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
        <div class="h-[70vh]">
          <MapComponent 
            :center="mapCenter" 
            :zoom="mapZoom"
            :markers="filteredMarkers"
            class="h-full rounded-lg overflow-hidden"
          />
        </div>
      </div>

      <!-- Partners List -->
      <div
        class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
        <h3 class="text-lg font-semibold text-[#1b1b18] mb-4">Partner Directory</h3>
        
        <div v-if="filteredPartners.length === 0" class="text-center py-8">
          <Icon name="map-pin" class="mx-auto h-12 w-12 text-[#706f6c] mb-4" />
          <p class="text-[#706f6c]">
            {{ selectedCategory ? 'No partners found in this category.' : 'No partners available.' }}
          </p>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="partner in filteredPartners" :key="partner.id"
            class="rounded-lg bg-[#FDFDFC] p-4 shadow-sm border border-[#e3e3e0]">
            
            <!-- Partner Image -->
            <div v-if="getPartnerImage(partner)" class="mb-3">
              <img :src="getPartnerImage(partner)" :alt="partner.title" 
                class="w-full h-32 object-cover rounded-lg">
            </div>
            
            <!-- Partner Info -->
            <div class="space-y-2">
              <div class="flex items-start justify-between">
                <h4 class="font-semibold text-[#1b1b18] text-sm">{{ partner.title }}</h4>
                <span class="text-xs px-2 py-1 rounded-full bg-[#706f6c]/10 text-[#706f6c]">
                  {{ getCategoryName(partner.category) }}
                </span>
              </div>
              
              <p v-if="partner.name_of_owner" class="text-xs text-[#706f6c]">
                Owner: {{ partner.name_of_owner }}
              </p>
              
              <p class="text-xs text-[#706f6c] line-clamp-2">{{ partner.description }}</p>
              
              <div class="flex items-center text-xs text-[#706f6c]">
                <Icon name="map-pin" class="mr-1 h-3 w-3" />
                {{ partner.city }}, {{ partner.zip_code }}
              </div>
              
              <!-- Actions -->
              <div class="flex items-center justify-between pt-2">
                <div class="flex space-x-2">
                  <Link :href="route('partners.edit', partner.id)"
                    class="text-[#706f6c] hover:text-[#1b1b18]">
                    <Icon name="pencil" class="h-3 w-3" />
                  </Link>
                </div>
                <Button 
                  size="small" 
                  severity="secondary" 
                  text
                  @click="centerOnPartner(partner)"
                  class="text-xs min-w-24">
                  <template #icon>
                    <Icon name="map-pin" class="h-3 w-3" />
                  </template>
                  View on Map
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Icon from '@/components/Icon.vue'
import MapComponent from '@/components/MapComponent.vue'
import categories from '@/data/categories.json'

// PrimeVue Components
import Select from 'primevue/select'
import Button from 'primevue/button'

interface User {
  id: number
  name: string
  email: string
}

interface PartnerImage {
  id: number
  partner_id: number
  path: string
  sort_order: number
}

interface Partner {
  id: number
  title: string
  name_of_owner: string | null
  category: string
  description: string
  image: string | null
  images?: PartnerImage[]
  city: string
  zip_code: string
  longitude: number
  latitude: number
  created_by: number
  user?: User
}

interface Props {
  partners: Partner[]
}

const props = defineProps<Props>()

// Reactive data
const selectedCategory = ref<string | null>(null)
const mapCenter = ref<[number, number]>([41.9, 22.4]) // Default to Macedonia region
const mapZoom = ref<number>(8)

// Category options for filter
const categoryOptions = computed(() => [
  { id: null, name: 'All Categories', icon: '🔍' },
  ...categories
])

// Filtered partners based on selected category
const filteredPartners = computed(() => {
  if (!selectedCategory.value) {
    return props.partners
  }
  return props.partners.filter(partner => partner.category === selectedCategory.value)
})

// Map markers from partners
const filteredMarkers = computed(() => {
  return filteredPartners.value.map(partner => ({
    lat: partner.latitude,
    lng: partner.longitude,
    popup: `
      <div class="p-2">
        <h4 class="font-semibold text-sm mb-1">${partner.title}</h4>
        ${partner.name_of_owner ? `<p class="text-xs text-gray-600 mb-1">Owner: ${partner.name_of_owner}</p>` : ''}
        <p class="text-xs text-gray-700 mb-2">${partner.description.length > 100 ? partner.description.substring(0, 100) + '...' : partner.description}</p>
        <div class="flex items-center text-xs text-gray-500 mb-2">
          <span class="mr-1">📍</span>
          ${partner.city}, ${partner.zip_code}
        </div>
        <div class="flex items-center text-xs">
          <span class="mr-1">${getCategoryIcon(partner.category)}</span>
          ${getCategoryName(partner.category)}
        </div>
      </div>
    `
  }))
})

// Helper functions
const getCategoryName = (categoryId: string): string => {
  const category = categories.find(cat => cat.id === categoryId)
  return category ? category.name : categoryId
}

const getCategoryIcon = (categoryId: string): string => {
  const category = categories.find(cat => cat.id === categoryId)
  return category ? category.icon : '📍'
}

const getPartnerImage = (partner: Partner): string | null => {
  // Check for multiple images first
  if (partner.images && partner.images.length > 0) {
    const imagePath = partner.images[0].path
    return getImageUrl(imagePath)
  }
  
  // Fallback to single image
  if (partner.image) {
    return getImageUrl(partner.image)
  }
  
  return null
}

const getImageUrl = (imagePath: string): string => {
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }
  return `/storage/${imagePath}`
}

const filterMarkers = () => {
  // The computed properties will handle the filtering
  // Optionally center the map on filtered partners
  if (filteredPartners.value.length > 0) {
    // Calculate center of filtered partners
    const lats = filteredPartners.value.map(p => p.latitude)
    const lngs = filteredPartners.value.map(p => p.longitude)
    const centerLat = lats.reduce((sum, lat) => sum + lat, 0) / lats.length
    const centerLng = lngs.reduce((sum, lng) => sum + lng, 0) / lngs.length
    mapCenter.value = [centerLat, centerLng]
  }
}

const resetFilters = () => {
  selectedCategory.value = null
  mapCenter.value = [41.9, 22.4]
  mapZoom.value = 8
}

const centerOnPartner = (partner: Partner) => {
  mapCenter.value = [partner.latitude, partner.longitude]
  mapZoom.value = 15
  
  // Scroll to map
  const mapElement = document.querySelector('.h-\\[70vh\\]')
  if (mapElement) {
    mapElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
  }
}

// Initialize map center based on partners
onMounted(() => {
  if (props.partners.length > 0) {
    // Calculate center of all partners
    const lats = props.partners.map(p => p.latitude)
    const lngs = props.partners.map(p => p.longitude)
    const centerLat = lats.reduce((sum, lat) => sum + lat, 0) / lats.length
    const centerLng = lngs.reduce((sum, lng) => sum + lng, 0) / lngs.length
    mapCenter.value = [centerLat, centerLng]
    mapZoom.value = 10
  }
})
</script>