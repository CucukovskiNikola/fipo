<template>
  <nav
    class="flex items-center justify-between px-6 py-2 bg-[#2a1509] max-w-6xl mx-auto rounded-4xl shadow-lg"
  >
    <!-- Left Menu -->
    <div class="flex space-x-6">
      <button
        @click="$emit('change-section', 'overview')"
        :class="activeSection === 'overview' ? 'underline' : ''"
      >
        Overview
      </button>
      <button
        @click="$emit('change-section', 'contact')"
        :class="activeSection === 'contact' ? 'underline' : ''"
      >
        Contact
      </button>
    </div>

    <!-- Logo -->
    <div class="text-xl font-bold tracking-tight">findemich</div>

    <!-- Auth Links -->
    <nav class="flex items-center justify-end gap-4">
      <Link
        v-if="$page.props.auth.user"
        :href="route('dashboard')"
        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#EDEDEC] hover:border-[#1915014a]"
      >
        Dashboard
      </Link>
      <template v-else>
        <Link
          :href="route('login')"
          class="inline-block rounded-4xl border border-transparent px-5 py-1.5 text-sm text-[#EDEDEC] hover:border-[#19140035]"
        >
          Log in
        </Link>
        <Link
          :href="route('register')"
          class="inline-block rounded-4xl bg-orange-600 px-5 py-1.5 text-sm text-black hover:bg-orange-700"
        >
          Register
        </Link>
      </template>
    </nav>
  </nav>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
  activeSection: String,
});
defineEmits(["change-section"]);
</script>
