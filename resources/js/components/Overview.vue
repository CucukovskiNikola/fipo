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
      class="bg-white text-black max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
    >

      <!-- What We Do Section -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">What We Do</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
          <CardItem v-for="(card, index) in aboutCards" :key="`about-${index}`" :card="card" />
        </div>
      </div>

      <!-- Search & Filters -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Find Partners</h3>
        <div class="flex flex-col gap-4">
          <!-- Search Bar -->
          <div class="w-full">
            <IconField>
              <InputIcon class="pi pi-search" />
              <InputText 
                v-model="searchTerm" 
                placeholder="Search partners by name, category, or city..." 
                class="w-full !rounded-4xl"
                size="large"
              />
            </IconField>
          </div>

          <!-- Filters Row -->
          <div class="flex flex-wrap gap-3 items-center">
            <Button
              @click="resetFilters"
              :severity="activeFilter === 'all' ? 'primary' : 'secondary'"
              :outlined="activeFilter !== 'all'"
              size="small"
              class="!rounded-4xl min-w-48"
              :style="{ backgroundColor: activeFilter === 'all' ? '#1a0d05' : '', borderColor: activeFilter === 'all' ? '#1a0d05' : '' }"
            >
              All Partners
            </Button>
            
            <div v-if="categories.length > 0">
              <Select
                v-model="selectedCategory"
                :options="categoryOptions"
                option-label="label"
                option-value="value"
                placeholder="Select Category"
                class="min-w-48 !rounded-4xl custom-select-brown"
                show-clear
                @change="onCategoryChange"
              >
                <template #option="{ option }">
                  <div class="flex items-center">
                    <span class="mr-2">{{ getCategoryIcon(option.value) }}</span>
                    {{ option.label }}
                  </div>
                </template>
              </Select>
            </div>
            
            <div v-if="cities.length > 0">
              <Select
                v-model="selectedCity"
                :options="cityOptions"
                option-label="label"
                option-value="value"
                placeholder="Select City"
                class="min-w-48 !rounded-4xl custom-select-brown"
                show-clear
                @change="onCityChange"
              />
            </div>
            
            <Button
              v-if="hasActiveFilters"
              @click="resetFilters"
              severity="secondary"
              size="small"
              icon="pi pi-times"
              outlined
              class="min-w-48 !rounded-4xl"
              :style="{ borderColor: '#1a0d05', color: '#1a0d05' }"
            >
              Reset Filters
            </Button>
          </div>
        </div>
      </div>

      <!-- Partners Section -->
      <div>
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Our Partners</h3>
          <span v-if="!loading && totalPartners > 0" class="text-sm text-gray-500">
            Showing {{ partners.length }} of {{ totalPartners }} partners
          </span>
          <span v-if="loading" class="text-sm text-gray-500">Loading...</span>
        </div>
        
        <div v-if="!loading && partners.length > 0" class="space-y-6">
          <!-- Partners Grid -->
          <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <PartnerCard v-for="partner in partners" :key="partner.id" :partner="partner" />
          </div>
          
          <!-- Load More Button -->
          <div v-if="hasMore" class="text-center pt-4">
            <Button
              @click="loadMorePartners"
              :disabled="loadingMore"
              :loading="loadingMore"
              class="!rounded-4xl px-8 py-3"
              size="large"
              outlined
              :style="{ borderColor: '#1a0d05', color: '#1a0d05' }"
            >
              <i v-if="!loadingMore" class="pi pi-plus mr-2"></i>
              {{ loadingMore ? 'Loading...' : `Load More Partners (${totalPartners - partners.length} remaining)` }}
            </Button>
          </div>
          
          <!-- All Loaded Message -->
          <div v-else-if="partners.length > 12" class="text-center pt-4 text-sm text-gray-500">
            <i class="pi pi-check mr-2"></i>
            All {{ totalPartners }} partners loaded
          </div>
        </div>
        
        <div v-else-if="!loading && partners.length === 0" class="text-center py-8 text-gray-500">
          <div class="flex flex-col items-center">
            <i class="pi pi-search text-4xl mb-4 text-gray-300"></i>
            <p class="text-lg font-medium mb-2">No partners found</p>
            <p class="text-sm">Try adjusting your search criteria or filters.</p>
          </div>
        </div>
        
        <div v-if="loading" class="text-center py-8">
          <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading partners...
          </div>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="flex justify-between text-xs text-gray-500 mt-6">
        <div class="space-x-4">
          <a href="#" class="hover:underline">World's Healthiest</a>
          <a href="#" class="hover:underline">The Founder Health Coalition</a>
        </div>
        <div class="space-x-4">
          <a href="#" class="hover:underline">Privacy Policy</a>
          <a href="#" class="hover:underline">Informed Medical Consent</a>
          <a href="#" class="hover:underline">Terms & Conditions</a>
        </div>
      </div>
    </section>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import CardItem from "./CardItem.vue";
import PartnerCard from "./PartnerCard.vue";
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import categoriesData from '@/data/categories.json';

const props = defineProps({
  isActive: Boolean,
});

// Static cards about the business
const aboutCards = [
  {
    title: "Find Partners",
    description: "Discover local businesses and partners in your area to collaborate and grow together.",
    image:
      "https://images.unsplash.com/photo-1521737711867-e3b97375f902?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
  },
  {
    title: "Promote Your Business",
    description: "List your business and reach potential customers in your local community.",
    image:
      "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
  },
  {
    title: "Local Network",
    description: "Build connections with local entrepreneurs and expand your business network.",
    image:
      "https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
  },
  {
    title: "Business Growth",
    description: "Access resources and opportunities to help your business thrive in the local market.",
    image:
      "https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
  },
];

// Partners data
const partners = ref([]);
const searchTerm = ref("");
const activeFilter = ref("all");
const selectedCategory = ref(null);
const selectedCity = ref(null);
const categories = ref([]);
const cities = ref([]);
const loading = ref(false);
const loadingMore = ref(false);
const currentPage = ref(1);
const hasMore = ref(true);
const totalPartners = ref(0);

// Computed options for dropdowns
const categoryOptions = computed(() => 
  categories.value.map(cat => ({ label: cat.charAt(0).toUpperCase() + cat.slice(1), value: cat }))
);

const cityOptions = computed(() => 
  cities.value.map(city => ({ label: city, value: city }))
);

const hasActiveFilters = computed(() => 
  searchTerm.value || selectedCategory.value || selectedCity.value
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
    if (searchTerm.value) params.append('search', searchTerm.value);
    if (selectedCategory.value) params.append('category', selectedCategory.value);
    if (selectedCity.value) params.append('city', selectedCity.value);
    params.append('page', currentPage.value.toString());
    params.append('per_page', '12');

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

    // Extract unique categories and cities for filters (only on initial load)
    if (currentPage.value === 1 && data.categories && data.cities) {
      categories.value = data.categories;
      cities.value = data.cities;
    }
  } catch (error) {
    console.error('Error fetching partners:', error);
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

watch([selectedCategory, selectedCity], () => {
  currentPage.value = 1;
  fetchPartners(true);
});

onMounted(() => {
  fetchPartners(true);
});

// Filter handlers
const onCategoryChange = () => {
  currentPage.value = 1;
  fetchPartners(true);
};

const onCityChange = () => {
  currentPage.value = 1;
  fetchPartners(true);
};

const resetFilters = () => {
  searchTerm.value = "";
  selectedCategory.value = null;
  selectedCity.value = null;
  activeFilter.value = "all";
  currentPage.value = 1;
  fetchPartners(true);
};

// Get category icon
const getCategoryIcon = (categoryId) => {
  const category = categoriesData.find(cat => cat.id === categoryId);
  return category ? category.icon : '📍';
};
</script>

<style scoped>
/* Custom styling for PrimeVue Select components with brown theme */
:deep(.custom-select-brown .p-select-dropdown) {
  border-color: #d1d5db;
}

:deep(.custom-select-brown .p-select-dropdown:hover) {
  border-color: #1a0d05;
}

:deep(.custom-select-brown .p-select-dropdown:focus) {
  border-color: #1a0d05;
  box-shadow: 0 0 0 0.2rem rgba(26, 13, 5, 0.2);
}

:deep(.custom-select-brown .p-select-option:hover) {
  background-color: rgba(26, 13, 5, 0.1);
}

:deep(.custom-select-brown .p-select-option.p-select-option-selected) {
  background-color: #1a0d05;
  color: white;
}

:deep(.custom-select-brown .p-select-option.p-select-option-selected:hover) {
  background-color: #1a0d05;
  color: white;
}

:deep(.custom-select-brown .p-select-clear-icon) {
  color: #1a0d05;
}

:deep(.custom-select-brown .p-select-dropdown-icon) {
  color: #1a0d05;
}
</style>
