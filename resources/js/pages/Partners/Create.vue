<template>
  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Partner erstellen"
      title-icon="home"
      :home-route="route('dashboard')"
      :navigation-links="navigationLinks"
    />

    <div class="w-full max-w-6xl mx-auto space-y-6">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Grundinformationen -->
        <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
          <h3 class="text-lg font-semibold text-white mb-6">
            Grundinformationen
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Titel -->
            <div class="md:col-span-2">
              <Label
                for="title"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Titel <span class="text-red-400">*</span>
              </Label>
              <Input
                id="title"
                v-model="form.title"
                required
                maxlength="100"
                placeholder="Gib den Titel des Partners ein"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.title" class="mt-1" />
            </div>

            <!-- Name des Inhabers -->
            <div>
              <Label
                for="name_of_owner"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Name des Inhabers
              </Label>
              <Input
                id="name_of_owner"
                maxlength="100"
                v-model="form.name_of_owner"
                placeholder="Optional: Name des Inhabers"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.name_of_owner" class="mt-1" />
            </div>

            <!-- Kategorie -->
            <div>
              <p
                name="category"
                for="category"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Kategorie <span class="text-red-400">*</span>
              </p>
              <Select
                name="category"
                id="category"
                v-model="form.category"
                required
              >
                <SelectTrigger
                  class="w-full text-md rounded-2xl bg-white/20 border border-white/20 text-white px-4 py-5.5"
                >
                  <SelectValue
                    placeholder="Wähle eine Kategorie"
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
                      {{ category.icon }} {{ category.name }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>

            <!-- Beschreibung -->
            <div class="md:col-span-2">
              <Label
                for="description"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Beschreibung <span class="text-red-400">*</span>
              </Label>
              <Textarea
                id="description"
                v-model="form.description"
                rows="4"
                maxlength="500"
                required
                placeholder="Beschreibe den Partner"
                class="w-full min-h-[100px] border border-white/20"
              />
              <InputError :message="errors.description" class="mt-1" />
            </div>

            <!-- Bilder hochladen -->
            <div class="md:col-span-2">
              <Label
                for="images"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Partner‑Bilder
              </Label>
              <input
                type="file"
                id="images"
                @change="handleImagesUpload"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp"
                multiple
                class="block w-full text-sm text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/30"
              />
              <p class="mt-1 text-xs text-gray-400">
                Optional: Lade bis zu 15 Bilder hoch (JPEG, PNG, JPG, GIF, SVG,
                WebP, max. 5MB pro Datei) - Bilder werden automatisch zu WebP
                komprimiert
              </p>
              <InputError :message="errors.images" class="mt-1" />

              <!-- Progress Bar -->
              <div
                v-if="uploadProgress.show"
                class="mt-4 p-4 bg-white/5 rounded-lg border border-white/20"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-white font-medium">
                    Compressing Images
                  </span>
                  <span class="text-xs text-white/70">
                    {{ uploadProgress.current }}/{{ uploadProgress.total }}
                  </span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-white/10 rounded-full h-2 mb-2">
                  <div
                    class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-300 ease-out"
                    :style="{ width: `${uploadProgress.overall}%` }"
                  ></div>
                </div>

                <!-- Status Text -->
                <div class="flex items-center justify-between">
                  <span class="text-xs text-white/60">
                    {{ uploadProgress.status }}
                  </span>
                  <span class="text-xs text-white/60">
                    {{ uploadProgress.overall }}%
                  </span>
                </div>

                <!-- Current File -->
                <div v-if="uploadProgress.currentFile" class="mt-1">
                  <span class="text-xs text-blue-400 font-mono">
                    {{ uploadProgress.currentFile }}
                  </span>
                </div>
              </div>

              <!-- Bildvorschau -->
              <div
                v-if="selectedImages.length > 0"
                class="mt-4 grid grid-cols-6 md:grid-cols-9 gap-4"
              >
                <div
                  v-for="(image, index) in selectedImages"
                  :key="index"
                  class="relative"
                >
                  <div class="relative">
                    <img
                      :src="image.preview"
                      :alt="`Vorschau ${index + 1}`"
                      class="w-full h-24 object-cover rounded-lg border border-[#e3e3e0]"
                      :class="{ 'opacity-50': image.loading }"
                    />
                    <!-- Loading indicator -->
                    <div
                      v-if="image.loading"
                      class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-lg"
                    >
                      <div
                        class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"
                      ></div>
                    </div>
                  </div>
                  <button
                    v-if="!image.loading"
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

        <!-- Standortinformationen -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <h3 class="text-lg font-semibold text-white mb-6">
            Standortinformationen
          </h3>

          <!-- Karten‑Auswahl -->
          <LocationPicker
            name="locationPicker"
            v-model="locationData"
            :user="props.user"
            class="mb-6"
          />

          <!-- Manuelle Eingabe -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <Label
                for="city"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Stadt <span class="text-red-400">*</span>
              </Label>
              <Input
                id="city"
                v-model="form.city"
                required
                placeholder="Stadt eingeben"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.city" class="mt-1" />
            </div>

            <div>
              <Label
                for="zip_code"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Postleitzahl <span class="text-red-400">*</span>
              </Label>
              <Input
                id="zip_code"
                v-model="form.zip_code"
                required
                placeholder="PLZ eingeben"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.zip_code" class="mt-1" />
            </div>

            <div>
              <Label
                for="latitude"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Breitengrad <span class="text-red-400">*</span>
              </Label>
              <Input
                id="latitude"
                v-model="form.latitude"
                type="number"
                min="-90"
                max="90"
                step="any"
                required
                placeholder="Breitengrad eingeben"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.latitude" class="mt-1" />
            </div>

            <div>
              <Label
                for="longitude"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Längengrad <span class="text-red-400">*</span>
              </Label>
              <Input
                id="longitude"
                v-model="form.longitude"
                type="number"
                min="-180"
                max="180"
                step="any"
                required
                placeholder="Längengrad eingeben"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.longitude" class="mt-1" />
            </div>
          </div>
        </div>

        <!-- Aktionen -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <div class="flex items-center justify-end space-x-4">
            <Button
              type="button"
              size="normal"
              @click="$inertia.visit(route('partners.index'))"
              class="rounded-2xl bg-white/10 border border-white/20 text-white hover:bg-white/20 transition-colors"
            >
              Abbrechen
            </Button>
            <Button
              type="submit"
              :disabled="processing"
              variant="gradient"
              size="normal"
              class="min-w-32 rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white border-0 transition-colors disabled:opacity-50"
            >
              <Icon name="plus" class="h-4 w-4 mr-2" />
              {{ processing ? "Wird erstellt..." : "Partner erstellen" }}
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
import { useCategories } from "@/composables/useCategories";
import { useTranslations } from "@/composables/useTranslations";
import { useImageCompression } from "@/composables/useImageCompression";
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
  loading?: boolean;
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

const { trans } = useTranslations();
const { categories, getCategoryName } = useCategories();
const { processImages, getCompressionPresets, createCustomSettings } =
  useImageCompression();

// Progress state
const uploadProgress = ref({
  show: false,
  overall: 0,
  current: 0,
  total: 0,
  status: "",
  currentFile: "",
});

const handleImagesUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const maxImages = Math.min(
      15 - selectedImages.value.length,
      target.files.length
    );

    const filesToProcess = Array.from(target.files).slice(0, maxImages);

    try {
      // Show progress
      uploadProgress.value.show = true;
      uploadProgress.value.overall = 0;
      uploadProgress.value.current = 0;
      uploadProgress.value.total = filesToProcess.length;
      uploadProgress.value.status = "Starting compression...";

      // Show loading state
      const loadingImages = filesToProcess.map((file, index) => ({
        file,
        preview: URL.createObjectURL(file),
        loading: true,
      }));

      selectedImages.value = [...selectedImages.value, ...loadingImages];

      // Progress callback
      const onProgress = (
        overall: number,
        current: number,
        total: number,
        status?: string,
        filename?: string
      ) => {
        uploadProgress.value.overall = Math.round(overall);
        uploadProgress.value.current = current;
        uploadProgress.value.total = total;
        uploadProgress.value.currentFile = filename || "";

        switch (status) {
          case "processing":
            uploadProgress.value.status = `Processing ${filename}...`;
            break;
          case "completed":
            uploadProgress.value.status = `Completed ${filename}`;
            break;
          case "error":
            uploadProgress.value.status = `Error processing ${filename}`;
            break;
          case "skipped":
            uploadProgress.value.status = `Skipped non-image file`;
            break;
          case "skipped_optimized":
            uploadProgress.value.status = `Already optimized`;
            break;
        }
      };

      // Process images to WebP with standard quality settings
      const compressionSettings = createCustomSettings({
        quality: 0.8,
        maxWidth: 1920,
        maxHeight: 1080,
      });

      const compressedFiles = await processImages(
        filesToProcess,
        compressionSettings,
        onProgress
      );

      // Update with compressed images
      const newImages: SelectedImage[] = compressedFiles.map((file, index) => {
        const preview = URL.createObjectURL(file);
        return { file, preview };
      });

      // Remove loading images and add compressed ones
      selectedImages.value = [
        ...selectedImages.value.slice(
          0,
          selectedImages.value.length - filesToProcess.length
        ),
        ...newImages,
      ];

      form.images = selectedImages.value.map((img) => img.file);

      // Hide progress after a short delay
      setTimeout(() => {
        uploadProgress.value.show = false;
      }, 1500);
    } catch (error) {
      console.error("Error processing images:", error);
      uploadProgress.value.status = "Error occurred during compression";

      // Remove loading images on error
      selectedImages.value = selectedImages.value.slice(
        0,
        selectedImages.value.length - filesToProcess.length
      );

      // Hide progress after error
      setTimeout(() => {
        uploadProgress.value.show = false;
      }, 3000);
    }

    // Clear input
    target.value = "";
  }
};

const removeSelectedImage = (index: number) => {
  // Revoke the object URL to free memory
  URL.revokeObjectURL(selectedImages.value[index].preview);
  selectedImages.value.splice(index, 1);
  form.images = selectedImages.value.map((img) => img.file);
};

const submit = () => {
  form.post(route("partners.store"), {
    forceFormData: true,
    onSuccess: (response) => {},
    onError: (errors) => {
      console.error("Form submission errors:", errors);
    },
  });
};
</script>
