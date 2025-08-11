<template>
  <div class="min-h-screen bg-[#1a0d05] text-white p-2">
    <Navbar :activeSection="activeSection" @change-section="activeSection = $event" />
    <Overview :isActive="activeSection === 'overview'" />
    <ContactForm :isActive="activeSection === 'contact'" />
  </div>
</template>

<script setup>
import { ref } from "vue";
import Navbar from "@/components/Navbar.vue";
import Overview from "@/components/Overview.vue";
import ContactForm from "@/components/ContactForm.vue";

const activeSection = ref("overview");
</script>
