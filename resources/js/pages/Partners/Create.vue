<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link :href="route('partners.index')"
          class="text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
        <Icon name="arrow-left" class="h-5 w-5" />
        </Link>
        <Heading>Create Partner</Heading>
      </div>
    </template>

    <div class="w-full mx-auto space-y-6 p-6">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information -->
        <div
          class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
          <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Basic Information</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
              <label for="title" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Title <span class="text-red-500">*</span>
              </label>
              <InputText id="title" v-model="form.title" required placeholder="Enter partner title" class="w-full"
                :invalid="!!errors.title" />
              <InputError :message="errors.title" class="mt-1" />
            </div>

            <!-- Name of Owner -->
            <div>
              <label for="name_of_owner" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Name of Owner
              </label>
              <InputText id="name_of_owner" v-model="form.name_of_owner" placeholder="Enter owner name (optional)"
                class="w-full" :invalid="!!errors.name_of_owner" />
              <InputError :message="errors.name_of_owner" class="mt-1" />
            </div>

            <!-- Category -->
            <div>
              <label for="category" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Category <span class="text-red-500">*</span>
              </label>
              <Select v-model="form.category" :options="categories" option-label="name" option-value="id"
                placeholder="Select a category" class="w-full" :invalid="!!errors.category">
                <template #option="{ option }">
                  <div class="flex items-center">
                    <span class="mr-2">{{ option.icon }}</span>
                    {{ option.name }}
                  </div>
                </template>
              </Select>
              <InputError :message="errors.category" class="mt-1" />
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
              <label for="description" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Description <span class="text-red-500">*</span>
              </label>
              <Textarea id="description" v-model="form.description" rows="4" required
                placeholder="Enter partner description" class="w-full" :invalid="!!errors.description" />
              <InputError :message="errors.description" class="mt-1" />
            </div>
          </div>
        </div>

        <!-- Location Information -->
        <div
          class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
          <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Location Information</h3>

          <!-- Location Picker -->
          <LocationPicker v-model="locationData" class="mb-6" />

          <!-- Manual Location Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="city" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                City <span class="text-red-500">*</span>
              </label>
              <InputText id="city" v-model="form.city" required placeholder="Enter city name" class="w-full"
                :invalid="!!errors.city" />
              <InputError :message="errors.city" class="mt-1" />
            </div>

            <div>
              <label for="zip_code" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Zip Code <span class="text-red-500">*</span>
              </label>
              <InputText id="zip_code" v-model="form.zip_code" required placeholder="Enter zip code" class="w-full"
                :invalid="!!errors.zip_code" />
              <InputError :message="errors.zip_code" class="mt-1" />
            </div>

            <div>
              <label for="latitude" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Latitude <span class="text-red-500">*</span>
              </label>
              <InputNumber id="latitude" v-model="form.latitude" :min="-90" :max="90" :fraction-digits="8" required
                placeholder="Enter latitude" class="w-full" :invalid="!!errors.latitude" />
              <InputError :message="errors.latitude" class="mt-1" />
            </div>

            <div>
              <label for="longitude" class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Longitude <span class="text-red-500">*</span>
              </label>
              <InputNumber id="longitude" v-model="form.longitude" :min="-180" :max="180" :fraction-digits="8" required
                placeholder="Enter longitude" class="w-full" :invalid="!!errors.longitude" />
              <InputError :message="errors.longitude" class="mt-1" />
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div
          class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
          <div class="flex items-center justify-end space-x-4">
            <Button type="button" severity="secondary" outlined @click="$inertia.visit(route('partners.index'))">
              Cancel
            </Button>
            <Button type="submit" :loading="processing" :disabled="processing" class="min-w-32">
              <template #icon>
                <Icon name="plus" class="h-4 w-4" />
              </template>
              {{ processing ? 'Creating...' : 'Create Partner' }}
            </Button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import LocationPicker from '@/components/LocationPicker.vue'
import categories from '@/data/categories.json'

// PrimeVue Components
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import Textarea from 'primevue/textarea'
import InputNumber from 'primevue/inputnumber'
import Button from 'primevue/button'

interface LocationData {
  city: string
  zipCode: string
  latitude: number
  longitude: number
}

const form = useForm({
  title: '',
  name_of_owner: '',
  category: '',
  description: '',
  city: '',
  zip_code: '',
  latitude: 0,
  longitude: 0,
})

const locationData = ref<LocationData | null>(null)

// Watch for location picker changes and update form
watch(locationData, (newLocation) => {
  if (newLocation) {
    form.city = newLocation.city
    form.zip_code = newLocation.zipCode
    form.latitude = newLocation.latitude
    form.longitude = newLocation.longitude
  }
}, { deep: true })

// Watch for manual form changes and update location picker
watch([() => form.city, () => form.zip_code, () => form.latitude, () => form.longitude], () => {
  if (form.latitude && form.longitude) {
    locationData.value = {
      city: form.city,
      zipCode: form.zip_code,
      latitude: form.latitude,
      longitude: form.longitude,
    }
  }
})

const { errors, processing } = form

const submit = () => {
  form.post(route('partners.store'), {
    onSuccess: () => {
      // Form will redirect on success
    },
    onError: () => {
      // Errors will be shown in form
    }
  })
}
</script>
