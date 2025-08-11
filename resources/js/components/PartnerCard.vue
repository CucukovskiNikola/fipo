<template>
  <Card class="hover:shadow-md transition">
    <CardContent class="p-4">
      <div class="mb-3">
        <h3 class="font-semibold text-lg mb-1">{{ partner.title }}</h3>
        <span class="inline-flex items-center px-3 py-1 bg-[#1a0d05] text-white text-xs rounded-full mb-2">
          <span class="mr-1">{{ categoryInfo.icon }}</span>
          {{ categoryInfo.name }}
        </span>
      </div>
      
      <p class="text-sm text-gray-600 mb-3 line-clamp-3">{{ partner.description }}</p>
      
      <div class="space-y-1 text-xs text-gray-500">
        <div class="flex items-center">
          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {{ partner.city }}, {{ partner.zip_code }}
        </div>
        
        <div v-if="partner.name_of_owner" class="flex items-center">
          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          {{ partner.name_of_owner }}
        </div>
      </div>
    </CardContent>
  </Card>
</template>

<script setup>
import { computed } from 'vue';
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
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>