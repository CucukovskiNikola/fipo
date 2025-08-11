<template>
  <transition
    enter-active-class="transition ease-out duration-500"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <section
      v-if="isActive"
      class="bg-white text-black max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
    >
      <h2 class="text-xl font-semibold mb-6">Contact</h2>
      <form @submit.prevent="submitForm" class="space-y-4" novalidate>
        <input
          v-model="form.name"
          type="text"
          placeholder="Your name"
          class="w-full border rounded-4xl p-2"
          :class="{ 'border-red-500': errors.name }"
        />
        <div v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</div>

        <input
          v-model="form.email"
          type="email"
          placeholder="you@example.com"
          class="w-full border rounded-4xl p-2"
          :class="{ 'border-red-500': errors.email }"
        />
        <div v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</div>

        <textarea
          v-model="form.message"
          placeholder="Your message"
          class="w-full border rounded-4xl p-2"
          rows="4"
          :class="{ 'border-red-500': errors.message }"
        ></textarea>
        <div v-if="errors.message" class="text-red-500 text-xs">{{ errors.message }}</div>

        <button
          type="submit"
          class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700"
          :disabled="isSubmitting"
        >
          {{ isSubmitting ? "Sending..." : "Send" }}
        </button>
      </form>
    </section>
  </transition>
</template>

<script setup>
import { reactive, ref } from "vue";

const props = defineProps({
  isActive: Boolean,
});

const form = reactive({
  name: "",
  email: "",
  message: "",
});

const errors = reactive({
  name: null,
  email: null,
  message: null,
});

const isSubmitting = ref(false);

function validateEmail(email) {
  const re = /\S+@\S+\.\S+/;
  return re.test(email);
}

function submitForm() {
  errors.name = null;
  errors.email = null;
  errors.message = null;

  let valid = true;

  if (!form.name.trim()) {
    errors.name = "Name is required.";
    valid = false;
  }
  if (!form.email.trim()) {
    errors.email = "Email is required.";
    valid = false;
  } else if (!validateEmail(form.email)) {
    errors.email = "Email is invalid.";
    valid = false;
  }
  if (!form.message.trim()) {
    errors.message = "Message is required.";
    valid = false;
  }

  if (!valid) return;

  isSubmitting.value = true;

  setTimeout(() => {
    alert(`Thank you, ${form.name}! Your message has been sent.`);
    form.name = "";
    form.email = "";
    form.message = "";
    isSubmitting.value = false;
  }, 1500);
}
</script>
