<template>
  <Card class="hover:shadow-lg transition-all duration-300 border-0 bg-white/10 backdrop-blur-sm">
    <CardContent class="p-6 text-center">
      <!-- Icon with gradient background -->
      <div class="mb-4 mx-auto w-16 h-16 rounded-2xl flex items-center justify-center" :class="`bg-gradient-to-r ${card.color}`">
        <span class="text-3xl">{{ card.icon }}</span>
      </div>
      
      <!-- Title -->
      <h3 class="font-semibold text-lg text-white mb-3">{{ card.title }}</h3>
      
      <!-- Description -->
      <p class="text-sm text-gray-300 leading-relaxed">{{ card.description }}</p>
    </CardContent>
  </Card>
</template>

<script setup>
import { Card, CardContent } from "@/components/ui/card";

defineProps({
  card: Object,
});
</script>

<style scoped>
/* Additional hover effects */
.hover\:shadow-lg:hover {
  transform: translateY(-4px);
}
</style>