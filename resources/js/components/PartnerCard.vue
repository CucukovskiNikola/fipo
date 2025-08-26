<template>
  <Card
    class="hover:shadow-md transition overflow-hidden cursor-pointer"
    @click="navigateToPartner"
  >
    <!-- Partner Image -->
    <div class="relative h-55 bg-gray-800 rounded-xl overflow-hidden">
      <img
        v-if="displayImage"
        :src="displayImage"
        :alt="partner.title"
        class="w-full h-full object-cover shadow-2xl rounded-xl transition-opacity duration-500"
        loading="lazy"
        decoding="async"
        @error="handleImageError"
      />

      <div
        v-else
        class="w-full h-full flex items-center justify-center text-gray-400"
        aria-label="No image available"
        role="img"
      >
        <svg
          class="w-12 h-12"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          aria-hidden="true"
          focusable="false"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
          />
        </svg>
      </div>
    </div>

    <CardContent class="p-2">
      <div class="flex items-center justify-between">
        <span
          class="inline-flex items-center px-3 py-1 border bg-white/10 backdrop-blur-sm border-white/20 text-white text-xs rounded-2xl"
        >
          <span>{{ categoryInfo.icon }}</span>
          {{ categoryInfo.name }}
        </span>
        <!-- Translation indicator -->
        <div
          v-if="shouldTranslate && isTranslating"
          class="inline-flex items-center px-2 py-1 bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs rounded-lg"
        >
          <svg
            class="animate-spin -ml-1 mr-1 h-3 w-3 text-blue-300"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          Translating...
        </div>
      </div>
      <div class="my-3">
        <h1 class="font-semibold text-lg text-white line-clamp-2 min-h-14">
          {{ partner.title }}
        </h1>
      </div>

      <p class="text-sm text-white mb-3 line-clamp-3 min-h-16">
        {{ displayDescription }}
      </p>

      <div class="space-y-1 text-xs text-white">
        <div class="flex items-center line-clamp-1">
          <svg
            class="w-6 h-6 mr-1"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
            />
          </svg>
          {{ partner.city }}, {{ partner.zip_code }}
        </div>
      </div>
    </CardContent>
  </Card>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { Card, CardContent } from "@/components/ui/card";
import { useCategories } from "@/composables/useCategories";
import { useLibreTranslate } from "@/composables/useLibreTranslate";
import { getLocalizedPartnerUrl } from "@/lib/utils";

const props = defineProps({
  partner: Object,
  enableTranslation: {
    type: Boolean,
    default: false,
  },
});

// Get current page data to access locale
const page = usePage();

// Translation composable
const { translateText, autoTranslate, isTranslating } = useLibreTranslate();

// Categories composable
const { categories, getCategoryName } = useCategories();
// Translated text refs
const translatedDescription = ref("");
const translatedCategoryName = ref("");

// Compute whether translation should be enabled based on locale or manual prop
const shouldTranslate = computed(() => {
  const currentLocale = page.props.locale || "de";
  return props.enableTranslation || currentLocale === "en";
});

// Function to perform translations
const performTranslations = async () => {
  if (shouldTranslate.value) {
    translatedDescription.value = await autoTranslate(
      props.partner.description
    );

    // Translate category name if it's German
    const category = categories.value.find(
      (cat) => cat.id === props.partner.category
    );
    if (category) {
      translatedCategoryName.value = await autoTranslate(category.name);
    }
  } else {
    // Clear translations when disabled
    translatedDescription.value = "";
    translatedCategoryName.value = "";
  }
};

// Initialize translations on mount
onMounted(() => {
  performTranslations();
});

// Watch for changes to enableTranslation prop or locale
watch(
  [() => props.enableTranslation, () => page.props.locale],
  ([newTranslationValue, newLocale]) => {
    console.log(
      "Translation state changed - enableTranslation:",
      newTranslationValue,
      "locale:",
      newLocale
    );
    performTranslations();
  }
);

const categoryInfo = computed(() => {
  const category = categories.value.find(
    (cat) => cat.id === props.partner.category
  );
  const name =
    shouldTranslate.value && translatedCategoryName.value
      ? translatedCategoryName.value
      : category
        ? category.name
        : props.partner.category;

  return category ? { icon: category.icon, name } : { icon: "📍", name };
});

const displayDescription = computed(() => {
  return shouldTranslate.value && translatedDescription.value
    ? translatedDescription.value
    : props.partner.description;
});

const displayImage = computed(() => {
  if (props.partner.images && props.partner.images.length > 0) {
    return `/storage/${props.partner.images[0].path}`;
  }
  if (props.partner.image) {
    return props.partner.image.startsWith("http")
      ? props.partner.image
      : `/storage/${props.partner.image}`;
  }
  return null;
});

const handleImageError = (event) => {
  event.target.style.display = "none";
};

const navigateToPartner = () => {
  if (!props.partner.id) return;

  const currentLocale = page.props.locale || "de";
  const finalUrl = getLocalizedPartnerUrl(
    props.partner.id,
    props.partner.title,
    currentLocale
  );
  router.visit(finalUrl);
};
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
