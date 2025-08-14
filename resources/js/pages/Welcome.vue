<template>
  <div class="min-h-auto text-white p-2 bg-black bg-image" fetchpriority="high">
    <Navbar :activeSection="activeSection" @change-section="changeSection" />
    <Overview
      :isActive="activeSection === 'overview'"
      @navigate-to-contact="() => changeSection('contact')"
    />
    <ContactForm :isActive="activeSection === 'contact'" />
    <WhatWeDo :isActive="activeSection === 'whatwedo'" />
    <PrivacyPolicy :isActive="activeSection === 'privacy'" />
    <TermsAndConditions :isActive="activeSection === 'terms'" />
    <Footer @navigate-to="changeSection" />
  </div>
</template>

<script setup>
import { ref, onMounted, defineAsyncComponent } from "vue";

const Navbar = defineAsyncComponent(() => import("@/components/Navbar.vue"));
const Overview = defineAsyncComponent(
  () => import("@/components/Overview.vue")
);
const ContactForm = defineAsyncComponent(
  () => import("@/components/ContactForm.vue")
);
const WhatWeDo = defineAsyncComponent(
  () => import("@/components/WhatWeDo.vue")
);
const PrivacyPolicy = defineAsyncComponent(
  () => import("@/components/PrivacyPolicy.vue")
);
const TermsAndConditions = defineAsyncComponent(
  () => import("@/components/TermsAndConditions.vue")
);
const Footer = defineAsyncComponent(() => import("@/components/Footer.vue"));

const activeSection = ref("overview");

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const sectionParam = urlParams.get("section");

  if (sectionParam) {
    const validSections = [
      "overview",
      "whatwedo",
      "contact",
      "privacy",
      "terms",
    ];
    if (validSections.includes(sectionParam)) {
      activeSection.value = sectionParam;
    }
  }
});

const changeSection = (section) => {
  activeSection.value = section;

  const url = new URL(window.location);
  if (section === "overview") {
    url.searchParams.delete("section");
  } else {
    url.searchParams.set("section", section);
  }

  window.history.pushState({}, "", url.toString());
};
</script>
