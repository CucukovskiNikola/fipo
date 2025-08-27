<template>
  <footer
    class="liquid-glass max-w-6xl mx-auto rounded-4xl shadow-lg mt-8 overflow-hidden"
  >
    <!-- Main Footer Content -->
    <div class="px-6 py-8 lg:px-8 lg:py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Company Info -->
        <div class="lg:col-span-2">
          <div class="flex items-center mb-4">
            <img
              src="/images/logo.svg"
              alt="Logo"
              class="w-32 h-12"
              fetchpriority="high"
            />
          </div>
          <p class="text-gray-300 text-sm leading-relaxed mb-6 max-w-md">
            {{ trans("home.footer_description") }}
          </p>

          <!-- Social Media -->
          <div class="flex space-x-4">
            <a
              href="#"
              aria-label="Facebook"
              class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center text-white hover:bg-white/20 transition-colors border border-white/10"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h1.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h1.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                />
              </svg>
            </a>

            <a
              href="#"
              aria-label="Instagram"
              class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center text-white hover:bg-white/20 transition-colors border border-white/10"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M7.75 2A5.75 5.75 0 0 0 2 7.75v8.5A5.75 5.75 0 0 0 7.75 22h8.5A5.75 5.75 0 0 0 22 16.25v-8.5A5.75 5.75 0 0 0 16.25 2h-8.5zm0 1.5h8.5A4.25 4.25 0 0 1 20.5 7.75v8.5a4.25 4.25 0 0 1-4.25 4.25h-8.5A4.25 4.25 0 0 1 3.5 16.25v-8.5A4.25 4.25 0 0 1 7.75 3.5zm8.75 2a1 1 0 1 0 0 2 1 1 0 0 0 0-2zM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 1.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"
                />
              </svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h1 class="text-white font-semibold mb-4">
            {{ trans("home.quick_links") }}
          </h1>
          <ul class="space-y-2">
            <li>
              <button
                @click="handleNavigate('overview')"
                class="text-gray-300 hover:text-white text-sm transition-colors hover:translate-x-1 transform duration-200"
              >
                {{ trans("home.overview") }}
              </button>
            </li>
            <li>
              <button
                @click="handleNavigate('whatwedo')"
                class="text-gray-300 hover:text-white text-sm transition-colors hover:translate-x-1 transform duration-200"
              >
                {{ trans("home.what_we_do") }}
              </button>
            </li>
            <li>
              <button
                @click="handleNavigate('contact')"
                class="text-gray-300 hover:text-white text-sm transition-colors hover:translate-x-1 transform duration-200"
              >
                {{ trans("common.contact") }}
              </button>
            </li>
          </ul>
        </div>

        <!-- Legal -->
        <div>
          <h1 class="text-white font-semibold mb-4">
            {{ trans("home.legal") }}
          </h1>
          <ul class="space-y-2">
            <li>
              <button
                @click="handleNavigate('privacy')"
                class="text-gray-300 hover:text-white text-sm transition-colors hover:translate-x-1 transform duration-200"
              >
                {{ trans("home.privacy_policy") }}
              </button>
            </li>
            <li>
              <button
                @click="handleNavigate('terms')"
                class="text-gray-300 hover:text-white text-sm transition-colors hover:translate-x-1 transform duration-200"
              >
                {{ trans("home.terms_conditions") }}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="border-t border-white/10 px-6 py-4 lg:px-8">
      <div
        class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0"
      >
        <p class="text-gray-400 text-xs">
          © {{ new Date().getFullYear() }} findemich.
          {{ trans("home.all_rights_reserved") }}
        </p>
        <div class="flex items-center space-x-4 text-xs text-gray-400">
          <span>{{ trans("home.made_with_love") }}</span>
          <div class="w-1 h-1 bg-gray-500 rounded-full"></div>
          <span>v1.0.0</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { useTranslations } from "@/composables/useTranslations";

const emit = defineEmits(["navigate-to"]);
const { trans } = useTranslations();

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
};

const handleNavigate = (section) => {
  // First scroll to top
  scrollToTop();

  // Then emit navigation event after a small delay to ensure smooth scroll
  setTimeout(() => {
    emit("navigate-to", section);
  }, 200);
};
</script>
