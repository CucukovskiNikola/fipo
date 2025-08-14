<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link :href="route('partners.index')"
          class="text-[#706f6c] hover:text-[#1b1b18]">
        <Icon name="arrow-left" class="h-5 w-5" />
        </Link>
        <Heading title="Edit Partner" />
      </div>
    </template>

    <div class="w-full mx-auto space-y-6 p-6">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information -->
        <div
          class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
          <h3 class="text-lg font-semibold text-[#1b1b18] mb-6">Basic Information</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
              <label for="title" class="block text-sm font-medium text-[#706f6c] mb-2">
                Title <span class="text-red-500">*</span>
              </label>
              <InputText id="title" v-model="form.title" required placeholder="Enter partner title" class="w-full"
                :invalid="!!errors.title" />
              <InputError :message="errors.title" class="mt-1" />
            </div>

            <!-- Name of Owner -->
            <div>
              <label for="name_of_owner" class="block text-sm font-medium text-[#706f6c] mb-2">
                Name of Owner
              </label>
              <InputText id="name_of_owner" v-model="form.name_of_owner" placeholder="Enter owner name (optional)"
                class="w-full" :invalid="!!errors.name_of_owner" />
              <InputError :message="errors.name_of_owner" class="mt-1" />
            </div>

            <!-- Category -->
            <div>
              <label for="category" class="block text-sm font-medium text-[#706f6c] mb-2">
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
              <label for="description" class="block text-sm font-medium text-[#706f6c] mb-2">
                Description <span class="text-red-500">*</span>
              </label>
              <Textarea id="description" v-model="form.description" rows="4" required
                placeholder="Enter partner description" class="w-full" :invalid="!!errors.description" />
              <InputError :message="errors.description" class="mt-1" />
            </div>

            <!-- Image Upload -->
            <div class="md:col-span-2">
              <label for="images" class="block text-sm font-medium text-[#706f6c] mb-2">
                Partner Images
              </label>

              <!-- Current Images Display -->
              <div v-if="partner.images && partner.images.length > 0" class="mb-4">
                <p class="text-sm text-[#706f6c] mb-2">Current Images:</p>
                <div class="grid grid-cols-6 md:grid-cols-9 gap-4 mb-4">
                  <div v-for="image in partner.images.filter(img => !imagesToRemove.includes(img.id))" :key="image.id"
                    class="relative">
                    <img :src="getImageUrl(image.path)" :alt="partner.title"
                      class="w-full h-24 object-cover rounded-lg border border-[#e3e3e0]">
                    <button type="button" @click="removeExistingImage(image.id)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                      ×
                    </button>
                  </div>
                </div>
              </div>

              <!-- Fallback to legacy single image -->
              <div v-else-if="partner.image" class="mb-4">
                <p class="text-sm text-[#706f6c] mb-2">Current Image:</p>
                <img :src="getImageUrl(partner.image)" :alt="partner.title"
                  class="w-32 h-32 object-cover rounded-lg border border-[#e3e3e0]">
              </div>

              <input type="file" id="images" @change="handleImagesUpload"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" multiple
                class="block w-full text-sm text-[#706f6c] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#FDFDFC] file:text-[#1b1b18] hover:file:bg-[#f8f8f7]" />
              <p class="mt-1 text-xs text-[#706f6c]">
                Optional: Upload up to 15 images for this partner (JPEG, PNG, JPG, GIF, SVG, max 5MB each)
              </p>
              <InputError :message="errors.images" class="mt-1" />

              <!-- New Image Previews -->
              <div v-if="selectedImages.length > 0" class="mt-4">
                <p class="text-sm text-[#706f6c] mb-2">New Images to Add:</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div v-for="(image, index) in selectedImages" :key="index" class="relative">
                    <img :src="image.preview" :alt="`Preview ${index + 1}`"
                      class="w-full h-24 object-cover rounded-lg border border-[#e3e3e0]">
                    <button type="button" @click="removeSelectedImage(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Location Information -->
        <div
          class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
          <h3 class="text-lg font-semibold text-[#1b1b18] mb-6">Location Information</h3>

          <!-- Location Picker -->
          <LocationPicker v-model="locationData" :user="props.user" class="mb-6" />

          <!-- Manual Location Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="city" class="block text-sm font-medium text-[#706f6c] mb-2">
                City <span class="text-red-500">*</span>
              </label>
              <InputText id="city" v-model="form.city" required placeholder="Enter city name" class="w-full"
                :invalid="!!errors.city" />
              <InputError :message="errors.city" class="mt-1" />
            </div>

            <div>
              <label for="zip_code" class="block text-sm font-medium text-[#706f6c] mb-2">
                Zip Code <span class="text-red-500">*</span>
              </label>
              <InputText id="zip_code" v-model="form.zip_code" required placeholder="Enter zip code" class="w-full"
                :invalid="!!errors.zip_code" />
              <InputError :message="errors.zip_code" class="mt-1" />
            </div>

            <div>
              <label for="latitude" class="block text-sm font-medium text-[#706f6c] mb-2">
                Latitude <span class="text-red-500">*</span>
              </label>
              <InputNumber id="latitude" v-model="form.latitude" :min="-90" :max="90" :fraction-digits="8" required
                placeholder="Enter latitude" class="w-full" :invalid="!!errors.latitude" />
              <InputError :message="errors.latitude" class="mt-1" />
            </div>

            <div>
              <label for="longitude" class="block text-sm font-medium text-[#706f6c] mb-2">
                Longitude <span class="text-red-500">*</span>
              </label>
              <InputNumber id="longitude" v-model="form.longitude" :min="-180" :max="180" :fraction-digits="8" required
                placeholder="Enter longitude" class="w-full" :invalid="!!errors.longitude" />
              <InputError :message="errors.longitude" class="mt-1" />
            </div>
          </div>
        </div>

        <!-- Meta Information -->
        <div
          class="rounded-lg bg-[#FDFDFC] p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
          <h3 class="text-lg font-semibold text-[#1b1b18] mb-4">Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <span class="font-medium text-[#706f6c]">Created by:</span>
              <p class="text-[#1b1b18]">{{ partner.user?.name}}</p>
            </div>
            <div>
              <span class="font-medium text-[#706f6c]">Created at:</span>
              <p class="text-[#1b1b18]">{{ formatDate(partner.created_at) }}</p>
            </div>
            <div>
              <span class="font-medium text-[#706f6c]">Last updated:</span>
              <p class="text-[#1b1b18]">{{ formatDate(partner.updated_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div
          class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
          <div class="flex items-center justify-end space-x-4">
            <Button type="button" severity="secondary" outlined @click="$inertia.visit(route('partners.index'))">
              Cancel
            </Button>
            <Button type="submit" :loading="processing" :disabled="processing" class="min-w-32">
              <template #icon>
                <Icon name="pencil" class="h-4 w-4" />
              </template>
              {{ processing ? 'Updating...' : 'Update Partner' }}
            </Button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'
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

interface User {
  id: number
  name: string
  email: string
}

interface PartnerImage {
  id: number
  partner_id: number
  path: string
  sort_order: number
}

interface Partner {
  id: number
  title: string
  name_of_owner: string | null
  category: string
  description: string
  image: string | null
  images?: PartnerImage[]
  city: string
  zip_code: string
  longitude: number
  latitude: number
  created_by: number
  created_at: string
  updated_at: string
  user?: User
}

interface SelectedImage {
  file: File
  preview: string
}

interface LocationData {
  city: string
  zipCode: string
  latitude: number
  longitude: number
}

interface Props {
  partner: Partner
  user?: {
    city?: string
    zip_code?: string
    latitude?: number
    longitude?: number
  }
}

const props = defineProps<Props>()

const selectedImages = ref<SelectedImage[]>([])
const imagesToRemove = ref<number[]>([])

const form = useForm({
  title: props.partner.title,
  name_of_owner: props.partner.name_of_owner || '',
  category: props.partner.category,
  description: props.partner.description,
  image: null as File | null,
  images: [] as File[],
  remove_images: [] as number[],
  city: props.partner.city,
  zip_code: props.partner.zip_code,
  latitude: props.partner.latitude,
  longitude: props.partner.longitude,
})

const locationData = ref<LocationData | null>(null)

// Initialize location data
onMounted(() => {
  locationData.value = {
    city: props.partner.city,
    zipCode: props.partner.zip_code,
    latitude: props.partner.latitude,
    longitude: props.partner.longitude,
  }
})

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

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString()
}

const handleImagesUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const newImages: SelectedImage[] = []
    const currentImageCount = (props.partner.images?.length || 0) - imagesToRemove.value.length
    const maxNewImages = Math.min(15 - currentImageCount, target.files.length)

    for (let i = 0; i < maxNewImages; i++) {
      const file = target.files[i]
      const preview = URL.createObjectURL(file)
      newImages.push({ file, preview })
    }

    selectedImages.value = [...selectedImages.value, ...newImages]
    form.images = selectedImages.value.map(img => img.file)
  }
}

const removeSelectedImage = (index: number) => {
  // Revoke the object URL to free memory
  URL.revokeObjectURL(selectedImages.value[index].preview)
  selectedImages.value.splice(index, 1)
  form.images = selectedImages.value.map(img => img.file)
}

const removeExistingImage = (imageId: number) => {
  imagesToRemove.value.push(imageId)
  form.remove_images = imagesToRemove.value
}

const getImageUrl = (imagePath: string): string => {
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }
  return `/storage/${imagePath}`
}

const submit = () => {
  const formData = new FormData()

  formData.append('title', form.title)
  formData.append('name_of_owner', form.name_of_owner || '')
  formData.append('category', form.category)
  formData.append('description', form.description)
  formData.append('city', form.city)
  formData.append('zip_code', form.zip_code)
  formData.append('latitude', form.latitude.toString())
  formData.append('longitude', form.longitude.toString())
  formData.append('_method', 'PUT')

  // Add image files
  if (form.images && form.images.length > 0) {
    form.images.forEach((file, index) => {
      if (file instanceof File) {
        formData.append(`images[${index}]`, file)
      }
    })
  }

  // Add images to remove
  if (form.remove_images && form.remove_images.length > 0) {
    form.remove_images.forEach((imageId, index) => {
      formData.append(`remove_images[${index}]`, imageId.toString())
    })
  }

  // Submit using Inertia router with manual FormData
  router.post(route('partners.update', props.partner.id), formData, {
    onSuccess: () => {
      selectedImages.value.forEach(img => URL.revokeObjectURL(img.preview))
      selectedImages.value = []
      imagesToRemove.value = []
    },
    onError: (errors) => {
      console.log('Form submission errors:', errors)
    }
  })
}
</script>