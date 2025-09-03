<template>
  <div ref="mapContainer" class="h-full w-full rounded-lg" />
</template>

<script setup lang="ts">
import L from "leaflet";
import type { LatLngTuple } from "leaflet";
import { onMounted, onUnmounted, ref, watch } from "vue";

interface Props {
  center?: LatLngTuple;
  zoom?: number;
  height?: string;
  markers?: Array<{ lat: number; lng: number; popup?: string }>;
  className?: string;
}

const props = withDefaults(defineProps<Props>(), {
  center: () => [51.505, -0.09], // London as default
  zoom: 13,
  height: "400px",
  markers: () => [],
  className: "",
});

const mapContainer = ref<HTMLDivElement>();
let map: L.Map | null = null;
let markerGroup: L.LayerGroup | null = null;

const initializeMap = () => {
  if (!mapContainer.value) return;

  // Validate center coordinates
  const center: LatLngTuple =
    props.center && !isNaN(props.center[0]) && !isNaN(props.center[1])
      ? props.center
      : [51.505, -0.09]; // London as fallback

  // Create map
  map = L.map(mapContainer.value).setView(center, props.zoom);

  // Add OpenStreetMap tiles
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map);

  // Initialize marker group
  markerGroup = L.layerGroup().addTo(map);

  // Add initial markers
  updateMarkers();
};

const updateMarkers = () => {
  if (!map || !markerGroup) return;

  // Clear existing markers
  markerGroup.clearLayers();

  // Add new markers
  props.markers.forEach((marker) => {
    // Create custom HTML icon using map-pin
    const customIcon = L.divIcon({
      html: `
        <div class="custom-marker">
          <svg width="50" height="50" viewBox="0 0 24 24" fill="bg-blue-500" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
      `,
      className: "custom-div-icon",
      iconSize: [50, 50],
      iconAnchor: [20, 35],
      popupAnchor: [0, -24],
    });

    const leafletMarker = L.marker([marker.lat, marker.lng], {
      icon: customIcon,
    });

    if (marker.popup) {
      leafletMarker.bindPopup(marker.popup);
    }

    leafletMarker.addTo(markerGroup!);
  });
};

// Watch for marker changes
watch(() => props.markers, updateMarkers, { deep: true });

// Watch for center changes
watch(
  () => props.center,
  (newCenter) => {
    if (map && newCenter && !isNaN(newCenter[0]) && !isNaN(newCenter[1])) {
      map.setView(newCenter, props.zoom);
    }
  },
  { deep: true }
);

// Watch for zoom changes
watch(
  () => props.zoom,
  (newZoom) => {
    if (map) {
      map.setZoom(newZoom);
    }
  }
);

onMounted(() => {
  initializeMap();
});

onUnmounted(() => {
  if (map) {
    map.remove();
    map = null;
  }
});
</script>

<style>
@import "leaflet/dist/leaflet.css";

/* Custom marker styles */
.custom-div-icon {
  background: transparent !important;
  border: none !important;
}

.custom-marker {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
}

.custom-marker svg {
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}
</style>
