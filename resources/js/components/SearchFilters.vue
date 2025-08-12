<template>
  <div class="flex gap-3 items-center">
    <!-- Search Bar -->
    <div class="flex-1 relative">
      <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-white" />
      <Input
        :model-value="searchTerm"
        @update:model-value="$emit('update:searchTerm', $event)"
        placeholder="Search partners by name, category, or city..."
        class="w-full pl-10 pr-4 h-14 rounded-3xl  bg-gray-200 backdrop-blur-sm text-black placeholder:text-black"
      />
    </div>

    <!-- Filter Popup Button -->
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button
          variant="outline"
          size="sm"
          class="h-14 w-14 rounded-3xl bg-gray-200 backdrop-blur-sm text-black hover:bg-white/20 p-0 flex items-center justify-center"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="w-80 p-4 rounded-3xl" align="end">
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h4 class="font-medium">Filters</h4>
            <Button
              v-if="hasActiveFilters"
              @click="handleResetFilters"
              variant="outline"
              size="sm"
              class="h-8 px-3 text-xs"
            >
              <X class="h-3 w-3 mr-1" />
              Reset
            </Button>
          </div>
          
          <!-- Category Filter -->
          <div v-if="categories.length > 0">
            <label class="text-sm font-medium mb-2 block">Category</label>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button
                  variant="outline"
                  class="w-full justify-between h-10"
                >
                  <div class="flex items-center">
                    <span class="mr-2">{{ selectedCategory ? getCategoryIcon(selectedCategory) : '📁' }}</span>
                    {{ selectedCategory ? getCategoryName(selectedCategory) : 'Select Category' }}
                  </div>
                  <ChevronDown class="h-4 w-4 opacity-50" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent class="w-64">
                <DropdownMenuItem @click="handleCategoryChange('')" class="cursor-pointer">
                  <div class="flex items-center">
                    <span class="mr-2">📁</span>
                    All Categories
                  </div>
                </DropdownMenuItem>
                <DropdownMenuItem
                  v-for="category in categories"
                  :key="category"
                  @click="handleCategoryChange(category)"
                  class="cursor-pointer"
                >
                  <div class="flex items-center">
                    <span class="mr-2">{{ getCategoryIcon(category) }}</span>
                    {{ getCategoryName(category) }}
                  </div>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
          
          <!-- City Filter -->
          <div v-if="cities.length > 0">
            <label class="text-sm font-medium mb-2 block">City</label>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button
                  variant="outline"
                  class="w-full justify-between h-10"
                >
                  <div class="flex items-center">
                    <MapPin class="mr-2 h-4 w-4" />
                    {{ selectedCity || 'Select City' }}
                  </div>
                  <ChevronDown class="h-4 w-4 opacity-50" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent class="w-48">
                <DropdownMenuItem @click="handleCityChange('')" class="cursor-pointer">
                  <div class="flex items-center">
                    <MapPin class="mr-2 h-4 w-4" />
                    All Cities
                  </div>
                </DropdownMenuItem>
                <DropdownMenuItem
                  v-for="city in cities"
                  :key="city"
                  @click="handleCityChange(city)"
                  class="cursor-pointer"
                >
                  <div class="flex items-center">
                    <MapPin class="mr-2 h-4 w-4" />
                    {{ city }}
                  </div>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>

          <!-- Active Filters Display -->
          <div v-if="hasActiveFilters" class="space-y-2">
            <label class="text-sm font-medium">Active Filters:</label>
            <div class="flex flex-wrap gap-2">
              <span v-if="selectedCategory" class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                {{ getCategoryIcon(selectedCategory) }} {{ getCategoryName(selectedCategory) }}
                <button @click="handleCategoryChange('')" class="ml-1 text-blue-600 hover:text-blue-800">
                  <X class="h-3 w-3" />
                </button>
              </span>
              <span v-if="selectedCity" class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                <MapPin class="mr-1 h-3 w-3" /> {{ selectedCity }}
                <button @click="handleCityChange('')" class="ml-1 text-green-600 hover:text-green-800">
                  <X class="h-3 w-3" />
                </button>
              </span>
            </div>
          </div>
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Input } from '@/components/ui/input'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { Search, X, MapPin, ChevronDown } from 'lucide-vue-next'
import categoriesData from '@/data/categories.json';

const props = defineProps({
  searchTerm: String,
  selectedCategory: String,
  selectedCity: String,
  categories: Array,
  cities: Array
});

const emit = defineEmits([
  'update:searchTerm',
  'categoryChange',
  'cityChange',
  'resetFilters'
]);

const hasActiveFilters = computed(() => 
  props.searchTerm || props.selectedCategory || props.selectedCity
);

const handleCategoryChange = (category) => {
  emit('categoryChange', category || null);
};

const handleCityChange = (city) => {
  emit('cityChange', city || null);
};

const handleResetFilters = () => {
  emit('resetFilters');
};

// Get category icon and name
const getCategoryIcon = (categoryId) => {
  const category = categoriesData.find(cat => cat.id === categoryId);
  return category ? category.icon : '📍';
};

const getCategoryName = (categoryId) => {
  const category = categoriesData.find(cat => cat.id === categoryId);
  return category ? category.name : categoryId;
};
</script>

<style scoped>
/* Custom styling for search filters */
</style>