<template>
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed inset-0 z-50 overflow-y-auto bg-black/70"
      @click.self="closeModal"
    >
      <div class="flex min-h-full items-center justify-center p-4">
        <transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="show"
            class="liquid-glass rounded-3xl w-full max-w-2xl shadow-2xl border border-white/20"
          >
            <!-- Header -->
            <div
              class="flex items-center justify-between p-6 border-b border-white/10"
            >
              <h2 class="text-xl font-bold text-white">
                {{ cookieTrans("settings") }}
              </h2>
              <button
                @click="closeModal"
                class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-colors"
              >
                <i class="pi pi-times"></i>
              </button>
            </div>

            <!-- Content -->
            <div class="p-6 max-h-96 overflow-y-auto backdrop-blur-3xl">
              <p class="text-white/70 text-sm mb-6 leading-relaxed">
                {{ cookieTrans("description") }}
              </p>

              <!-- Cookie Categories -->
              <div class="space-y-6">
                <!-- Essential Cookies -->
                <div class="border border-white/10 rounded-xl p-4">
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-10 h-10 gradient-color rounded-lg flex items-center justify-center"
                      >
                        <i class="pi pi-shield-check text-white text-sm"></i>
                      </div>
                      <div>
                        <h3 class="font-semibold text-white text-sm">
                          {{ cookieTrans("essential_cookies") }}
                        </h3>
                        <p class="text-white/50 text-xs">
                          {{ cookieTrans("always_active") }}
                        </p>
                      </div>
                    </div>
                    <div
                      class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-medium rounded-full"
                    >
                      {{ cookieTrans("always_active") }}
                    </div>
                  </div>
                  <p class="text-white/60 text-xs leading-relaxed mb-3">
                    {{ cookieTrans("essential_description") }}
                  </p>
                  <button
                    @click="toggleCookieDetails('essential')"
                    class="text-xs text-blue-400 hover:text-blue-300 transition-colors"
                  >
                    {{ showDetails.essential ? "Hide" : "View" }} cookies ({{
                      essentialCookies.length
                    }})
                  </button>
                  <div v-if="showDetails.essential" class="mt-3 space-y-1">
                    <div
                      v-for="cookie in essentialCookies"
                      :key="cookie.name"
                      class="flex justify-between items-center text-xs bg-white/5 p-2 rounded"
                    >
                      <span class="font-mono text-white/80">{{
                        cookie.name
                      }}</span>
                      <span class="text-white/50 truncate max-w-32">{{
                        cookie.value
                      }}</span>
                    </div>
                  </div>
                </div>

                <!-- Functional Cookies -->
                <div class="border border-white/10 rounded-xl p-4">
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-10 h-10 gradient-color rounded-lg flex items-center justify-center"
                      >
                        <i class="pi pi-cog text-white text-sm"></i>
                      </div>
                      <div>
                        <h3 class="font-semibold text-white text-sm">
                          {{ cookieTrans("functional_cookies") }}
                        </h3>
                        <p class="text-white/50 text-xs">
                          {{
                            localPreferences.functional
                              ? cookieTrans("on")
                              : cookieTrans("off")
                          }}
                        </p>
                      </div>
                    </div>
                    <CookieToggle
                      v-model="localPreferences.functional"
                      :disabled="false"
                    />
                  </div>
                  <p class="text-white/60 text-xs leading-relaxed mb-3">
                    {{ cookieTrans("functional_description") }}
                  </p>
                  <button
                    @click="toggleCookieDetails('functional')"
                    class="text-xs text-blue-400 hover:text-blue-300 transition-colors"
                  >
                    {{ showDetails.functional ? "Hide" : "View" }} cookies ({{
                      functionalCookies.length
                    }})
                  </button>
                  <div v-if="showDetails.functional" class="mt-3 space-y-1">
                    <div
                      v-for="cookie in functionalCookies"
                      :key="cookie.name"
                      class="flex justify-between items-center text-xs bg-white/5 p-2 rounded"
                    >
                      <span class="font-mono text-white/80">{{
                        cookie.name
                      }}</span>
                      <span class="text-white/50 truncate max-w-32">{{
                        cookie.value
                      }}</span>
                    </div>
                  </div>
                </div>

                <!-- Analytics Cookies -->
                <div class="border border-white/10 rounded-xl p-4">
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-10 h-10 gradient-color rounded-lg flex items-center justify-center"
                      >
                        <i class="pi pi-chart-line text-white text-sm"></i>
                      </div>
                      <div>
                        <h3 class="font-semibold text-white text-sm">
                          {{ cookieTrans("analytics_cookies") }}
                        </h3>
                        <p class="text-white/50 text-xs">
                          {{
                            localPreferences.analytics
                              ? cookieTrans("on")
                              : cookieTrans("off")
                          }}
                        </p>
                      </div>
                    </div>
                    <CookieToggle
                      v-model="localPreferences.analytics"
                      :disabled="false"
                    />
                  </div>
                  <p class="text-white/60 text-xs leading-relaxed mb-3">
                    {{ cookieTrans("analytics_description") }}
                  </p>
                  <button
                    @click="toggleCookieDetails('analytics')"
                    class="text-xs text-blue-400 hover:text-blue-300 transition-colors"
                  >
                    {{ showDetails.analytics ? "Hide" : "View" }} cookies ({{
                      analyticsCookies.length
                    }})
                  </button>
                  <div v-if="showDetails.analytics" class="mt-3 space-y-1">
                    <div
                      v-for="cookie in analyticsCookies"
                      :key="cookie.name"
                      class="flex justify-between items-center text-xs bg-white/5 p-2 rounded"
                    >
                      <span class="font-mono text-white/80">{{
                        cookie.name
                      }}</span>
                      <span class="text-white/50 truncate max-w-32">{{
                        cookie.value
                      }}</span>
                    </div>
                  </div>
                </div>

                <!-- Marketing Cookies -->
                <div class="border border-white/10 rounded-xl p-4">
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-10 h-10 gradient-color rounded-lg flex items-center justify-center"
                      >
                        <i class="pi pi-bullseye text-white text-sm"></i>
                      </div>
                      <div>
                        <h3 class="font-semibold text-white text-sm">
                          {{ cookieTrans("marketing_cookies") }}
                        </h3>
                        <p class="text-white/50 text-xs">
                          {{
                            localPreferences.marketing
                              ? cookieTrans("on")
                              : cookieTrans("off")
                          }}
                        </p>
                      </div>
                    </div>
                    <CookieToggle
                      v-model="localPreferences.marketing"
                      :disabled="false"
                    />
                  </div>
                  <p class="text-white/60 text-xs leading-relaxed mb-3">
                    {{ cookieTrans("marketing_description") }}
                  </p>
                  <button
                    @click="toggleCookieDetails('marketing')"
                    class="text-xs text-blue-400 hover:text-blue-300 transition-colors"
                  >
                    {{ showDetails.marketing ? "Hide" : "View" }} cookies ({{
                      marketingCookies.length
                    }})
                  </button>
                  <div v-if="showDetails.marketing" class="mt-3 space-y-1">
                    <div
                      v-for="cookie in marketingCookies"
                      :key="cookie.name"
                      class="flex justify-between items-center text-xs bg-white/5 p-2 rounded"
                    >
                      <span class="font-mono text-white/80">{{
                        cookie.name
                      }}</span>
                      <span class="text-white/50 truncate max-w-32">{{
                        cookie.value
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Audit Trail Section -->
              <div class="mt-6 pt-6 border-t border-white/10">
                <CookieAuditTrail />
              </div>

              <!-- Compliance Section -->
              <div class="mt-6 pt-6 border-t border-white/10">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                  <button
                    @click="viewCookiePolicy"
                    class="flex items-center gap-2 text-blue-400 hover:text-blue-300 transition-colors"
                  >
                    <i class="pi pi-file-text"></i>
                    {{ cookieTrans("cookie_policy") }}
                  </button>
                  <button
                    @click="viewPrivacyPolicy"
                    class="flex items-center gap-2 text-blue-400 hover:text-blue-300 transition-colors"
                  >
                    <i class="pi pi-shield-check"></i>
                    {{ cookieTrans("privacy_policy") }}
                  </button>
                  <button
                    @click="withdrawConsent"
                    class="flex items-center gap-2 text-red-400 hover:text-red-300 transition-colors"
                  >
                    <i class="pi pi-times-circle"></i>
                    {{ cookieTrans("withdraw_consent") }}
                  </button>
                  <button
                    @click="doNotSell"
                    class="flex items-center gap-2 text-yellow-400 hover:text-yellow-300 transition-colors"
                  >
                    <i class="pi pi-ban"></i>
                    {{ cookieTrans("do_not_sell") }}
                  </button>
                </div>
              </div>

              <!-- Consent Info -->
              <div class="mt-4 p-3 bg-white/5 rounded-lg">
                <div
                  class="flex justify-between items-center text-xs text-white/60"
                >
                  <span
                    >{{ cookieTrans("expires_in") }}: {{ daysUntilExpiry }}
                    {{ cookieTrans("days") }}</span
                  >
                  <span
                    >{{ cookieTrans("last_updated") }}: {{ lastUpdated }}</span
                  >
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div
              class="flex flex-col sm:flex-row gap-3 p-6 border-t border-white/10"
            >
              <button
                @click="acceptAll"
                class="flex-1 px-4 py-3 gradient-color text-white font-medium text-sm rounded-xl hover:opacity-90 transition-opacity"
              >
                {{ cookieTrans("accept") }}
              </button>
              <button
                @click="declineAll"
                class="flex-1 px-4 py-3 bg-white/10 text-white font-medium text-sm rounded-xl hover:bg-white/20 transition-colors"
              >
                {{ cookieTrans("decline") }}
              </button>
              <button
                @click="savePreferences"
                class="flex-1 px-4 py-3 bg-blue-600 text-white font-medium text-sm rounded-xl hover:bg-blue-700 transition-colors"
              >
                {{ cookieTrans("save_preferences") }}
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import { useCookieTranslations } from "@/composables/useCookieTranslations";
import { useCookieManager, COOKIE_TYPES } from "@/composables/useCookieManager";
import CookieToggle from "./CookieToggle.vue";
import CookieAuditTrail from "./CookieAuditTrail.vue";

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close", "save"]);

const { cookieTrans } = useCookieTranslations();
const {
  preferences,
  getCookiesByCategory,
  acceptAllCookies,
  declineAllCookies,
  saveCustomPreferences,
  withdrawConsent: withdrawCookieConsent,
  getDaysUntilExpiry,
} = useCookieManager();

// Local state
const localPreferences = reactive({
  functional: preferences.functional,
  analytics: preferences.analytics,
  marketing: preferences.marketing,
});

const showDetails = reactive({
  essential: false,
  functional: false,
  analytics: false,
  marketing: false,
});

// Watch for changes in global preferences
watch(
  () => preferences,
  (newPrefs) => {
    Object.assign(localPreferences, {
      functional: newPrefs.functional,
      analytics: newPrefs.analytics,
      marketing: newPrefs.marketing,
    });
  },
  { deep: true }
);

// Cookie categories computed
const cookiesByCategory = computed(() => getCookiesByCategory());
const essentialCookies = computed(
  () => cookiesByCategory.value.essential || []
);
const functionalCookies = computed(
  () => cookiesByCategory.value.functional || []
);
const analyticsCookies = computed(
  () => cookiesByCategory.value.analytics || []
);
const marketingCookies = computed(
  () => cookiesByCategory.value.marketing || []
);

// Computed properties
const daysUntilExpiry = computed(() => getDaysUntilExpiry());
const lastUpdated = computed(() => {
  const consent = localStorage.getItem("cookie_consent");
  if (consent) {
    try {
      const data = JSON.parse(consent);
      return new Date(data.timestamp).toLocaleDateString();
    } catch (e) {
      return "Unknown";
    }
  }
  return "Not set";
});

// Methods
const closeModal = () => {
  emit("close");
};

const toggleCookieDetails = (category) => {
  showDetails[category] = !showDetails[category];
};

const acceptAll = () => {
  acceptAllCookies();
  emit("save");
  closeModal();
};

const declineAll = () => {
  declineAllCookies();
  emit("save");
  closeModal();
};

const savePreferences = () => {
  saveCustomPreferences({
    [COOKIE_TYPES.FUNCTIONAL]: localPreferences.functional,
    [COOKIE_TYPES.ANALYTICS]: localPreferences.analytics,
    [COOKIE_TYPES.MARKETING]: localPreferences.marketing,
  });
  emit("save");
  closeModal();
};

const withdrawConsent = () => {
  if (confirm(cookieTrans("withdraw_consent_confirm"))) {
    withdrawCookieConsent();
    emit("save");
    closeModal();
  }
};

const doNotSell = () => {
  // Implement CCPA compliance
  localPreferences.marketing = false;
  localPreferences.analytics = false;
  savePreferences();
};

const viewCookiePolicy = () => {
  window.open("/cookie-policy", "_blank");
};

const viewPrivacyPolicy = () => {
  window.open("/?section=privacy", "_blank");
};
</script>
