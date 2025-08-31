<template>
  <!-- Main Consent Banner -->
  <transition
    enter-active-class="transition ease-out duration-500"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-4"
  >
    <div
      v-if="showConsent"
      class="fixed bottom-4 left-4 right-4 z-50 backdrop-blur-2xl max-w-lg mx-auto md:left-auto md:right-4 md:max-w-md"
    >
      <div
        class="liquid-glass rounded-2xl p-6 shadow-xl border border-white/20"
      >
        <div class="flex items-start gap-3 mb-4">
          <div
            class="w-10 h-10 gradient-color rounded-full flex items-center justify-center flex-shrink-0"
          >
            <Icon name="cookie" class="h-6 w-6" />
          </div>
          <div>
            <h3 class="text-white font-semibold text-sm mb-2">
              {{ cookieTrans("title") }}
            </h3>
            <p class="text-white/70 text-xs leading-relaxed">
              {{ cookieTrans("description") }}
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2">
          <button
            @click="declineAllCookies"
            class="px-3 py-2 text-xs font-medium text-white/70 hover:text-white transition-colors rounded-lg hover:bg-white/5 border border-white/20"
          >
            {{ cookieTrans("decline") }}
          </button>
          <button
            @click="openSettings"
            class="px-3 py-2 text-xs font-medium text-blue-400 hover:text-blue-300 transition-colors rounded-lg hover:bg-blue-500/10 border border-blue-500/30"
          >
            {{ cookieTrans("settings") }}
          </button>
          <button
            @click="acceptAllCookies"
            class="px-4 py-2 text-xs font-medium text-white gradient-color rounded-lg hover:opacity-90 transition-opacity shadow-sm"
          >
            {{ cookieTrans("accept") }}
          </button>
        </div>

        <!-- Compliance Links -->
        <div
          class="flex justify-center gap-4 mt-3 pt-3 border-t border-white/10"
        >
          <a
            href="/?section=privacy"
            class="text-xs text-white/50 hover:text-white/70 transition-colors"
            target="_blank"
          >
            {{ cookieTrans("privacy_policy") }}
          </a>
          <button
            @click="showDoNotSell"
            class="text-xs text-white/50 hover:text-white/70 transition-colors"
          >
            {{ cookieTrans("do_not_sell") }}
          </button>
        </div>
      </div>
    </div>
  </transition>

  <!-- Cookie Settings Modal -->
  <CookieSettingsModal
    :show="showSettingsModal"
    @close="showSettingsModal = false"
    @save="handleSettingsSave"
  />

  <!-- Floating Settings Button -->
  <CookieSettingsButton @open-settings="openSettings" />

  <!-- Do Not Sell Modal -->
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="showDoNotSellModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
      @click.self="showDoNotSellModal = false"
    >
      <div
        class="liquid-glass rounded-2xl p-6 max-w-sm w-full border border-white/20"
      >
        <div class="text-center">
          <div
            class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i class="pi pi-exclamation-triangle text-yellow-400 text-xl"></i>
          </div>
          <h3 class="text-white font-semibold mb-2">CCPA Compliance</h3>
          <p class="text-white/70 text-sm mb-4">
            Do not sell my personal information. This will disable marketing and
            analytics cookies.
          </p>
          <div class="flex gap-2">
            <button
              @click="showDoNotSellModal = false"
              class="flex-1 px-3 py-2 text-sm text-white/70 hover:text-white transition-colors rounded-lg hover:bg-white/5"
            >
              Cancel
            </button>
            <button
              @click="handleDoNotSell"
              class="flex-1 px-3 py-2 text-sm text-white bg-yellow-500 hover:bg-yellow-600 transition-colors rounded-lg"
            >
              Confirm
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useCookieTranslations } from "@/composables/useCookieTranslations";
import {
  useCookieManager,
  CONSENT_STATUS,
  COOKIE_TYPES,
} from "@/composables/useCookieManager";
import CookieSettingsModal from "./CookieSettingsModal.vue";
import CookieSettingsButton from "./CookieSettingsButton.vue";
import Icon from "./Icon.vue";

const { cookieTrans } = useCookieTranslations();
const {
  consentStatus,
  acceptAllCookies: acceptAll,
  declineAllCookies: declineAll,
  saveCustomPreferences,
} = useCookieManager();

const showConsent = ref(false);
const showSettingsModal = ref(false);
const showDoNotSellModal = ref(false);

onMounted(() => {
  // Check if user has already made a choice
  if (consentStatus.value === CONSENT_STATUS.PENDING) {
    // Delay showing the banner slightly for better UX
    setTimeout(() => {
      showConsent.value = true;
    }, 1000);
  }
});

const acceptAllCookies = () => {
  acceptAll();
  showConsent.value = false;
};

const declineAllCookies = () => {
  declineAll();
  showConsent.value = false;
};

const openSettings = () => {
  showSettingsModal.value = true;
  showConsent.value = false;
};

const handleSettingsSave = () => {
  showConsent.value = false;
  showSettingsModal.value = false;
};

const showDoNotSell = () => {
  showDoNotSellModal.value = true;
};

const handleDoNotSell = () => {
  // Disable marketing and analytics cookies (CCPA compliance)
  saveCustomPreferences({
    [COOKIE_TYPES.FUNCTIONAL]: true, // Keep functional
    [COOKIE_TYPES.ANALYTICS]: false,
    [COOKIE_TYPES.MARKETING]: false,
  });

  showDoNotSellModal.value = false;
  showConsent.value = false;
};
</script>
