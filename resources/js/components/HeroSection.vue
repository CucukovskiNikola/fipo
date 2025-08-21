<script setup lang="ts">
import Button from "./ui/button/Button.vue";
import { useTranslations } from "@/composables/useTranslations";

const emit = defineEmits(["navigate-to-contact"]);
const { trans } = useTranslations();

const handleContactClick = () => {
  emit("navigate-to-contact");
};
</script>

<template>
  <div class="mb-12 text-center bg-[rgba(3,3,3,0.8)] rounded-3xl p-2">
    <div class="max-w-4xl mx-auto p-5">
      <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
        {{ trans('home.boost_business_title', { country: trans('home.germany') }) }}
      </h1>

      <p
        class="text-base lg:text-xl text-white mb-8 leading-relaxed max-w-3xl mx-auto"
      >
        {{ trans('home.hero_description_part1') }}
        <span class="font-semibold text-blue-600">{{ trans('home.free_targeted_ads') }}</span>
        {{ trans('home.hero_description_part2') }}
      </p>

      <div class="flex flex-col md:flex-row items-center justify-center gap-6">
        <Button
          variant="gradient"
          size="large"
          class="cursor-pointer"
          @click="handleContactClick"
        >
          {{ trans('home.contact_us_button') }}
          <i class="pi pi-phone mr-2"></i>
        </Button>

        <!-- Avatars and Text -->
        <div class="flex items-center space-x-3">
          <div class="flex -space-x-2">
            <img
              class="w-8 h-8 object-cover rounded-full border-2 border-white shadow-md"
              src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80"
              alt="Business partner"
              fetchpriority="high"
            />
            <img
              class="w-8 h-8 object-cover rounded-full border-2 border-white shadow-md"
              src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80"
              alt="Business partner"
              fetchpriority="high"
            />
            <img
              class="w-8 h-8 object-cover rounded-full border-2 border-white shadow-md"
              src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80"
              alt="Business partner"
              fetchpriority="high"
            />
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-white">{{ trans('home.join_thousands') }}</p>
            <p class="text-sm font-semibold text-blue-600">{{ trans('home.business_partners') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
