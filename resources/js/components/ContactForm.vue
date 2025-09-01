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
      class="liquid-glass text-white max-w-6xl mx-auto rounded-4xl p-8 mt-4 shadow-lg"
    >
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Contact Information -->
        <div class="space-y-6">
          <div>
            <h2 class="text-3xl font-bold mb-4 text-white">
              {{ trans("contact.get_in_touch") }}
            </h2>
            <p class="text-white/50 text-lg leading-relaxed mb-6">
              {{ trans("contact.contact_description") }}
            </p>
          </div>

          <div class="space-y-4">
            <div class="flex items-center space-x-3">
              <div
                class="w-13 h-13 gradient-color rounded-full flex items-center justify-center"
              >
                <Icon name="mail" class="h-7 w-7" />
              </div>
              <div>
                <p class="font-semibold text-white">
                  {{ trans("common.email") }}
                </p>
                <p class="text-white/50">contact@findemich.eu</p>
              </div>
            </div>

            <div class="flex items-center space-x-3">
              <div
                class="w-13 h-13 gradient-color rounded-full flex items-center justify-center"
              >
                <Icon name="phone" class="h-7 w-7" />
              </div>
              <div>
                <p class="font-semibold text-white">
                  {{ trans("contact.phone_number") }}
                </p>
                <p class="text-white/50">+1 (555) 123-4567</p>
              </div>
            </div>

            <div class="flex items-center space-x-3">
              <div
                class="w-13 h-13 gradient-color rounded-full flex items-center justify-center"
              >
                <Icon name="map-pin" class="h-7 w-7" />
              </div>
              <div>
                <p class="font-semibold text-white">
                  {{ trans("business.address") }}
                </p>
                <p class="text-white/50">
                  123 Business St, Suite 100<br />City, State 12345
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-gray-50 rounded-3xl p-6">
          <form @submit.prevent="submitForm" class="space-y-6" novalidate>
            <div class="space-y-4">
              <div>
                <label
                  for="name"
                  class="block text-sm font-semibold text-[#1a0d05] mb-2"
                >
                  {{ trans("contact.full_name") }} *
                </label>
                <Input
                  id="name"
                  v-model="form.name"
                  :placeholder="trans('contact.full_name_placeholder')"
                  class="w-full !rounded-2xl text-[#1a0d05]"
                  :invalid="!!errors.name"
                  size="large"
                />
                <small v-if="errors.name" class="text-red-500">{{
                  errors.name
                }}</small>
              </div>

              <div>
                <label
                  for="email"
                  class="block text-sm font-semibold text-[#1a0d05] mb-2"
                >
                  {{ trans("contact.email_address") }} *
                </label>
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  :placeholder="trans('contact.email_address_placeholder')"
                  class="w-full !rounded-2xl text-[#1a0d05]"
                  :invalid="!!errors.email"
                  size="large"
                />
                <small v-if="errors.email" class="text-red-500">{{
                  errors.email
                }}</small>
              </div>

              <div>
                <label
                  for="subject"
                  class="block text-sm font-semibold text-[#1a0d05] mb-2"
                >
                  {{ trans("contact.subject") }}
                </label>
                <Input
                  id="subject"
                  v-model="form.subject"
                  :placeholder="trans('contact.subject_placeholder')"
                  class="w-full !rounded-2xl text-[#1a0d05]"
                  size="large"
                />
              </div>

              <div>
                <label
                  for="message"
                  class="block text-sm font-semibold text-[#1a0d05] mb-2"
                >
                  {{ trans("contact.message") }} *
                </label>
                <Textarea
                  id="message"
                  v-model="form.message"
                  :placeholder="trans('contact.message_placeholder')"
                  class="w-full !rounded-2xl text-[#1a0d05]"
                  :invalid="!!errors.message"
                  rows="4"
                  auto-resize
                />
                <small v-if="errors.message" class="text-red-500">{{
                  errors.message
                }}</small>
              </div>

              <!-- Simple Captcha -->
              <div>
                <label class="block text-sm font-semibold text-[#1a0d05] mb-2">
                  {{
                    trans("contact.security_check", {
                      question: captcha.question,
                    })
                  }}
                </label>
                <Input
                  v-model="captcha.userAnswer"
                  :placeholder="trans('contact.captcha_answer_placeholder')"
                  class="w-full !rounded-2xl text-[#1a0d05]"
                  :invalid="!!errors.captcha"
                  size="large"
                />
                <small v-if="errors.captcha" class="text-red-500">{{
                  errors.captcha
                }}</small>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
              <Button
                type="submit"
                :disabled="isSubmitting"
                class="flex-1 !rounded-3xl"
                size="normal"
                variant="gradient"
                :loading="isSubmitting"
              >
                <i class="pi pi-send mr-2"></i>
                {{ isSubmitting ? "Senden..." : trans("contact.send_message") }}
              </Button>

              <Button
                type="button"
                @click="resetForm"
                severity="secondary"
                variant="default"
                class="!rounded-3xl"
                size="normal"
                :style="{ borderColor: '#1a0d05', color: '#1a0d05' }"
              >
                <i class="pi pi-refresh mr-2"></i>
                {{ trans("common.reset") }}
              </Button>
            </div>
          </form>
        </div>
      </div>

      <!-- Success Message -->
      <div
        v-if="showSuccessMessage"
        class="mt-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-4xl"
      >
        <div class="flex items-center">
          <i class="pi pi-check-circle mr-2 text-green-600"></i>
          {{ trans("contact.message_sent_success") }}
        </div>
      </div>
    </section>
  </transition>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import Textarea from "./ui/textarea/Textarea.vue";
import Button from "./ui/button/Button.vue";
import Input from "./ui/input/Input.vue";
import { useTranslations } from "@/composables/useTranslations";
import Icon from "./Icon.vue";

const props = defineProps({
  isActive: Boolean,
});

const form = reactive({
  name: "",
  email: "",
  subject: "",
  message: "",
});

const errors = reactive({
  name: null,
  email: null,
  message: null,
  captcha: null,
});

const captcha = reactive({
  question: "",
  userAnswer: "",
});

const isSubmitting = ref(false);
const showSuccessMessage = ref(false);
const { trans } = useTranslations();

// Fetch captcha from backend
async function fetchCaptcha() {
  try {
    const response = await fetch("/api/contact/captcha");
    const data = await response.json();

    captcha.question = data.question;
    captcha.userAnswer = "";
  } catch (error) {
    console.error("Error fetching captcha:", error);
  }
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validateForm() {
  // Reset errors
  Object.keys(errors).forEach((key) => (errors[key] = null));

  let valid = true;

  if (!form.name.trim()) {
    errors.name = "Vollständiger Name ist erforderlich";
    valid = false;
  } else if (form.name.trim().length < 2) {
    errors.name = "Name muss mindestens 2 Zeichen lang sein";
    valid = false;
  }

  if (!form.email.trim()) {
    errors.email = "E-Mail-Adresse ist erforderlich";
    valid = false;
  } else if (!validateEmail(form.email)) {
    errors.email = "Bitte geben Sie eine gültige E-Mail-Adresse ein";
    valid = false;
  }

  if (!form.message.trim()) {
    errors.message = "Nachricht ist erforderlich";
    valid = false;
  } else if (form.message.trim().length < 10) {
    errors.message = "Nachricht muss mindestens 10 Zeichen lang sein";
    valid = false;
  }

  if (!captcha.userAnswer.trim()) {
    errors.captcha = "Bitte lösen Sie das Rechenproblem";
    valid = false;
  }

  return valid;
}

async function submitForm() {
  if (!validateForm()) return;

  isSubmitting.value = true;
  showSuccessMessage.value = false;

  try {
    const response = await fetch("/api/contact", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN":
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",
      },
      body: JSON.stringify({
        name: form.name,
        email: form.email,
        subject: form.subject || "Contact Form Inquiry",
        message: form.message,
        captcha_answer: captcha.userAnswer,
      }),
    });

    const data = await response.json();

    if (response.ok && data.success) {
      showSuccessMessage.value = true;
      resetForm();

      // Hide success message after 5 seconds
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 5000);
    } else {
      // Handle validation errors
      if (data.errors) {
        Object.keys(data.errors).forEach((field) => {
          if (field === "captcha_answer") {
            errors.captcha = data.errors[field][0];
          } else {
            errors[field] = data.errors[field][0];
          }
        });
      } else {
        // Show generic error message
        errors.message =
          data.message ||
          "Beim Senden Ihrer Nachricht ist ein Fehler aufgetreten.";
      }

      // Generate new captcha if there was a captcha error
      if (data.errors?.captcha_answer) {
        await fetchCaptcha();
      }
    }
  } catch (error) {
    console.error("Contact form submission error:", error);
    errors.message =
      "Netzwerkfehler. Bitte überprüfen Sie Ihre Verbindung und versuchen Sie es erneut.";
  } finally {
    isSubmitting.value = false;
  }
}

function resetForm() {
  form.name = "";
  form.email = "";
  form.subject = "";
  form.message = "";
  Object.keys(errors).forEach((key) => (errors[key] = null));
  fetchCaptcha();
}

// Initialize captcha when component mounts
onMounted(() => {
  fetchCaptcha();
});
</script>
