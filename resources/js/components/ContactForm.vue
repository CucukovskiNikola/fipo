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
            <h2 class="text-3xl font-bold mb-4 text-white">Get in Touch</h2>
            <p class="text-white/50 text-lg leading-relaxed mb-6">
              Ready to promote your business or find local partners? We'd love to hear from you. 
              Send us a message and we'll get back to you as soon as possible.
            </p>
          </div>
          
          <div class="space-y-4">
            <div class="flex items-center space-x-3">
              <div class="w-13 h-13 gradient-color rounded-full flex items-center justify-center">
                <i class="pi pi-envelope text-white"></i>
              </div>
              <div>
                <p class="font-semibold text-white">Email</p>
                <p class="text-white/50">contact@businessplatform.com</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-3">
              <div class="w-13 h-13 gradient-color rounded-full flex items-center justify-center">
                <i class="pi pi-phone text-white"></i>
              </div>
              <div>
                <p class="font-semibold text-white">Phone</p>
                <p class="text-white/50">+1 (555) 123-4567</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-3">
              <div class="w-13 h-13 gradient-color rounded-full flex items-center justify-center">
                <i class="pi pi-map-marker text-white"></i>
              </div>
              <div>
                <p class="font-semibold text-white">Address</p>
                <p class="text-white/50">123 Business St, Suite 100<br>City, State 12345</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-gray-50 rounded-3xl p-6">
          <form @submit.prevent="submitForm" class="space-y-6" novalidate>
            <div class="space-y-4">
              <div>
                <label for="name" class="block text-sm font-semibold text-[#1a0d05] mb-2">
                  Full Name *
                </label>
                <InputText
                  id="name"
                  v-model="form.name"
                  placeholder="Enter your full name"
                  class="w-full !rounded-2xl bg-white"
                  :invalid="!!errors.name"
                  size="large"
                />
                <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
              </div>

              <div>
                <label for="email" class="block text-sm font-semibold text-[#1a0d05] mb-2">
                  Email Address *
                </label>
                <InputText
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="Enter your email address"
                  class="w-full !rounded-2xl"
                  :invalid="!!errors.email"
                  size="large"
                />
                <small v-if="errors.email" class="text-red-500">{{ errors.email }}</small>
              </div>

              <div>
                <label for="subject" class="block text-sm font-semibold text-[#1a0d05] mb-2">
                  Subject
                </label>
                <InputText
                  id="subject"
                  v-model="form.subject"
                  placeholder="What is this about?"
                  class="w-full !rounded-2xl"
                  size="large"
                />
              </div>

              <div>
                <label for="message" class="block text-sm font-semibold text-[#1a0d05] mb-2">
                  Message *
                </label>
                <Textarea
                  id="message"
                  v-model="form.message"
                  placeholder="Tell us more about your inquiry..."
                  class="w-full !rounded-2xl"
                  :invalid="!!errors.message"
                  rows="4"
                  auto-resize
                />
                <small v-if="errors.message" class="text-red-500">{{ errors.message }}</small>
              </div>

              <!-- Simple Captcha -->
              <div>
                <label class="block text-sm font-semibold text-[#1a0d05] mb-2">
                  Security Check: What is {{ captcha.question }}? *
                </label>
                <InputText
                  v-model="captcha.userAnswer"
                  placeholder="Enter your answer"
                  class="w-full !rounded-2xl"
                  :invalid="!!errors.captcha"
                  size="large"
                />
                <small v-if="errors.captcha" class="text-red-500">{{ errors.captcha }}</small>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
              <Button
                type="submit"
                :disabled="isSubmitting"
                class="flex-1 !rounded-3xl"
                size="large"
                variant="gradient"
                :loading="isSubmitting"
              >
                <i class="pi pi-send mr-2"></i>
                {{ isSubmitting ? 'Sending...' : 'Send Message' }}
              </Button>
              
              <Button
                type="button"
                @click="resetForm"
                severity="secondary"
                variant="default"
                class="!rounded-3xl"
                size="large"
                :style="{ borderColor: '#1a0d05', color: '#1a0d05' }"
              >
                <i class="pi pi-refresh mr-2"></i>
                Reset
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
          <strong>Success!</strong> Thank you for your message! We'll get back to you soon.
        </div>
      </div>
    </section>
  </transition>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from "./ui/button/Button.vue";

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
  correctAnswer: 0,
  userAnswer: "",
});

const isSubmitting = ref(false);
const showSuccessMessage = ref(false);

// Generate random captcha
function generateCaptcha() {
  const num1 = Math.floor(Math.random() * 10) + 1;
  const num2 = Math.floor(Math.random() * 10) + 1;
  const operations = [
    { symbol: '+', operation: (a, b) => a + b },
    { symbol: '-', operation: (a, b) => a - b },
    { symbol: '×', operation: (a, b) => a * b }
  ];
  
  const randomOp = operations[Math.floor(Math.random() * operations.length)];
  
  captcha.question = `${num1} ${randomOp.symbol} ${num2}`;
  captcha.correctAnswer = randomOp.operation(num1, num2);
  captcha.userAnswer = "";
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validateForm() {
  // Reset errors
  Object.keys(errors).forEach(key => errors[key] = null);
  
  let valid = true;

  if (!form.name.trim()) {
    errors.name = "Full name is required";
    valid = false;
  } else if (form.name.trim().length < 2) {
    errors.name = "Name must be at least 2 characters";
    valid = false;
  }

  if (!form.email.trim()) {
    errors.email = "Email address is required";
    valid = false;
  } else if (!validateEmail(form.email)) {
    errors.email = "Please enter a valid email address";
    valid = false;
  }

  if (!form.message.trim()) {
    errors.message = "Message is required";
    valid = false;
  } else if (form.message.trim().length < 10) {
    errors.message = "Message must be at least 10 characters";
    valid = false;
  }

  if (!captcha.userAnswer.trim()) {
    errors.captcha = "Please solve the math problem";
    valid = false;
  } else if (parseInt(captcha.userAnswer) !== captcha.correctAnswer) {
    errors.captcha = "Incorrect answer. Please try again.";
    generateCaptcha(); // Generate new captcha
    valid = false;
  }

  return valid;
}

function submitForm() {
  if (!validateForm()) return;

  isSubmitting.value = true;
  showSuccessMessage.value = false;

  // Simulate API call
  setTimeout(() => {
    showSuccessMessage.value = true;
    resetForm();
    isSubmitting.value = false;
    
    // Hide success message after 5 seconds
    setTimeout(() => {
      showSuccessMessage.value = false;
    }, 5000);
  }, 1500);
}

function resetForm() {
  form.name = "";
  form.email = "";
  form.subject = "";
  form.message = "";
  Object.keys(errors).forEach(key => errors[key] = null);
  generateCaptcha();
}

// Initialize captcha when component mounts
onMounted(() => {
  generateCaptcha();
});
</script>
