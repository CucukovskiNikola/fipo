<template>
  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Partners Map"
      title-icon="map"
      :home-route="route('dashboard')"
      :navigation-links="navigationLinks"
    />

    <div class="space-y-6 max-w-6xl mx-auto">
      <!-- Map Controls -->
      <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h3 class="text-lg font-semibold text-white mb-1">
              Partner Locations
            </h3>
            <p class="text-sm text-gray-300">
              Showing {{ partners.length }} partner{{
                partners.length === 1 ? "" : "s"
              }}
              on the map
            </p>
          </div>
        </div>
      </div>

      <!-- Partners Map -->
      <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
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
      <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
        <h3 class="text-lg font-semibold text-white mb-4">Partner Directory</h3>

        <div v-if="filteredPartners.length === 0" class="text-center py-8">
          <Icon name="map-pin" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
          <p class="text-gray-300">
            {{
              selectedCategory
                ? "No partners found in this category."
                : "No partners available."
            }}
          </p>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="partner in filteredPartners"
            :key="partner.id"
            class="rounded-lg border border-white/20 p-6 transition-all hover:border-white/40 hover:bg-white/10 backdrop-blur-sm"
          >
            <!-- Partner Image -->
            <div v-if="getPartnerImage(partner)" class="mb-3">
              <img
                :src="getPartnerImage(partner)!"
                :alt="partner.title"
                class="w-full h-32 object-cover rounded-lg"
              />
            </div>

            <!-- Partner Info -->
            <div class="space-y-2">
              <div class="flex items-start justify-between">
                <h4 class="font-semibold text-white text-sm truncate">
                  {{ partner.title }}
                </h4>
                <span
                  class="text-xs px-2 py-1 rounded-full bg-white/10 text-gray-300"
                >
                  {{ getCategoryName(partner.category) }}
                </span>
              </div>

              <p v-if="partner.name_of_owner" class="text-xs text-gray-300">
                Owner: {{ partner.name_of_owner }}
              </p>

              <p class="text-xs text-gray-300 min-h-6 truncate">
                {{ partner.description }}
              </p>

              <div class="flex items-center text-xs text-gray-300">
                <Icon name="map-pin" class="mr-1 h-3 w-3" />
                {{ partner.city }}, {{ partner.zip_code }}
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-end pt-2">
                <Button
                  variant="default"
                  size="normal"
                  @click="centerOnPartner(partner)"
                  class="text-xs min-w-24 text-gray-300 hover:text-white hover:bg-white/10"
                >
                  <Icon name="map-pin" class="h-3 w-3 mr-1" />
                  View on Map
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Icon from "@/components/Icon.vue";
import DashboardNavbar from "@/components/DashboardNavbar.vue";
import MapComponent from "@/components/MapComponent.vue";
import { useCategories } from "@/composables/useCategories";
import { useTranslations } from "@/composables/useTranslations";

// Shadcn Components
import Button from "@/components/ui/button/Button.vue";
import DropdownMenu from "@/components/ui/dropdown-menu/DropdownMenu.vue";
import DropdownMenuContent from "@/components/ui/dropdown-menu/DropdownMenuContent.vue";
import DropdownMenuItem from "@/components/ui/dropdown-menu/DropdownMenuItem.vue";
import DropdownMenuTrigger from "@/components/ui/dropdown-menu/DropdownMenuTrigger.vue";

interface User {
  id: number;
  name: string;
  email: string;
}

interface PartnerImage {
  id: number;
  partner_id: number;
  path: string;
  sort_order: number;
}

interface Partner {
  id: number;
  title: string;
  name_of_owner: string | null;
  category: string;
  description: string;
  image: string | null;
  images?: PartnerImage[];
  city: string;
  zip_code: string;
  longitude: number;
  latitude: number;
  created_by: number;
  user?: User;
}

interface Props {
  partners: Partner[];
}

const props = defineProps<Props>();

// Navigation configuration
const navigationLinks = [
  {
    label: "Partners",
    href: route("partners.index"),
  },
  {
    label: "Create Partner",
    href: route("partners.create"),
  },
  {
    label: "Home",
    href: route("dashboard"),
  },
];

// Reactive data
const selectedCategory = ref<string | null>(null);
const mapCenter = ref<[number, number]>([41.9, 22.4]); // Default to Macedonia region
const mapZoom = ref<number>(8);

const { trans } = useTranslations();
const { categories, getCategoryName } = useCategories();

// Display text for selected category
const selectedCategoryDisplay = computed(() => {
  if (!selectedCategory.value) {
    return "All Categories";
  }
  const category = categories.value.find((cat) => cat.id === selectedCategory.value);
  return category ? category.name : "All Categories";
});

// Filtered partners based on selected category
const filteredPartners = computed(() => {
  if (!selectedCategory.value) {
    return props.partners;
  }
  return props.partners.filter(
    (partner) => partner.category === selectedCategory.value
  );
});

// Map markers from partners
const filteredMarkers = computed(() => {
  return filteredPartners.value.map((partner) => ({
    lat: partner.latitude,
    lng: partner.longitude,
    popup: `
      <div class="p-2">
        <h4 class="font-semibold text-sm mb-1">${partner.title}</h4>
        ${partner.name_of_owner ? `<p class="text-xs text-gray-600 mb-1">Owner: ${partner.name_of_owner}</p>` : ""}
        <p class="text-xs text-gray-700 mb-2">${partner.description.length > 100 ? partner.description.substring(0, 100) + "..." : partner.description}</p>
        <div class="flex items-center text-xs text-gray-500 mb-2">
          <span class="mr-1">📍</span>
          ${partner.city}, ${partner.zip_code}
        </div>
        <div class="flex items-center text-xs">
          <span class="mr-1">${getCategoryIcon(partner.category)}</span>
          ${getCategoryName(partner.category)}
        </div>
      </div>
    `,
  }));
});

// Helper functions
const getCategoryIcon = (categoryId: string): string => {
  const category = categories.value.find((cat) => cat.id === categoryId);
  return category ? category.icon : "📍";
};

const getPartnerImage = (partner: Partner): string | null => {
  // Check for multiple images first
  if (partner.images && partner.images.length > 0) {
    const imagePath = partner.images[0].path;
    return getImageUrl(imagePath);
  }

  // Fallback to single image
  if (partner.image) {
    return getImageUrl(partner.image);
  }

  return null;
};

const getImageUrl = (imagePath: string): string => {
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }
  return `/storage/${imagePath}`;
};

const selectCategory = (categoryId: string | null) => {
  selectedCategory.value = categoryId;
  filterMarkers();
};

const filterMarkers = () => {
  // The computed properties will handle the filtering
  // Optionally center the map on filtered partners
  if (filteredPartners.value.length > 0) {
    // Calculate center of filtered partners
    const lats = filteredPartners.value
      .map((p) => p.latitude)
      .filter((lat) => !isNaN(lat));
    const lngs = filteredPartners.value
      .map((p) => p.longitude)
      .filter((lng) => !isNaN(lng));

    if (lats.length > 0 && lngs.length > 0) {
      const centerLat = lats.reduce((sum, lat) => sum + lat, 0) / lats.length;
      const centerLng = lngs.reduce((sum, lng) => sum + lng, 0) / lngs.length;
      mapCenter.value = [centerLat, centerLng];
    }
  }
};

const resetFilters = () => {
  selectedCategory.value = null;
  mapCenter.value = [41.9, 22.4];
  mapZoom.value = 8;
};

const centerOnPartner = (partner: Partner) => {
  mapCenter.value = [partner.latitude, partner.longitude];
  mapZoom.value = 15;

  // Scroll to map
  const mapElement = document.querySelector(".h-\\[70vh\\]");
  if (mapElement) {
    mapElement.scrollIntoView({ behavior: "smooth", block: "center" });
  }
};

// Initialize map center based on partners
onMounted(() => {
  if (props.partners.length > 0) {
    // Calculate center of all partners
    const lats = props.partners
      .map((p) => p.latitude)
      .filter((lat) => !isNaN(lat));
    const lngs = props.partners
      .map((p) => p.longitude)
      .filter((lng) => !isNaN(lng));

    if (lats.length > 0 && lngs.length > 0) {
      const centerLat = lats.reduce((sum, lat) => sum + lat, 0) / lats.length;
      const centerLng = lngs.reduce((sum, lng) => sum + lng, 0) / lngs.length;
      mapCenter.value = [centerLat, centerLng];
      mapZoom.value = 10;
    }
  }
});
</script>
