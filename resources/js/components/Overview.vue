<template>
  <transition
    enter-active-class="transition ease-out duration-500"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-4"
  >
    <section
      v-if="isActive"
      class="liquid-glass text-white max-w-6xl mx-auto rounded-4xl p-6 lg:p-10 mt-4 shadow-lg"
    >
      <HeroSection @navigate-to-contact="$emit('navigate-to-contact')" />

      <div class="mb-6">
        <SearchFilters
          v-model:searchTerm="searchTerm"
          v-model:selectedCategories="selectedCategories"
          v-model:selectedCities="selectedCities"
          :categories="categories"
          :cities="cities"
          @resetFilters="resetFilters"
        />
      </div>

      <!-- Partners Section -->
      <div>
        <div class="flex justify-end items-center mb-4">
          <span v-if="!loading && totalPartners > 0" class="text-sm text-white">
            {{
              trans("home.showing_partners", {
                showing: partners.length,
                total: totalPartners,
              })
            }}
          </span>
          <span v-if="loading" class="text-sm text-gray-500">{{
            trans("home.loading")
          }}</span>
        </div>

        <div v-if="!loading && partners.length > 0" class="space-y-6">
          <!-- Partners Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <PartnerCard
              v-for="(partner, index) in partners"
              :key="partner.id"
              :partner="partner"
              :index="index"
            />
          </div>

          <!-- Load More Button -->
          <div v-if="hasMore" class="text-center pt-4 pb-6">
            <Button
              @click="loadMorePartners"
              :disabled="loadingMore"
              :loading="loadingMore"
              class="!rounded-4xl px-8 py-3 w-full lg:w-[40%] cursor-pointer"
              size="large"
              variant="gradient"
            >
              <i v-if="!loadingMore" class="pi pi-plus mr-2"></i>
              {{
                loadingMore
                  ? trans("home.loading")
                  : trans("home.load_more") +
                    ` (${totalPartners - partners.length} remaining)`
              }}
            </Button>
          </div>

          <!-- All Loaded Message -->
          <div
            v-else-if="partners.length > 12"
            class="text-center pt-4 text-sm text-white"
          >
            <i class="pi pi-check mr-2"></i>
            {{ trans("home.all_partners_loaded", { total: totalPartners }) }}
          </div>
        </div>

        <div
          v-else-if="!loading && partners.length === 0"
          class="text-center py-8 text-white"
        >
          <div class="flex flex-col items-center">
            <i class="pi pi-search text-4xl mb-4 text-gray-300"></i>
            <p class="text-lg font-medium mb-2">
              {{ trans("home.no_partners_found") }}
            </p>
            <p class="text-sm">
              {{ trans("home.try_different_filters") }}
            </p>
          </div>
        </div>

        <div
          v-if="loading"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <PartnerCardSkeleton v-for="n in 6" :key="n" />
        </div>
      </div>
    </section>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import PartnerCard from "./PartnerCard.vue";
import PartnerCardSkeleton from "./PartnerCardSkeleton.vue";
import SearchFilters from "./SearchFilters.vue";
import HeroSection from "./HeroSection.vue";
import Button from "./ui/button/Button.vue";
import { useTranslations } from "@/composables/useTranslations";

const props = defineProps({
  isActive: Boolean,
});

const { trans } = useTranslations();

// Partners data
const partners = ref([]);
const searchTerm = ref("");
const activeFilter = ref("all");
const selectedCategories = ref([]);
const selectedCities = ref([]);
const categories = ref([]);
const cities = ref([]);
const allCategories = ref([]);
const allCities = ref([]);
const loading = ref(false);
const loadingMore = ref(false);
const currentPage = ref(1);
const hasMore = ref(true);
const totalPartners = ref(0);

// Computed options for dropdowns
const categoryOptions = computed(() =>
  categories.value.map((cat) => ({
    label: cat.charAt(0).toUpperCase() + cat.slice(1),
    value: cat,
  }))
);

const cityOptions = computed(() =>
  cities.value.map((city) => ({ label: city, value: city }))
);

const hasActiveFilters = computed(
  () =>
    searchTerm.value ||
    selectedCategories.value.length > 0 ||
    selectedCities.value.length > 0
);

// Fetch partners from API
const fetchPartners = async (reset = true) => {
  if (reset) {
    loading.value = true;
    currentPage.value = 1;
    partners.value = [];
  } else {
    loadingMore.value = true;
  }

  try {
    const params = new URLSearchParams();
    if (searchTerm.value) params.append("search", searchTerm.value);
    if (selectedCategories.value.length > 0) {
      selectedCategories.value.forEach((category) => {
        params.append("categories[]", category);
      });
    }
    if (selectedCities.value.length > 0) {
      selectedCities.value.forEach((city) => {
        params.append("cities[]", city);
      });
    }
    params.append("page", currentPage.value.toString());
    params.append("per_page", "12");

    const url = `/api/partners?${params.toString()}`;
    const response = await fetch(url);
    const data = await response.json();

    if (reset) {
      partners.value = data.data;
    } else {
      partners.value = [...partners.value, ...data.data];
    }

    hasMore.value = data.has_more;
    totalPartners.value = data.total;

    // Extract unique categories and cities for filters (only on initial load without filters)
    if (currentPage.value === 1 && data.categories && data.cities) {
      // Only update if we don't have categories/cities yet (initial load)
      if (allCategories.value.length === 0 && allCities.value.length === 0) {
        allCategories.value = data.categories;
        allCities.value = data.cities;
      }
    }

    // Always use the full list for display
    categories.value = allCategories.value;
    cities.value = allCities.value;
  } catch (error) {
    console.error("Error fetching partners:", error);
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};

// Load more partners
const loadMorePartners = async () => {
  if (!hasMore.value || loadingMore.value) return;

  currentPage.value += 1;
  await fetchPartners(false);
};

// Watch for search changes with debounce
let searchTimeout;
watch(searchTerm, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchPartners(true);
  }, 300);
});

watch(
  [selectedCategories, selectedCities],
  () => {
    currentPage.value = 1;
    fetchPartners(true);
  },
  { deep: true }
);

onMounted(() => {
  fetchPartners(true);
});

// Filter handlers - now handled by v-model

const resetFilters = () => {
  searchTerm.value = "";
  selectedCategories.value = [];
  selectedCities.value = [];
  activeFilter.value = "all";
  currentPage.value = 1;
  fetchPartners(true);
};
</script>
