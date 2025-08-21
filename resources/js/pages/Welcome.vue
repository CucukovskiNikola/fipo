<template>
  <Head>
    <title>
      {{ meta?.title || trans('business.find_local_partners') + ' - findemich' }}
    </title>
    <meta
      name="description"
      :content="
        meta?.description || trans('business.find_partners_near_you')
      "
    />
  </Head>

  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <Navbar :activeSection="activeSection" @change-section="changeSection" />
    <Overview
      v-if="activeSection === 'overview'"
      :isActive="activeSection === 'overview'"
      @navigate-to-contact="() => changeSection('contact')"
    />
    <ContactForm
      v-if="activeSection === 'contact'"
      :isActive="activeSection === 'contact'"
    />
    <WhatWeDo
      v-if="activeSection === 'whatwedo'"
      :isActive="activeSection === 'whatwedo'"
    />
    <PrivacyPolicy
      v-if="activeSection === 'privacy'"
      :isActive="activeSection === 'privacy'"
    />
    <TermsAndConditions
      v-if="activeSection === 'terms'"
      :isActive="activeSection === 'terms'"
    />
    <Footer v-if="activeSection === 'overview'" @navigate-to="changeSection" />
  </div>
</template>

<script setup>
import { ref, onMounted, defineAsyncComponent } from "vue";
import { Head } from "@inertiajs/vue3";
import { useTranslations } from "@/composables/useTranslations";

import Navbar from "@/components/Navbar.vue";
import Overview from "@/components/Overview.vue";

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

const props = defineProps(["meta", "debug"]);
const { trans } = useTranslations();

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
