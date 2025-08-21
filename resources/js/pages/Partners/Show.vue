<template>
  <Head>
    <title>{{ meta?.title || `findemich - ${partner?.title}` }}</title>
    <meta
      name="description"
      :content="
        meta?.description ||
        `View ${partner?.title} - ${partner?.category} business partner in ${partner?.city}. Contact information, services, and location details available.`
      "
    />
  </Head>

  <div
    class="min-h-screen text-white p-2 bg-black"
    style="
      background-image: url(&quot;/images/bg.webp&quot;);
      background-size: contain;
      background-repeat: no-repeat;
    "
  >
    <Navbar :activeSection="''" @change-section="handleNavigation" />
    <transition
      enter-active-class="transition ease-out duration-500"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-300"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <section
        class="liquid-glass text-white max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
      >
        <!-- Back Button -->
        <div class="mb-6">
          <Button
            @click="goBack"
            variant="default"
            class="!rounded-3xl bg-white/10 border border-white/10 text-white"
            size="normal"
          >
            <CircumIcons
              name="circle_chev_left"
              size="16px"
              color="white"
              class="mr-2"
            />
            Back
          </Button>
        </div>

        <div
          class="grid grid-cols-1 lg:grid-cols-2 gap-8 bg-white/10 p-2 rounded-3xl"
        >
          <!-- Left Side: Images + Owner Info + Rating -->
          <div class="space-y-6">
            <!-- Main Image Display -->
            <div
              v-if="partner.images && partner.images.length > 0"
              class="relative p-2 border border-white/10 rounded-3xl shadow-lg"
            >
              <div class="aspect-video bg-gray-900 rounded-3xl overflow-hidden">
                <img
                  :src="currentImageUrl"
                  :alt="partner.title"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
              </div>

              <!-- Image Navigation Thumbnails -->
              <div v-if="partner.images.length > 1" class="mt-4">
                <div class="flex justify-center space-x-2 mb-2">
                  <button
                    v-for="(image, index) in partner.images"
                    :key="image.id"
                    @click="currentImageIndex = index"
                    :class="[
                      'w-16 h-16 rounded-2xl overflow-hidden border-2 transition-all',
                      currentImageIndex === index
                        ? 'border-white'
                        : 'border-gray-600 hover:border-gray-400',
                    ]"
                  >
                    <img
                      :src="getImageUrl(image.path)"
                      :alt="`${partner.title} - Image ${index + 1}`"
                      class="w-full h-full object-cover"
                    />
                  </button>
                </div>
                <div class="text-center text-white/50 text-sm">
                  {{ currentImageIndex + 1 }} of {{ partner.images.length }}
                </div>
              </div>
            </div>

            <!-- Fallback to legacy single image -->
            <div
              v-else-if="partner.image"
              class="aspect-video bg-gray-900 rounded-3xl overflow-hidden"
            >
              <img
                :src="getImageUrl(partner.image)"
                :alt="partner.title"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>

            <!-- No Image Placeholder -->
            <div
              v-else
              class="aspect-video bg-gray-900 rounded-3xl flex items-center justify-center"
            >
              <div class="text-center text-gray-400">
                <CircumIcons
                  name="image_on"
                  size="40px"
                  color="currentColor"
                  class="mb-2"
                />
                <p>No images available</p>
              </div>
            </div>

            <!-- Owner & Rating Info -->
            <div class="rounded-3xl p-6 space-y-4">
              <!-- Owner -->
              <div
                v-if="partner.name_of_owner"
                class="flex items-center space-x-3"
              >
                <div
                  class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center"
                >
                  <CircumIcons name="user" class="text-2xl" color="white" />
                </div>
                <div>
                  <p class="text-white/50">Owner</p>
                  <p class="font-semibold text-xl text-white">
                    {{ partner.name_of_owner }}
                  </p>
                </div>
              </div>

              <!-- Google Rating Placeholder -->
              <div class="flex items-center space-x-3">
                <div
                  class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center"
                >
                  <CircumIcons name="star" class="text-2xl" color="white" />
                </div>
                <div>
                  <p class="font-semibold text-white">Rating</p>
                  <div class="flex items-center space-x-2">
                    <div
                      class="flex text-yellow-400 space-x-1 max-w-[70%] overflow-visible"
                    >
                      <CircumIcons
                        name="star"
                        size="20px"
                        color="currentColor"
                      />
                      <CircumIcons
                        name="star"
                        size="20px"
                        color="currentColor"
                      />
                      <CircumIcons
                        name="star"
                        size="20px"
                        color="currentColor"
                      />
                      <CircumIcons
                        name="star"
                        size="20px"
                        color="currentColor"
                      />
                      <CircumIcons
                        name="star"
                        size="20px"
                        color="currentColor"
                      />
                    </div>
                    <span class="text-white/70 text-sm truncate"
                      >4.2 (Google Reviews)</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Side: Title, Description, Category -->
          <div class="rounded-3xl p-6 space-y-6">
            <!-- Header -->
            <div>
              <h1 class="text-3xl font-bold text-white mb-4">
                {{ partner.title }}
              </h1>
              <div class="flex items-center space-x-4 mb-4">
                <span
                  class="inline-flex bg-white/10 border-white/10 border items-center px-4 py-2 rounded-full text-sm font-medium text-white"
                >
                  {{ categoryInfo.icon }} {{ categoryInfo.name }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <div class="space-y-4">
              <h2 class="text-xl text-white">About this business:</h2>
              <p class="text-white/80 leading-relaxed text-lg">
                {{ partner.description }}
              </p>
            </div>
          </div>
        </div>
      </section>
    </transition>

    <!-- Full Width Map Section -->
    <section
      class="liquid-glass text-white max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
    >
      <div class="space-y-6">
        <!-- Map Header -->
        <div class="text-center">
          <h2 class="text-2xl font-bold text-white mb-2">Location</h2>
          <p class="text-white/70">
            Find {{ partner.title }} at {{ partner.city }},
            {{ partner.zip_code }}
          </p>
        </div>

        <!-- Map Container -->
        <div
          class="bg-gray-900 rounded-3xl overflow-hidden"
          style="height: 400px"
        >
          <div
            ref="mapContainer"
            id="partner-map"
            class="w-full h-full relative"
          >
            <!-- Map will be rendered here by Leaflet -->
          </div>
        </div>

        <!-- Map Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-center">
            <CircumIcons
              name="location_on"
              size="24px"
              color="white"
              class="mb-2 mx-auto"
            />
            <p class="font-semibold text-white">Address</p>
            <p class="text-white/70 text-sm">
              {{ partner.city }}, {{ partner.zip_code }}
            </p>
          </div>

          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-center">
            <CircumIcons
              name="compass_1"
              size="24px"
              color="white"
              class="mb-2 mx-auto"
            />
            <p class="font-semibold text-white">Coordinates</p>
            <p class="text-white/70 text-sm">
              {{ Number(partner.latitude)?.toFixed(4) }},
              {{ Number(partner.longitude)?.toFixed(4) }}
            </p>
          </div>

          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-center">
            <CircumIcons
              name="location_arrow_1"
              size="24px"
              color="white"
              class="mb-2 mx-auto"
            />
            <p class="font-semibold text-white">Get Directions</p>
            <a
              :href="`https://www.google.com/maps/dir/?api=1&destination=${Number(partner.latitude)},${Number(partner.longitude)}`"
              target="_blank"
              class="text-blue-400 hover:text-blue-300 text-sm underline transition-colors"
            >
              Open in Google Maps
            </a>
          </div>
        </div>
      </div>
    </section>

    <Footer @navigate-to="handleNavigation" />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { router, Head } from "@inertiajs/vue3";
import Button from "@/components/ui/button/Button.vue";
import Navbar from "@/components/Navbar.vue";
import Footer from "@/components/Footer.vue";
import categories from "@/data/categories.json";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

import CircumIcons from "@klarr-agency/circum-icons-vue";

const props = defineProps({
  partner: Object,
  meta: Object,
});

const currentImageIndex = ref(0);
const mapContainer = ref(null);
let map = null;

// Get category icon and name
const categoryInfo = computed(() => {
  const category = categories.find((cat) => cat.id === props.partner.category);
  return category
    ? { icon: category.icon, name: category.name }
    : { icon: "📍", name: props.partner.category };
});

// Get current image URL
const currentImageUrl = computed(() => {
  if (props.partner.images && props.partner.images.length > 0) {
    return getImageUrl(props.partner.images[currentImageIndex.value].path);
  }
  return null;
});

// Get image URL helper
const getImageUrl = (imagePath) => {
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }
  return `/storage/${imagePath}`;
};

// Handle image load errors
const handleImageError = (event) => {
  event.target.style.display = "none";
};

// Format date
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

// Go back function
const goBack = () => {
  window.history.back();
};

// Handle navigation from navbar/footer
const handleNavigation = (section) => {
  // Navigate to home page with the section
  router.visit(`/?section=${section}`);
};

// Initialize map
const initializeMap = () => {
  const lat = Number(props.partner.latitude);
  const lng = Number(props.partner.longitude);

  if (mapContainer.value && lat && lng && !isNaN(lat) && !isNaN(lng)) {
    // Create the map
    map = L.map(mapContainer.value).setView([lat, lng], 15);

    // Add OpenStreetMap tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution:
        '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19,
    }).addTo(map);

    // Create a custom icon for the marker
    const customIcon = L.divIcon({
      className: "custom-marker",
      html: `
        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full border-2 border-white shadow-lg">
          <i class="pi pi-map-marker text-white text-lg"></i>
        </div>
      `,
      iconSize: [40, 40],
      iconAnchor: [20, 20],
      popupAnchor: [0, -20],
    });

    // Add marker with popup
    const marker = L.marker([lat, lng], { icon: customIcon })
      .addTo(map)
      .bindPopup(
        `
        <div class="text-center p-2">
          <h3 class="font-bold text-lg mb-2">${props.partner.title}</h3>
          <p class="text-sm text-gray-600 mb-2">${categoryInfo.value.icon} ${categoryInfo.value.name}</p>
          <p class="text-sm text-gray-700">${props.partner.city}, ${props.partner.zip_code}</p>
          <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" 
             target="_blank" 
             class="inline-block mt-2 px-3 py-1 bg-white/10 text-white text-xs rounded-full hover:bg-white/20 transition-colors">
            Get Directions
          </a>
        </div>
      `
      )
      .openPopup();

    // Add some styling to the map container
    if (mapContainer.value) {
      mapContainer.value.style.borderRadius = "24px";
    }
  }
};

// Cleanup map on unmount
const cleanupMap = () => {
  if (map) {
    map.remove();
    map = null;
  }
};

// Mount and unmount hooks
onMounted(() => {
  // Small delay to ensure DOM is ready
  setTimeout(() => {
    initializeMap();
  }, 100);
});

onUnmounted(() => {
  cleanupMap();
});
</script>

<style scoped>
/* Custom marker styling */
:deep(.custom-marker) {
  background: transparent !important;
  border: none !important;
}

/* Map controls styling */
:deep(.leaflet-control-zoom) {
  border: none !important;
  border-radius: 12px !important;
  overflow: hidden;
}

:deep(.leaflet-control-zoom a) {
  background: rgba(255, 255, 255, 0.9) !important;
  border: none !important;
  color: #1a0d05 !important;
  font-weight: bold;
}

:deep(.leaflet-control-zoom a:hover) {
  background: rgba(255, 255, 255, 1) !important;
}

/* Attribution styling */
:deep(.leaflet-control-attribution) {
  background: rgba(0, 0, 0, 0.7) !important;
  color: white !important;
  border-radius: 8px !important;
  font-size: 10px !important;
}

:deep(.leaflet-control-attribution a) {
  color: #60a5fa !important;
}

/* Popup styling */
:deep(.leaflet-popup-content-wrapper) {
  border-radius: 12px !important;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
}

:deep(.leaflet-popup-tip) {
  background: white !important;
}
</style>
