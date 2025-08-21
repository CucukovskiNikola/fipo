<template>
      <transition
    enter-active-class="transition ease-out duration-500"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >

     <section
      v-if="isActive"
      class="liquid-glass text-white max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
    >
      <!-- MetaAds Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Left side - Image -->
        <div class="relative overflow-hidden rounded-3xl">
          <img 
            src="https://images.unsplash.com/photo-1611224923853-80b023f02d71?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
            alt="Meta Ads - High Quality Digital Marketing"
            class="w-full h-80 object-cover shadow-2xl"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
          <div class="absolute bottom-4 left-4 text-white">
            <h4 class="text-lg font-semibold">Professional Meta Advertising</h4>
            <p class="text-sm opacity-90">Reach millions of potential customers</p>
          </div>
        </div>
        
        <!-- Right side - Text -->
        <div class="space-y-6 flex flex-col justify-center">
          <h2 class="text-3xl md:text-4xl font-bold text-white leading-tight">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
              {{ trans('home.free_meta_ads') }}
            </span>
          </h2>
          
          <p class="text-lg text-gray-300 leading-relaxed">
            {{ trans('home.meta_ads_description') }}
          </p>
          
          <div class="space-y-3">
            <div class="flex items-center space-x-3">
              <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
              <span class="text-gray-300">{{ trans('home.professional_ad_design') }}</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
              <span class="text-gray-300">{{ trans('home.targeted_audience') }}</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
              <span class="text-gray-300">{{ trans('home.monthly_reports') }}</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
              <span class="text-gray-300">{{ trans('home.no_additional_cost') }}</span>
            </div>
          </div>
          
          <div class="pt-4">
            <Button variant="gradient" size="large" class="inline-flex items-center w-full lg:w-[60%]">
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              {{ trans('home.learn_more_meta_ads') }}
            </Button>
          </div>
        </div>
      </div>

      <!-- What We Do Section -->
      <div class="relative">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none rounded-4xl opacity-50">
          <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-3xl blur-3xl"></div>
          <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-gradient-to-tr from-purple-500/20 to-pink-500/20 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10">
          <!-- Section Header -->
          <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">
                {{ trans('home.what_we_deliver') }}
              </span>
            </h2>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
              {{ trans('home.what_we_deliver_description') }}
            </p>
          </div>

          <!-- Services Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            <div 
              v-for="(card, index) in aboutCards" 
              :key="`service-${index}`"
              class="group relative"
            >
              <!-- Main Service Card -->
              <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-white/10 via-white/5 to-transparent backdrop-blur-xl border border-white/20 p-8 h-full transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-blue-500/10">
                <!-- Floating Icon Background -->
                <div class="absolute top-6 right-6 w-20 h-20 bg-gradient-to-br from-white/20 to-white/5 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/10">
                  <span class="text-3xl">{{ getServiceIcon(index) }}</span>
                </div>

                <!-- Content -->
                <div class="relative z-10">
                  <!-- Service Number -->
                  <div class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl text-white font-bold text-sm mb-6">
                    {{ String(index + 1).padStart(2, '0') }}
                  </div>

                  <!-- Title -->
                  <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-400 group-hover:to-purple-400 transition-all duration-300">
                    {{ card.title }}
                  </h3>

                  <!-- Description -->
                  <p class="text-gray-300 leading-relaxed mb-6">
                    {{ card.description }}
                  </p>

                  <!-- Feature Tags -->
                  <div class="flex flex-wrap gap-2">
                    <span 
                      v-for="feature in getServiceFeatures(index)" 
                      :key="feature"
                      class="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-xs text-gray-300 border border-white/10"
                    >
                      {{ feature }}
                    </span>
                  </div>
                </div>

                <!-- Hover Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-purple-500/5 to-pink-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl"></div>
                
                <!-- Bottom Accent Line -->
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left rounded-full"></div>
              </div>
            </div>
          </div>

          <!-- Call to Action -->
          <div class="text-center mt-16">
            <div class="inline-flex items-center space-x-4 bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
              <div class="flex -space-x-2">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                  🚀
                </div>
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                  💼
                </div>
                <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                  📈
                </div>
              </div>
              <div class="text-left">
                <p class="text-white font-semibold">{{ trans('home.ready_to_grow') }}</p>
                <p class="text-gray-300 text-sm">{{ trans('home.join_successful_partners') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

      </transition>
</template>

<script setup>
import { computed } from "vue";
import Button from "./ui/button/Button.vue";
import { useTranslations } from "@/composables/useTranslations";

const props = defineProps({
  isActive: Boolean,
});

const { trans } = useTranslations();

const aboutCards = computed(() => [
  {
    title: trans('home.find_partners_title'),
    description: trans('home.find_partners_desc'),
  },
  {
    title: trans('home.promote_business_title'), 
    description: trans('home.promote_business_desc'),
  },
  {
    title: trans('home.local_network_title'),
    description: trans('home.local_network_desc'),
  },
  {
    title: trans('home.business_growth_title'),
    description: trans('home.business_growth_desc'),
  },
]);

// Helper function to get service icons
const getServiceIcon = (index) => {
  const icons = ['🤝', '📢', '🌐', '📈'];
  return icons[index] || '⭐';
};

// Helper function to get service features
const getServiceFeatures = (index) => {
  const features = [
    [trans('home.partner_matching'), trans('home.collaboration_tools'), trans('home.trust_network')],
    [trans('home.digital_marketing'), trans('home.local_visibility'), trans('home.target_audience')],
    [trans('home.community_access'), trans('home.business_events'), trans('home.networking_hub')],
    [trans('home.analytics_dashboard'), trans('home.growth_metrics'), trans('home.success_tracking')]
  ];
  return features[index] || [];
};
</script>