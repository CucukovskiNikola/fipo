<script setup lang="ts">
import Button from './ui/button/Button.vue';

const emit = defineEmits(['navigate-to-contact']);

const handleContactClick = () => {
  emit('navigate-to-contact');
};
</script>

<template>
 <div class="mb-12 text-center bg-[rgba(3,3,3,0.8)] rounded-3xl p-2">
        <div class="max-w-4xl mx-auto p-5">
          <h1
            class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight"
          >
            Boost Your Business Across
            <span
              class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600"
              >Germany</span
            >
          </h1>

          <p class="text-base  lg:text-xl text-white mb-8 leading-relaxed max-w-3xl mx-auto">
            Get your restaurant, shop, or service noticed! For a simple fee,
            we'll feature your business on our platform, alongside others, and
            promote it with
            <span class="font-semibold text-blue-600">free targeted ads</span>
            on Facebook and Instagram. Expand your reach, attract more
            customers, and grow your brand effortlessly.
          </p>

          <div
            class="flex flex-col md:flex-row items-center justify-center gap-6"
          >
            <Button variant="gradient" size="large" @click="handleContactClick">
              Contact Us
              <i class="pi pi-phone mr-2"></i>
              </Button>

            <!-- Avatars and Text -->
            <div class="flex items-center space-x-3">
              <div class="flex -space-x-2">
                <img
                  class="w-8 h-8 object-cover rounded-full border-2 border-white shadow-md"
                  src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80"
                  alt="Business partner"
                />
                <img
                  class="w-8 h-8 object-cover rounded-full border-2 border-white shadow-md"
                  src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80"
                  alt="Business partner"
                />
                <img
                  class="w-8 h-8 object-cover rounded-full border-2 border-white shadow-md"
                  src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80"
                  alt="Business partner"
                />
              </div>
              <div class="text-left">
                <p class="text-sm font-semibold text-white">
                  Join thousands of
                </p>
                <p class="text-sm font-semibold text-blue-600">
                  business partners</p>
              </div>
            </div>
          </div>
        </div>
      </div>
</template>