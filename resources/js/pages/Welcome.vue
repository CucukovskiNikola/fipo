<template>
<div class="min-h-screen text-white p-2 bg-black" style="background-image: url('/images/bg.jpg'); background-size: contain; background-repeat: no-repeat;">
    <Navbar :activeSection="activeSection" @change-section="activeSection = $event" />
    <Overview :isActive="activeSection === 'overview'" @navigate-to-contact="activeSection = 'contact'" />
    <ContactForm :isActive="activeSection === 'contact'" />
    <WhatWeDo :isActive="activeSection === 'whatwedo'"/>
    <PrivacyPolicy :isActive="activeSection === 'privacy'" />
    <TermsAndConditions :isActive="activeSection === 'terms'" />
    <Footer @navigate-to="activeSection = $event" />
  </div>
</template>

<script setup>
import { ref } from "vue";
import Navbar from "@/components/Navbar.vue";
import Overview from "@/components/Overview.vue";
import ContactForm from "@/components/ContactForm.vue";
import WhatWeDo from "@/components/WhatWeDo.vue";
import PrivacyPolicy from "@/components/PrivacyPolicy.vue";
import TermsAndConditions from "@/components/TermsAndConditions.vue";
import Footer from "@/components/Footer.vue";

const activeSection = ref("overview");
</script>
