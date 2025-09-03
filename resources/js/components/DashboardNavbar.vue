<template>
  <nav
    class="relative z-10 liquid-glass overflow-visible max-w-6xl mx-auto rounded-4xl shadow-lg"
  >
    <!-- Desktop Navigation -->
    <div class="hidden md:flex items-center justify-between px-6 py-2">
      <Link :href="route('dashboard')" class="flex items-center space-x-2">
        <span class="text-white font-semibold px-2">Dashboard</span>
      </Link>

      <!-- Desktop Menu -->
      <div class="flex space-x-2">
        <Link
          v-for="link in navigationLinks"
          :key="link.label"
          :href="link.href"
          @click="handleNavigation"
          class="rounded-4xl px-5 py-2 text-sm transition-colors text-[#EDEDEC] hover:border-[#19140035] border border-transparent"
        >
          {{ link.label }}
        </Link>
      </div>

      <!-- Desktop User Dropdown -->
      <div v-if="user" class="flex items-center justify-end gap-4">
        <div class="relative" @click.stop>
          <button
            @click="showDropdown = !showDropdown"
            class="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors"
          >
            <span>{{ user.name }}</span>
            <div
              class="w-8 h-8 bg-white/10 rounded-full flex items-center justify-center"
            >
              <Icon name="user" class="h-4 w-4 text-white" />
            </div>
          </button>

          <!-- Dropdown Menu -->
          <div
            v-show="showDropdown"
            class="absolute right-0 mt-3 w-48 liquid-glass rounded-2xl shadow-lg border border-white/20"
            style="z-index: 9999"
          >
            <div class="py-2">
              <Link
                :href="route('profile.edit')"
                class="flex items-center px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors"
              >
                <Icon name="user" class="h-4 w-4 mr-3" />
                Profileinstellungen
              </Link>
              <Link
                href="/"
                class="flex items-center px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors"
              >
                <Icon name="home" class="h-4 w-4 mr-3" />
                Startseite
              </Link>
              <hr class="border-white/20 my-2" />
              <button
                @click="logout"
                class="flex items-center w-full px-4 py-2 text-sm text-red-400 hover:bg-red-500/20 transition-colors"
              >
                <Icon name="logout" class="h-4 w-4 mr-3" />
                Abmelden
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="md:hidden">
      <!-- Mobile Header -->
      <div class="flex items-center justify-between px-4 py-3">
        <!-- Dashboard Title -->
        <Link :href="route('dashboard')" class="flex items-center space-x-2">
          <span class="text-white font-semibold px-2">Dashboard</span>
        </Link>

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
        <!-- Navigation Links -->
        <div class="space-y-2">
          <Link
            v-for="link in navigationLinks"
            :key="link.label"
            :href="link.href"
            @click="isMobileMenuOpen = false"
            class="block w-full text-left rounded-4xl px-4 py-3 text-sm text-[#EDEDEC] hover:bg-white/10 transition-colors"
          >
            {{ link.label }}
          </Link>
        </div>

        <!-- User Section -->
        <div v-if="user" class="border-t border-white/10 pt-3 space-y-2">
          <Link
            :href="route('profile.edit')"
            @click="isMobileMenuOpen = false"
            class="flex items-center w-full text-left rounded-4xl px-4 py-3 text-sm text-[#EDEDEC] hover:bg-white/10 transition-colors"
          >
            <Icon name="user" class="h-4 w-4 mr-3" />
            Profileinstellungen
          </Link>
          <button
            @click="logout"
            class="flex items-center w-full text-left rounded-4xl px-4 py-3 text-sm text-red-400 hover:bg-red-500/20 transition-colors"
          >
            <Icon name="logout" class="h-4 w-4 mr-3" />
            Abmelden
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Icon from "@/components/Icon.vue";
import { type User } from "@/types";

interface NavigationLink {
  label: string;
  href: string;
}

interface Props {
  title: string;
  titleIcon?: string;
  homeRoute?: string;
  navigationLinks?: NavigationLink[];
}

const props = withDefaults(defineProps<Props>(), {
  titleIcon: "home",
  homeRoute: "/",
  navigationLinks: () => [],
});

const page = usePage();
const user = page.props.auth?.user as User;
const showDropdown = ref(false);
const isMobileMenuOpen = ref(false);

const logout = () => {
  try {
    router.post(route("logout"));
  } catch (error) {
    console.error("Logout error:", error);
  }
};

const closeDropdown = (event?: Event) => {
  // Don't close if clicking on navigation links
  if (event?.target && (event.target as HTMLElement).closest("a[href]")) {
    return;
  }
  showDropdown.value = false;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleNavigation = () => {
  // Close mobile menu when navigating
  isMobileMenuOpen.value = false;
  showDropdown.value = false;
};

onMounted(() => {
  document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener("click", closeDropdown);
});
</script>
