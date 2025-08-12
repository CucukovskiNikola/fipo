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
          Terms & 
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">
            Conditions
          </span>
        </h1>
        <p class="text-xl text-gray-300 max-w-3xl mx-auto">
          Please read these terms carefully before using our platform. By using our services, you agree to these terms.
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
            Acceptance of Terms
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>By accessing and using findemich, you accept and agree to be bound by the terms and provision of this agreement.</p>
            <p>If you do not agree to abide by the above, please do not use this service.</p>
          </div>
        </div>

        <!-- Section 2 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">2</span>
            </div>
            Use License
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>Permission is granted to temporarily use findemich for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Modify or copy the materials</li>
              <li>Use the materials for any commercial purpose</li>
              <li>Attempt to reverse engineer any software contained on the platform</li>
              <li>Remove any copyright or other proprietary notations</li>
            </ul>
          </div>
        </div>

        <!-- Section 3 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-orange-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">3</span>
            </div>
            User Accounts
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>When you create an account with us, you must provide information that is accurate, complete, and current at all times.</p>
            <p>You are responsible for safeguarding the password and for all activities that occur under your account.</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>You must be at least 18 years old to use our services</li>
              <li>You are responsible for maintaining account security</li>
              <li>You must notify us immediately of any breach of security</li>
              <li>We reserve the right to refuse service or terminate accounts</li>
            </ul>
          </div>
        </div>

        <!-- Section 4 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">4</span>
            </div>
            Business Listings
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>When submitting business information to our platform, you agree that:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>All information provided is accurate and truthful</li>
              <li>You have the right to list the business</li>
              <li>You will not submit false, misleading, or fraudulent information</li>
              <li>You will comply with all applicable laws and regulations</li>
              <li>You grant us permission to display and promote your business</li>
            </ul>
          </div>
        </div>

        <!-- Section 5 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">5</span>
            </div>
            Prohibited Uses
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>You may not use our service:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>For any unlawful purpose or to solicit others to unlawful acts</li>
              <li>To violate any international, federal, provincial or state regulations, rules, laws, or local ordinances</li>
              <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
              <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
              <li>To submit false or misleading information</li>
              <li>To upload or transmit viruses or any other type of malicious code</li>
            </ul>
          </div>
        </div>

        <!-- Section 6 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">6</span>
            </div>
            Limitation of Liability
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>In no event shall findemich or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on findemich's website.</p>
            <p>Some jurisdictions do not allow the limitation or exclusion of liability or incidental or consequential damages, so the above limitation or exclusion may not apply to you.</p>
          </div>
        </div>

        <!-- Section 7 -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
              <span class="text-sm font-bold">7</span>
            </div>
            Governing Law
          </h2>
          <div class="text-gray-300 space-y-4">
            <p>These terms and conditions are governed by and construed in accordance with the laws of Germany and you irrevocably submit to the exclusive jurisdiction of the courts in that State or location.</p>
          </div>
        </div>

        <!-- Contact -->
        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
          <h2 class="text-2xl font-bold text-white mb-4">Contact Us</h2>
          <p class="text-gray-300 mb-4">
            If you have any questions about these Terms and Conditions, please contact us:
          </p>
          <div class="space-y-2 text-gray-300">
            <p>📧 Email: legal@findemich.de</p>
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