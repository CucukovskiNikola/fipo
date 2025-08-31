<template>
  <transition
    enter-active-class="transition ease-out duration-500"
    enter-from-class="opacity-0 scale-95 translate-y-2"
    enter-to-class="opacity-100 scale-100 translate-y-0"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100 scale-100 translate-y-0"
    leave-to-class="opacity-0 scale-95 translate-y-2"
  >
    <div v-if="showButton" class="fixed bottom-20 right-4 z-40">
      <div class="relative group">
        <!-- Tooltip -->
        <div
          v-if="showTooltip"
          class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
        >
          {{ cookieTrans("settings") }}
          <div
            class="absolute top-full right-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"
          ></div>
        </div>

        <!-- Button -->
        <button
          @click="openSettings"
          class="w-12 h-12 gradient-color rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center justify-center text-white group"
          :title="cookieTrans('settings')"
        >
          <Icon name="cookie" class="h-6 w-6" />
        </button>

        <!-- Badge for consent status -->
        <div
          v-if="consentStatus !== 'accepted'"
          class="absolute -top-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-xs font-bold"
          :class="consentBadgeClass"
        >
          {{ consentBadgeText }}
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useCookieTranslations } from "@/composables/useCookieTranslations";
import {
  useCookieManager,
  CONSENT_STATUS,
} from "@/composables/useCookieManager";
import Icon from "./Icon.vue";

const emit = defineEmits(["open-settings"]);

const { cookieTrans } = useCookieTranslations();
const { consentStatus } = useCookieManager();

const showButton = ref(false);
const showTooltip = ref(true);

// Show button after consent is given or after some time
let showButtonTimer = null;
let hideTooltipTimer = null;

onMounted(() => {
  // Show button after 3 seconds if consent was already given
  if (consentStatus.value !== CONSENT_STATUS.PENDING) {
    showButtonTimer = setTimeout(() => {
      showButton.value = true;
    }, 3000);
  }

  // Show button immediately after consent is given
  const checkConsent = () => {
    if (consentStatus.value !== CONSENT_STATUS.PENDING && !showButton.value) {
      showButton.value = true;
    }
  };

  // Poll for consent changes (simple approach)
  const pollInterval = setInterval(checkConsent, 1000);

  // Hide tooltip after 10 seconds
  hideTooltipTimer = setTimeout(() => {
    showTooltip.value = false;
  }, 10000);

  // Cleanup
  onUnmounted(() => {
    if (showButtonTimer) clearTimeout(showButtonTimer);
    if (hideTooltipTimer) clearTimeout(hideTooltipTimer);
    clearInterval(pollInterval);
  });
});

const consentBadgeClass = computed(() => {
  switch (consentStatus.value) {
    case CONSENT_STATUS.DECLINED:
      return "bg-red-500 text-white";
    case CONSENT_STATUS.PARTIAL:
      return "bg-yellow-500 text-black";
    case CONSENT_STATUS.PENDING:
      return "bg-blue-500 text-white";
    default:
      return "bg-gray-500 text-white";
  }
});

const consentBadgeText = computed(() => {
  switch (consentStatus.value) {
    case CONSENT_STATUS.DECLINED:
      return "!";
    case CONSENT_STATUS.PARTIAL:
      return "~";
    case CONSENT_STATUS.PENDING:
      return "?";
    default:
      return "";
  }
});

const openSettings = () => {
  showTooltip.value = false;
  emit("open-settings");
};

onUnmounted(() => {
  if (showButtonTimer) clearTimeout(showButtonTimer);
  if (hideTooltipTimer) clearTimeout(hideTooltipTimer);
});
</script>
