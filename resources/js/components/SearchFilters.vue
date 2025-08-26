<template>
  <div class="flex gap-3 items-center">
    <!-- Search Bar -->
    <div class="flex-1 relative">
      <Search
        class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-white"
      />
      <Input
        :model-value="searchTerm"
        @update:model-value="$emit('update:searchTerm', $event)"
        :placeholder="trans('home.search_placeholder')"
        class="w-full pl-10 pr-4 h-14 rounded-3xl bg-white/10 border border-white/10 backdrop-blur-sm text-white placeholder:text-white"
      />
    </div>

    <!-- Filter Popover -->
    <Popover v-model:open="isFilterOpen">
      <PopoverTrigger as-child>
        <Button
          variant="outline"
          aria-label="Filter"
          size="sm"
          class="h-14 w-14 cursor-pointer rounded-3xl bg-white/20 border border-white/10 backdrop-blur-sm text-white p-0 flex items-center justify-center"
        >
          <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
            />
          </svg>
        </Button>
      </PopoverTrigger>

      <PopoverContent
        class="w-80 p-4 rounded-3xl bg-gray-600 border border-white/10 text-white backdrop-blur-3xl space-y-4"
        align="end"
      >
        <!-- Header -->
        <div class="flex items-center justify-between">
          <h4 class="font-medium">{{ trans("home.categories") }}</h4>
          <Button
            v-if="hasActiveFilters"
            @click="handleResetFilters"
            variant="outline"
            size="sm"
            class="h-8 px-3 text-sm border"
          >
            <X class="h-3 w-3 mr-1" />
            {{ trans("home.reset_filters") }}
          </Button>
        </div>

        <!-- Categories Multi-select -->
        <div>
          <label class="text-sm font-medium mb-3 block">{{
            trans("home.categories")
          }}</label>
          <div class="space-y-3">
            <!-- All Categories Button -->
            <button
              @click="toggleCategory('')"
              class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm transition-all duration-200"
              :class="
                selectedCategories.length === 0
                  ? 'bg-gradient-to-r from-blue-500/30 to-purple-500/30 border border-blue-400/50 text-white shadow-lg shadow-blue-500/20'
                  : 'bg-white/5 border border-white/10 text-white/70 hover:bg-white/10 hover:text-white'
              "
            >
              <div class="flex items-center">
                <span class="mr-2">📁</span>
                <span>{{ trans("home.all_categories") }}</span>
              </div>
              <div
                v-if="selectedCategories.length === 0"
                class="w-2 h-2 bg-blue-400 rounded-full"
              ></div>
            </button>

            <!-- Category Chips Container -->
            <div class="max-h-48 overflow-y-scroll scrollbar-hide space-y-2">
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="category in categories"
                  :key="category.id"
                  @click="toggleCategory(category.id)"
                  class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-200 hover:scale-105"
                  :class="
                    selectedCategories.includes(category.id)
                      ? 'bg-gradient-to-r from-emerald-500/30 to-teal-500/30 border border-emerald-400/50 text-white shadow-lg shadow-emerald-500/20'
                      : 'bg-white/5 border border-white/20 text-white/70 hover:bg-white/10 hover:text-white hover:border-white/30'
                  "
                >
                  <span>{{ getLocalCategoryName(category.name) }}</span>
                  <div
                    v-if="selectedCategories.includes(category)"
                    class="ml-1.5 w-1.5 h-1.5 bg-emerald-400 rounded-full"
                  ></div>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Cities Multi-select -->
        <div>
          <label class="text-sm font-medium mb-3 block">{{
            trans("home.cities")
          }}</label>
          <div class="space-y-3">
            <!-- All Cities Button -->
            <button
              @click="toggleCity('')"
              class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm transition-all duration-200"
              :class="
                selectedCities.length === 0
                  ? 'bg-gradient-to-r from-blue-500/30 to-purple-500/30 border border-blue-400/50 text-white shadow-lg shadow-blue-500/20'
                  : 'bg-white/5 border border-white/10 text-white/70 hover:bg-white/10 hover:text-white'
              "
            >
              <div class="flex items-center">
                <MapPin class="mr-2 h-4 w-4" />
                <span>{{ trans("home.all_cities") }}</span>
              </div>
              <div
                v-if="selectedCities.length === 0"
                class="w-2 h-2 bg-blue-400 rounded-full"
              ></div>
            </button>

            <!-- City Chips Container -->
            <div class="max-h-48 overflow-y-scroll scrollbar-hide space-y-2">
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="city in cities"
                  :key="city"
                  @click="toggleCity(city)"
                  class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-200 hover:scale-105"
                  :class="
                    selectedCities.includes(city)
                      ? 'bg-gradient-to-r from-orange-500/30 to-red-500/30 border border-orange-400/50 text-white shadow-lg shadow-orange-500/20'
                      : 'bg-white/5 border border-white/20 text-white/70 hover:bg-white/10 hover:text-white hover:border-white/30'
                  "
                >
                  <MapPin class="mr-1.5 h-3 w-3" />
                  <span>{{ city }}</span>
                  <div
                    v-if="selectedCities.includes(city)"
                    class="ml-1.5 w-1.5 h-1.5 bg-orange-400 rounded-full"
                  ></div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Search, X, MapPin } from "lucide-vue-next";
import { useCategories } from "@/composables/useCategories";
import { useTranslations } from "@/composables/useTranslations";

const isFilterOpen = ref(false);
const { trans } = useTranslations();
const { categories, getCategoryName } = useCategories();

const props = defineProps({
  searchTerm: String,
  selectedCategories: {
    type: Array,
    default: () => [],
  },
  selectedCities: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
  cities: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits([
  "update:searchTerm",
  "update:selectedCategories",
  "update:selectedCities",
  "resetFilters",
]);

const hasActiveFilters = computed(() => {
  return (
    (props.searchTerm && props.searchTerm.trim().length > 0) ||
    (props.selectedCategories && props.selectedCategories.length > 0) ||
    (props.selectedCities && props.selectedCities.length > 0)
  );
});

const toggleCategory = (category) => {
  // If empty string (All Categories), reset selection
  if (category === "") {
    emit("update:selectedCategories", []);
    return;
  }

  const newSelection = [...props.selectedCategories];
  const index = newSelection.indexOf(category);
  if (index > -1) {
    newSelection.splice(index, 1);
  } else {
    newSelection.push(category);
  }
  emit("update:selectedCategories", newSelection);
};

const toggleCity = (city) => {
  // If empty string (All Cities), reset selection
  if (city === "") {
    emit("update:selectedCities", []);
    return;
  }

  const newSelection = [...props.selectedCities];
  const index = newSelection.indexOf(city);
  if (index > -1) {
    newSelection.splice(index, 1);
  } else {
    newSelection.push(city);
  }
  emit("update:selectedCities", newSelection);
};

const handleResetFilters = () => {
  emit("update:selectedCategories", []);
  emit("update:selectedCities", []);
  emit("update:searchTerm", "");
};

const getCategoryIcon = (categoryId) => {
  const category = categories.value.find((cat) => cat.id === categoryId);
  return category ? category.icon : "📍";
};

const getLocalCategoryName = (category) => {
  // Access the category translations directly from the translations object
  const { translations } = useTranslations();
  const categoryTranslations = translations.value?.categories;
  return categoryTranslations?.[category] || category;
};
</script>

<style scoped>
/* Hide scrollbar while keeping scroll functionality */
.scrollbar-hide {
  -ms-overflow-style: none; /* Internet Explorer 10+ */
  scrollbar-width: none; /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none; /* Safari and Chrome */
}
</style>
