<template>
  <Card class="hover:shadow-md transition overflow-hidden">
    <!-- Partner Image -->
    <div class="relative h-55">
      <div class="absolute z-50 right-0">
        <span class="inline-flex items-center px-3 py-1 border bg-white border-gray-500 text-black text-xs rounded-2xl ">
          <span>{{ categoryInfo.icon }}</span>
          {{ categoryInfo.name }}
        </span>
        </div>
      <img 
        v-if="partner.image" 
        :src="partner.image" 
        :alt="partner.title"
        class="w-full h-full rounded-xl object-cover shadow-2xl"
        @error="handleImageError"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
      </div>
    </div>
    
    <CardContent class="p-2">
      <div class="mb-1">
        <h3 class="font-semibold text-lg  text-black line-clamp-2 min-h-14">{{ partner.title }}</h3>
      </div>
      
      <p class="text-sm text-black mb-3 line-clamp-3">{{ partner.description }}</p>
      
      <div class="space-y-1 text-xs text-gray-900">

        <div class="flex justify-end items-center line-clamp-1">
          <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {{ partner.city }}, {{ partner.zip_code }}
        </div>
        
        <div v-if="partner.name_of_owner" class="flex items-center">
          <!-- <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          {{ partner.name_of_owner }} -->

        </div>
        
      </div>
    </CardContent>
  </Card>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Card, CardContent } from "@/components/ui/card";
import categories from '@/data/categories.json';

const props = defineProps({
  partner: Object,
});

// Get category icon and name
const categoryInfo = computed(() => {
  const category = categories.find(cat => cat.id === props.partner.category);
  return category ? { icon: category.icon, name: category.name } : { icon: '📍', name: props.partner.category };
});

// Handle image load errors
const handleImageError = (event) => {
  event.target.style.display = 'none';
};
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp:3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>