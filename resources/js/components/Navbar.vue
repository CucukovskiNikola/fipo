<template>
  <nav class="liquid-glass max-w-6xl mx-auto rounded-4xl shadow-lg">
    <!-- Desktop Navigation -->
    <div class="hidden md:flex items-center justify-between px-6 py-2">
      <img
        src="/images/logo.svg"
        alt="Logo"
        class="w-[150px] lg:w-[150px]"
        fetchpriority="high"
      />
      <!-- Menu -->
      <div class="flex space-x-2">
        <button
          @click="$emit('change-section', 'overview')"
          :class="[
            'rounded-4xl px-5 py-2 text-sm transition-colors',
            activeSection === 'overview'
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 '
              : 'text-[#EDEDEC] hover:border-[#19140035] border border-transparent ',
          ]"
        >
          Overview
        </button>
        <button
          @click="$emit('change-section', 'whatwedo')"
          :class="[
            'rounded-4xl px-5 py-2 text-sm transition-colors',
            activeSection === 'whatwedo'
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 '
              : 'text-[#EDEDEC] hover:border-[#19140035] border border-transparent ',
          ]"
        >
          What We Do
        </button>
        <button
          @click="$emit('change-section', 'contact')"
          :class="[
            'rounded-4xl px-5 py-2 text-sm transition-colors',
            activeSection === 'contact'
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 '
              : 'text-[#EDEDEC] hover:border-[#19140035] border border-transparent ',
          ]"
        >
          Contact
        </button>
      </div>

      <!-- Auth Links -->
      <div class="flex items-center justify-end gap-4">
        <Link
          v-if="$page.props.auth.user"
          :href="route('dashboard')"
          class="inline-block rounded-4xl px-5 py-2 text-sm leading-normal text-[#EDEDEC] hover:border-[#1915014a]"
        >
          Dashboard
        </Link>
        <template v-else>
          <Link
            :href="route('login')"
            class="inline-block rounded-4xl px-5 py-2 text-sm text-[#EDEDEC] bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
          >
            Log in
          </Link>
          <!-- <Link
            :href="route('register')"
            class="inline-block rounded-4xl bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700  px-5 py-2 text-sm text-white "
          >
            Register
          </Link> -->
        </template>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="md:hidden">
      <!-- Mobile Header -->
      <div class="flex items-center justify-between px-4 py-3">
        <!-- Logo -->
        <img src="/images/logo.svg" alt="Logo" class="w-[120px]" />

        <!-- Mobile Menu Button -->
        <button
          aria-label="navbar"
          @click="toggleMobileMenu"
          class="p-2 rounded-lg text-[#EDEDEC] hover:bg-white/10 transition-colors"
          :class="{ 'bg-white/10': isMobileMenuOpen }"
        >
          <svg
            v-if="!isMobileMenuOpen"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
          <svg
            v-else
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu Dropdown -->
      <div
        v-if="isMobileMenuOpen"
        class="border-t border-white/10 px-4 py-3 space-y-3"
      >
        <!-- Navigation Buttons -->
        <div class="space-y-2">
          <button
            @click="handleMobileNavClick('overview')"
            :class="[
              'w-full text-left rounded-4xl px-4 py-3 text-sm transition-colors',
              activeSection === 'overview'
                ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white'
                : 'text-[#EDEDEC] hover:bg-white/10',
            ]"
          >
            Overview
          </button>
          <button
            @click="handleMobileNavClick('whatwedo')"
            :class="[
              'w-full text-left rounded-4xl px-4 py-3 text-sm transition-colors',
              activeSection === 'whatwedo'
                ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white'
                : 'text-[#EDEDEC] hover:bg-white/10',
            ]"
          >
            What We Do
          </button>
          <button
            @click="handleMobileNavClick('contact')"
            :class="[
              'w-full text-left rounded-4xl px-4 py-3 text-sm transition-colors',
              activeSection === 'contact'
                ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white'
                : 'text-[#EDEDEC] hover:bg-white/10',
            ]"
          >
            Contact
          </button>
        </div>

        <!-- Auth Links -->
        <div class="border-t border-white/10 pt-3 space-y-2">
          <Link
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            class="block w-full text-left rounded-4xl px-4 py-3 text-sm text-[#EDEDEC] hover:bg-white/10 transition-colors"
          >
            Dashboard
          </Link>
          <template v-else>
            <Link
              :href="route('login')"
              class="block w-full text-left rounded-4xl px-4 py-3 text-sm text-[#EDEDEC] hover:bg-white/10 transition-colors"
            >
              Log in
            </Link>
            <!-- <Link
              :href="route('register')"
              class="block w-full text-left rounded-4xl bg-orange-600 px-4 py-3 text-sm text-black hover:bg-orange-700 transition-colors"
            >
              Register
            </Link> -->
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  activeSection: String,
});

const emit = defineEmits(["change-section"]);

// Mobile menu state
const isMobileMenuOpen = ref(false);

// Toggle mobile menu
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

// Handle mobile navigation click
const handleMobileNavClick = (section) => {
  emit("change-section", section);
  isMobileMenuOpen.value = false; // Close menu after selection
};
</script>
