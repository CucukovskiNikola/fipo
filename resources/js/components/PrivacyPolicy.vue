<template>
  <transition
    enter-active-class="transition ease-out duration-500"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-4"
  >
    <section
      v-if="isActive"
      class="liquid-glass text-white max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
    >
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Privacy 
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">
            Policy
          </span>
        </h1>
        <p class="text-xl text-gray-300 max-w-3xl mx-auto">
          Your privacy is important to us. This policy explains how we collect, use, and protect your information.
        </p>
        <div class="mt-4 text-sm text-gray-400">
          Last updated: {{ lastUpdated }}
        </div>
      </div>

      <!-- Content -->
      <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Section 1 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">1</span>
            </div>
            Information We Collect
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>We collect information you provide directly to us, such as:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Personal information (name, email address, phone number)</li>
              <li>Business information (company name, address, description)</li>
              <li>Account credentials and preferences</li>
              <li>Communications you send to us</li>
            </ul>
          </div>
        </div>

        <!-- Section 2 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">2</span>
            </div>
            How We Use Your Information
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>We use the information we collect to:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Provide and maintain our services</li>
              <li>Process transactions and send related information</li>
              <li>Send you technical notices and support messages</li>
              <li>Communicate with you about products, services, and events</li>
              <li>Monitor and analyze trends and usage</li>
              <li>Detect, investigate and prevent fraudulent transactions</li>
            </ul>
          </div>
        </div>

        <!-- Section 3 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-orange-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">3</span>
            </div>
            Information Sharing
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>We may share your information in the following circumstances:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>With your consent or at your direction</li>
              <li>For legal, protection, and safety purposes</li>
              <li>To enforce our terms and conditions</li>
              <li>With service providers who assist us in operating our platform</li>
            </ul>
            <p class="font-semibold text-white">We do not sell or rent your personal information to third parties.</p>
          </div>
        </div>

        <!-- Section 4 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">4</span>
            </div>
            Data Security
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            <p>However, no internet-based service is completely secure, so we cannot guarantee absolute security.</p>
          </div>
        </div>

        <!-- Section 5 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">5</span>
            </div>
            Your Rights
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>Under GDPR and other applicable laws, you have the right to:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Access your personal information</li>
              <li>Correct inaccurate or incomplete information</li>
              <li>Delete your personal information</li>
              <li>Object to or restrict processing</li>
              <li>Data portability</li>
              <li>Withdraw consent</li>
            </ul>
          </div>
        </div>

        <!-- Contact -->
        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
          <h2 class="text-2xl font-bold text-white mb-4">Contact Us</h2>
          <p class="text-gray-300 mb-4">
            If you have any questions about this Privacy Policy, please contact us:
          </p>
          <div class="space-y-2 text-gray-300">
            <p>📧 Email: privacy@findemich.de</p>
            <p>📞 Phone: +49 (0) 123 456 789</p>
            <p>📍 Address: Musterstraße 123, 12345 Berlin, Germany</p>
          </div>
        </div>
      </div>
    </section>
  </transition>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  isActive: Boolean,
});

const lastUpdated = computed(() => {
  return new Date().toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
});
</script>