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
      class="bg-white text-black max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
    >
      <h2 class="text-xl font-semibold mb-6">Overview</h2>

      <!-- Search & Filters -->
      <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
        <!-- Search Bar -->
        <div
          class="flex items-center w-full md:w-2/3 bg-gray-50 rounded-4xl px-4 py-2 border border-gray-100"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"
            />
          </svg>
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Search..."
            class="bg-transparent w-full focus:outline-none"
          />
        </div>

        <!-- Filters -->
        <div class="flex gap-2 w-full md:w-auto">
          <button
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-4xl text-sm border border-gray-200"
          >
            All
          </button>
          <button
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-4xl text-sm border border-gray-200"
          >
            City
          </button>
          <button
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-4xl text-sm border border-gray-200"
          >
            Category
          </button>
          <button
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-4xl text-sm border border-gray-200"
          >
            FAQs
          </button>
        </div>
      </div>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <CardItem v-for="(card, index) in filteredCards" :key="index" :card="card" />
      </div>

      <!-- Footer Links -->
      <div class="flex justify-between text-xs text-gray-500 mt-6">
        <div class="space-x-4">
          <a href="#" class="hover:underline">World's Healthiest</a>
          <a href="#" class="hover:underline">The Founder Health Coalition</a>
        </div>
        <div class="space-x-4">
          <a href="#" class="hover:underline">Privacy Policy</a>
          <a href="#" class="hover:underline">Informed Medical Consent</a>
          <a href="#" class="hover:underline">Terms & Conditions</a>
        </div>
      </div>
    </section>
  </transition>
</template>

<script setup>
import { ref, computed } from "vue";
import CardItem from "./CardItem.vue";

const props = defineProps({
  isActive: Boolean,
});

const cards = [
  {
    title: "Findemich",
    description: "Your membership starts with 100+ labs. Here's everything we test.",
    image:
      "https://imgs.search.brave.com/xNsdXO9R-wQ42ninZY-J6W-O5ZCrzjv69YPyuS1xc5I/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzA1LzIxLzE4LzAz/LzM2MF9GXzUyMTE4/MDM3N18yaUFWSnFC/UVNvM2NnS2FWcDh2/TUJSOGFzckM2MURv/VS5qcGc",
  },
  {
    title: "Stories",
    description: "Hear what members have to say about their Superpower experience.",
    image:
      "https://imgs.search.brave.com/xNsdXO9R-wQ42ninZY-J6W-O5ZCrzjv69YPyuS1xc5I/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzA1LzIxLzE4LzAz/LzM2MF9GXzUyMTE4/MDM3N18yaUFWSnFC/UVNvM2NnS2FWcDh2/TUJSOGFzckM2MURv/VS5qcGc",
  },
  {
    title: "Our Why",
    description: "Our vision for a better future of health.",
    image:
      "https://imgs.search.brave.com/xNsdXO9R-wQ42ninZY-J6W-O5ZCrzjv69YPyuS1xc5I/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzA1LzIxLzE4LzAz/LzM2MF9GXzUyMTE4/MDM3N18yaUFWSnFC/UVNvM2NnS2FWcDh2/TUJSOGFzckM2MURv/VS5qcGc",
  },
  {
    title: "FAQs",
    description: "Find answers to common questions.",
    image:
      "https://imgs.search.brave.com/xNsdXO9R-wQ42ninZY-J6W-O5ZCrzjv69YPyuS1xc5I/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90NC5m/dGNkbi5uZXQvanBn/LzA1LzIxLzE4LzAz/LzM2MF9GXzUyMTE4/MDM3N18yaUFWSnFC/UVNvM2NnS2FWcDh2/TUJSOGFzckM2MURv/VS5qcGc",
  },
];

const searchTerm = ref("");

const filteredCards = computed(() => {
  if (!searchTerm.value) return cards;

  const term = searchTerm.value.toLowerCase();

  return cards.filter(
    (card) =>
      card.title.toLowerCase().includes(term) ||
      card.description.toLowerCase().includes(term)
  );
});
</script>
