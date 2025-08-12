<template>
  <Card class="hover:shadow-md transition overflow-hidden">
    <!-- Image skeleton -->
    <div class="h-48 bg-gray-300 animate-pulse"></div>
    
    <CardContent class="p-4">
      <div class="mb-3">
        <!-- Title skeleton -->
        <div class="h-5 bg-gray-300 rounded animate-pulse mb-2"></div>
        <!-- Category badge skeleton -->
        <div class="h-6 bg-gray-200 rounded-full w-24 animate-pulse mb-2"></div>
      </div>
      
      <!-- Description skeleton -->
      <div class="space-y-2 mb-3">
        <div class="h-3 bg-gray-200 rounded animate-pulse"></div>
        <div class="h-3 bg-gray-200 rounded animate-pulse w-4/5"></div>
        <div class="h-3 bg-gray-200 rounded animate-pulse w-3/5"></div>
      </div>
      
      <!-- Footer info skeleton -->
      <div class="space-y-1">
        <div class="flex items-center">
          <div class="w-3 h-3 bg-gray-200 rounded mr-1 animate-pulse"></div>
          <div class="h-3 bg-gray-200 rounded w-32 animate-pulse"></div>
        </div>
        
        <div class="flex items-center">
          <div class="w-3 h-3 bg-gray-200 rounded mr-1 animate-pulse"></div>
          <div class="h-3 bg-gray-200 rounded w-24 animate-pulse"></div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>

<script setup>
import { Card, CardContent } from "@/components/ui/card";
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>