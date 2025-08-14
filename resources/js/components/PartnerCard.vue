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
        class="w-full h-full object-cover shadow-2xl rounded-xl"
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

      <div class="pt-2 pb-2">
        <span
          class="inline-flex items-center px-3 py-1 border bg-white/10 backdrop-blur-sm border-white/20 text-white text-xs rounded-2xl"
        >
          <span>{{ categoryInfo.icon }}</span>
          {{ categoryInfo.name }}
        </span>
      </div>
    </div>

    <CardContent class="p-2">
      <div class="my-3">
        <h3 class="font-semibold text-lg text-white line-clamp-2 min-h-14">
          {{ partner.title }}
        </h3>
      </div>

      <p class="text-sm text-white mb-3 line-clamp-3 min-h-16">
        {{ partner.description }}
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
import { computed } from "vue";
import { Card, CardContent } from "@/components/ui/card";
import { router } from "@inertiajs/vue3";
import categories from "@/data/categories.json";

const props = defineProps({
  partner: Object,
});

const categoryInfo = computed(() => {
  const category = categories.find((cat) => cat.id === props.partner.category);
  return category
    ? { icon: category.icon, name: category.name }
    : { icon: "📍", name: props.partner.category };
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

const createSlug = (title) => {
  return title
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");
};

const navigateToPartner = () => {
  if (!props.partner.id) return;

  const slug = createSlug(props.partner.title);

  const params = new URLSearchParams({
    id: props.partner.id,
    title: slug,
    category: props.partner.category,
    city: props.partner.city,
    location: `${props.partner.city}-${props.partner.zip_code}`,
  });

  const finalUrl = `/partners?${params.toString()}`;
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
