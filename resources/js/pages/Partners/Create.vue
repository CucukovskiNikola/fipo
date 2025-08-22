<template>
  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Create Partner"
      title-icon="home"
      :home-route="route('dashboard')"
      :navigation-links="navigationLinks"
    />

    <div class="w-full max-w-6xl mx-auto space-y-6">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information -->
        <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
          <h3 class="text-lg font-semibold text-white mb-6">
            Basic Information
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
              <Label
                for="title"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Title <span class="text-red-400">*</span>
              </Label>
              <Input
                id="title"
                v-model="form.title"
                required
                placeholder="Enter partner title"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.title" class="mt-1" />
            </div>

            <!-- Name of Owner -->
            <div>
              <Label
                for="name_of_owner"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Name of Owner
              </Label>
              <Input
                id="name_of_owner"
                v-model="form.name_of_owner"
                placeholder="Enter owner name (optional)"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.name_of_owner" class="mt-1" />
            </div>

            <!-- Category -->
            <div>
              <Label
                for="category"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Category <span class="text-red-400">*</span>
              </Label>
              <Select id="category" v-model="form.category" required>
                <SelectTrigger
                  class="w-full text-md rounded-2xl bg-white/20 border border-white/20 text-white px-4 py-5.5"
                >
                  <SelectValue
                    placeholder="Select a category"
                    class="text-white"
                  />
                </SelectTrigger>
                <SelectContent
                  class="bg-white/20 backdrop-blur-xl text-white rounded-2xl border-none"
                >
                  <SelectGroup>
                    <SelectItem
                      v-for="category in categories"
                      :key="category.id"
                      :value="category.id"
                      class="rounded-2xl text-md"
                    >
                      {{ category.icon }} {{ category.name }}</SelectItem
                    >
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
              <label
                for="description"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Description <span class="text-red-400">*</span>
              </label>
              <Textarea
                id="description"
                v-model="form.description"
                rows="4"
                required
                placeholder="Enter partner description"
                class="w-full min-h-[100px] border border-white/20"
              />
              <InputError :message="errors.description" class="mt-1" />
            </div>

            <!-- Image Upload -->
            <div class="md:col-span-2">
              <label
                for="images"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Partner Images
              </label>
              <input
                type="file"
                id="images"
                @change="handleImagesUpload"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"
                multiple
                class="block w-full text-sm text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/30"
              />
              <p class="mt-1 text-xs text-gray-400">
                Optional: Upload up to 15 images for this partner (JPEG, PNG,
                JPG, GIF, SVG, max 5MB each)
              </p>
              <InputError :message="errors.images" class="mt-1" />

              <!-- Image Previews -->
              <div
                v-if="selectedImages.length > 0"
                class="mt-4 grid grid-cols-6 md:grid-cols-9 gap-4"
              >
                <div
                  v-for="(image, index) in selectedImages"
                  :key="index"
                  class="relative"
                >
                  <img
                    :src="image.preview"
                    :alt="`Preview ${index + 1}`"
                    class="w-full h-24 object-cover rounded-lg border border-[#e3e3e0]"
                  />
                  <button
                    type="button"
                    @click="removeSelectedImage(index)"
                    class="absolute top-1 left-17 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                  >
                    ×
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Location Information -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <h3 class="text-lg font-semibold text-white mb-6">
            Location Information
          </h3>

          <!-- Location Picker -->
          <LocationPicker
            v-model="locationData"
            :user="props.user"
            class="mb-6"
          />

          <!-- Manual Location Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="city"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                City <span class="text-red-400">*</span>
              </label>
              <Input
                id="city"
                v-model="form.city"
                required
                placeholder="Enter city name"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.city" class="mt-1" />
            </div>

            <div>
              <label
                for="zip_code"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Zip Code <span class="text-red-400">*</span>
              </label>
              <Input
                id="zip_code"
                v-model="form.zip_code"
                required
                placeholder="Enter zip code"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.zip_code" class="mt-1" />
            </div>

            <div>
              <label
                for="latitude"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Latitude <span class="text-red-400">*</span>
              </label>
              <Input
                id="latitude"
                v-model="form.latitude"
                type="number"
                min="-90"
                max="90"
                step="0.00000001"
                required
                placeholder="Enter latitude"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.latitude" class="mt-1" />
            </div>

            <div>
              <label
                for="longitude"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Longitude <span class="text-red-400">*</span>
              </label>
              <Input
                id="longitude"
                v-model="form.longitude"
                type="number"
                min="-180"
                max="180"
                step="0.00000001"
                required
                placeholder="Enter longitude"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.longitude" class="mt-1" />
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <div class="flex items-center justify-end space-x-4">
            <Button
              type="button"
              size="normal"
              @click="$inertia.visit(route('partners.index'))"
              class="rounded-2xl bg-white/10 border border-white/20 text-white hover:bg-white/20 transition-colors"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="processing"
              variant="gradient"
              size="normal"
              class="min-w-32 rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white border-0 transition-colors disabled:opacity-50"
            >
              <Icon name="plus" class="h-4 w-4 mr-2" />
              {{ processing ? "Creating..." : "Create Partner" }}
            </Button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from "vue";
import { useForm, Link, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Icon from "@/components/Icon.vue";
import DashboardNavbar from "@/components/DashboardNavbar.vue";
import InputError from "@/components/InputError.vue";
import LocationPicker from "@/components/LocationPicker.vue";
import categories from "@/data/categories.json";
import { type User } from "@/types";

const page = usePage();
const user = page.props.auth.user as User;

// Navigation configuration
const navigationLinks = [
  {
    label: "Partners",
    href: route("partners.index"),
  },
  {
    label: "Create Partner",
    href: route("partners.create"),
  },
  {
    label: "Home",
    href: "/",
  },
];

// Shadcn/UI Components
import Input from "@/components/ui/input/Input.vue";
import Textarea from "@/components/ui/textarea/Textarea.vue";
import Button from "@/components/ui/button/Button.vue";
import Label from "@/components/ui/label/Label.vue";
import Select from "@/components/ui/select/Select.vue";
import SelectTrigger from "@/components/ui/select/SelectTrigger.vue";
import SelectValue from "@/components/ui/select/SelectValue.vue";
import SelectContent from "@/components/ui/select/SelectContent.vue";
import SelectGroup from "@/components/ui/select/SelectGroup.vue";
import SelectItem from "@/components/ui/select/SelectItem.vue";

interface SelectedImage {
  file: File;
  preview: string;
}

interface Props {
  user?: {
    city?: string;
    zip_code?: string;
    latitude?: number;
    longitude?: number;
  };
}

const props = defineProps<Props>();

interface LocationData {
  city: string;
  zipCode: string;
  latitude: number;
  longitude: number;
}

const selectedImages = ref<SelectedImage[]>([]);

const form = useForm({
  title: "",
  name_of_owner: "",
  category: "",
  description: "",
  image: null as File | null,
  images: [] as File[],
  city: "",
  zip_code: "",
  latitude: 0,
  longitude: 0,
});

const locationData = ref<LocationData | null>(null);

// Watch for location picker changes and update form
watch(
  locationData,
  (newLocation) => {
    if (newLocation) {
      form.city = newLocation.city;
      form.zip_code = newLocation.zipCode;
      form.latitude = newLocation.latitude;
      form.longitude = newLocation.longitude;
    }
  },
  { deep: true }
);

// Watch for manual form changes and update location picker
watch(
  [
    () => form.city,
    () => form.zip_code,
    () => form.latitude,
    () => form.longitude,
  ],
  () => {
    if (form.latitude && form.longitude) {
      locationData.value = {
        city: form.city,
        zipCode: form.zip_code,
        latitude: form.latitude,
        longitude: form.longitude,
      };
    }
  }
);

const { errors, processing } = form;

const handleImagesUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    console.log("Files selected:", target.files.length);
    const newImages: SelectedImage[] = [];
    const maxImages = Math.min(
      15 - selectedImages.value.length,
      target.files.length
    );

    for (let i = 0; i < maxImages; i++) {
      const file = target.files[i];
      console.log(
        "Processing file:",
        file.name,
        "Size:",
        file.size,
        "Type:",
        file.type
      );
      const preview = URL.createObjectURL(file);
      newImages.push({ file, preview });
    }

    selectedImages.value = [...selectedImages.value, ...newImages];
    form.images = selectedImages.value.map((img) => img.file);
    console.log("Total images selected:", selectedImages.value.length);
  }
};

const removeSelectedImage = (index: number) => {
  // Revoke the object URL to free memory
  URL.revokeObjectURL(selectedImages.value[index].preview);
  selectedImages.value.splice(index, 1);
  form.images = selectedImages.value.map((img) => img.file);
};

const submit = () => {
  console.log("Submitting form with images:", form.images.length);
  console.log("Form data:", {
    title: form.title,
    category: form.category,
    description: form.description,
    images_count: form.images.length,
  });

  form.post(route("partners.store"), {
    forceFormData: true,
    onSuccess: (response) => {
      console.log("Form submitted successfully:", response);
    },
    onError: (errors) => {
      console.error("Form submission errors:", errors);
    },
  });
};
</script>
