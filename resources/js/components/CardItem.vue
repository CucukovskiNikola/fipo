<template>
  <Card class="hover:shadow-md transition">
    <CardHeader>
      <img :src="card.image" :alt="card.title" class="rounded-md w-full h-40 object-cover" />
    </CardHeader>
    <CardContent>
      <h3 class="font-semibold text-lg">{{ card.title }}</h3>
      <p class="text-sm text-gray-600">{{ card.description }}</p>
    </CardContent>
  </Card>
</template>

<script setup>
import { Card, CardHeader, CardContent } from "@/components/ui/card";

defineProps({
  card: Object,
});
</script>
