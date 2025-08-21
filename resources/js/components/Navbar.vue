<template>
  <nav class="liquid-glass max-w-6xl mx-auto rounded-4xl shadow-lg">
    <!-- Desktop Navigation -->
    <div class="hidden md:flex items-center justify-between px-6 py-2">
      <img src="/images/logo.svg" alt="Logo" class="w-[150px] lg:w-[150px]" fetchpriority="high" />
      <!-- Menu -->
      <div class="flex space-x-2">
        <button @click="$emit('change-section', 'overview')" :class="[
          'rounded-4xl px-5 py-2 text-sm transition-colors',
          activeSection === 'overview'
            ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 '
            : 'text-[#EDEDEC] hover:border-[#19140035] border border-transparent ',
        ]">
          {{ trans('common.home') }}
        </button>
        <button @click="$emit('change-section', 'whatwedo')" :class="[
          'rounded-4xl px-5 py-2 text-sm transition-colors',
          activeSection === 'whatwedo'
            ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 '
            : 'text-[#EDEDEC] hover:border-[#19140035] border border-transparent ',
        ]">
          {{ trans('common.about') }}
        </button>
        <button @click="$emit('change-section', 'contact')" :class="[
          'rounded-4xl px-5 py-2 text-sm transition-colors',
          activeSection === 'contact'
            ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 '
            : 'text-[#EDEDEC] hover:border-[#19140035] border border-transparent ',
        ]">
          {{ trans('common.contact') }}
        </button>
      </div>

      <!-- Language Switcher & Auth Links -->
      <div class="flex items-center justify-end gap-4">
        <LanguageSwitcher />
        <template v-if="$page.props.auth.user">
          <!-- If Admin -->
          <Link v-if="$page.props.auth.user.role === 'admin'" :href="route('dashboard')"
            class="block w-full text-left rounded-4xl px-4 py-3 text-sm text-[#EDEDEC] hover:bg-white/10 transition-colors">
          {{ trans('common.dashboard') }}
          </Link>

          <!-- If Regular User -->
          <Link method="post" :href="route('logout')" as="button"
            class="block w-full text-left rounded-4xl px-4 py-3 text-sm text-[#EDEDEC] hover:bg-white/10 transition-colors">
          {{ trans('common.logout') }}
          </Link>
        </template>

        <template v-else>
          <Link :href="route('login')"
            class="block w-full text-left rounded-4xl px-4 py-3 text-sm text-[#EDEDEC] hover:bg-white/10 transition-colors">
          {{ trans('common.login') }}
          </Link>
        </template>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="md:hidden">
      <!-- Mobile Header -->
      <div class="flex items-center justify-between px-4 py-3">
        <!-- Logo -->
        <img src="/images/logo.svg" alt="Logo" class="w-[120px]" fetchpriority="high" />

        <!-- Mobile Menu Button -->
        <button aria-label="navbar" @click="toggleMobileMenu"
          class="p-2 rounded-lg text-[#EDEDEC] hover:bg-white/10 transition-colors"
          :class="{ 'bg-white/10': isMobileMenuOpen }">
          <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu Dropdown -->
      <div v-if="isMobileMenuOpen" class="border-t border-white/10 px-4 py-3 space-y-3">
        <!-- Navigation Buttons -->
        <div class="space-y-2">
          <button @click="handleMobileNavClick('overview')" :class="[
            'w-full text-left rounded-4xl px-4 py-3 text-sm transition-colors',
            activeSection === 'overview'
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white'
              : 'text-[#EDEDEC] hover:bg-white/10',
          ]">
            {{ trans('common.home') }}
          </button>
          <button @click="handleMobileNavClick('whatwedo')" :class="[
            'w-full text-left rounded-4xl px-4 py-3 text-sm transition-colors',
            activeSection === 'whatwedo'
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white'
              : 'text-[#EDEDEC] hover:bg-white/10',
          ]">
            {{ trans('common.about') }}
          </button>
          <button @click="handleMobileNavClick('contact')" :class="[
            'w-full text-left rounded-4xl px-4 py-3 text-sm transition-colors',
            activeSection === 'contact'
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white'
              : 'text-[#EDEDEC] hover:bg-white/10',
          ]">
            {{ trans('common.contact') }}
          </button>
        </div>

        <!-- Language Switcher -->
        <div class="border-t border-white/10 pt-3 pb-3">
          <div class="px-4">
            <LanguageSwitcher />
          </div>
        </div>

        <!-- Auth Links -->
        <div class="border-t border-white/10 pt-3 space-y-2">
          <template v-if="$page.props.auth.user">
            <!-- If Admin -->
            <Link v-if="$page.props.auth.user.role === 'admin'" :href="route('dashboard')"
              class="inline-block rounded-4xl px-5 py-2 text-sm leading-normal text-[#EDEDEC] hover:border-[#1915014a]">
            {{ trans('common.dashboard') }}
            </Link>

            <!-- If Regular User -->
            <div v-else class="relative" @mouseleave="showDropdown = false">
              <button @click="toggleDropdown"
                class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20">
                <!-- Example: Just first letter of name -->
                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
              </button>
              <div v-if="showDropdown" class="absolute right-0 mt-2 w-32 bg-white text-black rounded-lg shadow-lg z-10">
                <Link method="post" :href="route('logout')" as="button"
                  class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                {{ trans('common.logout') }}
                </Link>
              </div>
            </div>
          </template>

          <!-- If not logged in -->
          <template v-else>
            <Link :href="route('login')"
              class="inline-block rounded-4xl px-5 py-2 text-sm text-[#EDEDEC] bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">
            </Link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { useTranslations } from "@/composables/useTranslations";
import LanguageSwitcher from "@/components/LanguageSwitcher.vue";

const props = defineProps({
  activeSection: String,
});

const emit = defineEmits(["change-section"]);
const { trans } = useTranslations();

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