<template>
  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Partner bearbeiten"
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
                maxlength="100"
                required
                placeholder="Titel des Partners eingeben"
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
                placeholder="Name des Inhabers (optional)"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.name_of_owner" class="mt-1" />
            </div>

            <!-- Kategorie -->
            <div>
              <p
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
                    placeholder="Kategorie auswählen"
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
                placeholder="Beschreibung des Partners eingeben"
                class="w-full min-h-[100px] border border-white/20 rounded-2xl"
              />
              <InputError :message="errors.description" class="mt-1" />
            </div>

            <!-- Bilder hochladen -->
            <div class="md:col-span-2">
              <Label
                for="images"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Partner-Bilder
              </Label>

              <!-- Aktuelle Bilder -->
              <div
                v-if="partner.images && partner.images.length > 0"
                class="mb-4"
              >
                <p class="text-sm text-gray-300 mb-2">Aktuelle Bilder:</p>
                <div class="grid grid-cols-6 md:grid-cols-9 gap-4 mb-4">
                  <div
                    v-for="image in partner.images.filter(
                      (img) => !imagesToRemove.includes(img.id)
                    )"
                    :key="image.id"
                    class="relative"
                  >
                    <img
                      :src="getImageUrl(image.path)"
                      :alt="partner.title"
                      class="w-full h-24 object-cover rounded-lg border border-white/20"
                    />
                    <button
                      type="button"
                      @click="removeExistingImage(image.id)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-xl hover:bg-red-600"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>

              <!-- Einzelnes Bild (Fallback) -->
              <div v-else-if="partner.image" class="mb-4">
                <p class="text-sm text-gray-300 mb-2">Aktuelles Bild:</p>
                <img
                  :src="getImageUrl(partner.image)"
                  :alt="partner.title"
                  class="w-32 h-32 object-cover rounded-lg border border-[#e3e3e0]"
                />
              </div>

              <input
                type="file"
                id="images"
                @change="handleImagesUpload"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp"
                multiple
                class="block w-full text-sm text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/30"
              />
              <p class="mt-1 text-xs text-gray-300">
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

              <!-- Neue Bildvorschau -->
              <div v-if="selectedImages.length > 0" class="mt-4">
                <p class="text-sm text-gray-300 mb-2">
                  Neue Bilder zum Hinzufügen:
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div
                    v-for="(image, index) in selectedImages"
                    :key="index"
                    class="relative"
                  >
                    <img
                      :src="image.preview"
                      :alt="`Vorschau ${index + 1}`"
                      class="w-full h-24 object-cover rounded-lg border border-[#e3e3e0]"
                    />
                    <button
                      type="button"
                      @click="removeSelectedImage(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                    >
                      ×
                    </button>
                  </div>
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
            v-model="locationData"
            :user="props.user"
            class="mb-6"
          />

          <!-- Manuelle Felder -->
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
                step="any"
                min="-90"
                max="90"
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
                step="any"
                min="-180"
                max="180"
                required
                placeholder="Längengrad eingeben"
                class="w-full rounded-2xl bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-white/40"
              />
              <InputError :message="errors.longitude" class="mt-1" />
            </div>
          </div>
        </div>

        <!-- Meta‑Informationen -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <h3 class="text-lg font-semibold text-white mb-4">Informationen</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <span class="font-medium text-gray-300">Erstellt von:</span>
              <p class="text-white">{{ partner.user?.name }}</p>
            </div>
            <div>
              <span class="font-medium text-gray-300">Erstellt am:</span>
              <p class="text-white">{{ formatDate(partner.created_at) }}</p>
            </div>
            <div>
              <span class="font-medium text-gray-300">Zuletzt bearbeitet:</span>
              <p class="text-white">{{ formatDate(partner.updated_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Aktionen -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <div
            class="flex flex-col sm:flex-row items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4"
          >
            <Button
              type="button"
              class="bg-white/20 border border-white/20 text-white"
              @click="router.visit(route('partners.index'))"
            >
              Abbrechen
            </Button>
            <Button
              type="submit"
              :disabled="processing"
              size="normal"
              variant="gradient"
            >
              <Icon name="pencil" class="h-4 w-4 mr-2" />
              {{ processing ? "Aktualisieren..." : "Partner aktualisieren" }}
            </Button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Icon from "@/components/Icon.vue";
import DashboardNavbar from "@/components/DashboardNavbar.vue";
import InputError from "@/components/InputError.vue";
import LocationPicker from "@/components/LocationPicker.vue";
import { useCategories } from "@/composables/useCategories";
import { useTranslations } from "@/composables/useTranslations";
import { useImageCompression } from "@/composables/useImageCompression";

const page = usePage();

// Use composables
const { categories } = useCategories();
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
const { trans } = useTranslations();

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
    href: route("dashboard"),
  },
];

// Shadcn Components
import Input from "@/components/ui/input/Input.vue";
import Textarea from "@/components/ui/textarea/Textarea.vue";
import Button from "@/components/ui/button/Button.vue";
import Select from "@/components/ui/select/Select.vue";
import SelectTrigger from "@/components/ui/select/SelectTrigger.vue";
import SelectValue from "@/components/ui/select/SelectValue.vue";
import SelectContent from "@/components/ui/select/SelectContent.vue";
import SelectGroup from "@/components/ui/select/SelectGroup.vue";
import SelectItem from "@/components/ui/select/SelectItem.vue";

interface User {
  id: number;
  name: string;
  email: string;
}

interface PartnerImage {
  id: number;
  partner_id: number;
  path: string;
  sort_order: number;
}

interface Partner {
  id: number;
  title: string;
  name_of_owner: string | null;
  category: string;
  description: string;
  image: string | null;
  images?: PartnerImage[];
  city: string;
  zip_code: string;
  longitude: number;
  latitude: number;
  created_by: number;
  created_at: string;
  updated_at: string;
  user?: User;
}

interface SelectedImage {
  file: File;
  preview: string;
}

interface LocationData {
  city: string;
  zipCode: string;
  latitude: number;
  longitude: number;
}

interface Props {
  partner: Partner;
  user?: {
    city?: string;
    zip_code?: string;
    latitude?: number;
    longitude?: number;
  };
}

const props = defineProps<Props>();

const selectedImages = ref<SelectedImage[]>([]);
const imagesToRemove = ref<number[]>([]);

const form = useForm({
  title: props.partner.title,
  name_of_owner: props.partner.name_of_owner || "",
  category: props.partner.category,
  description: props.partner.description,
  image: null as File | null,
  images: [] as File[],
  remove_images: [] as number[],
  city: props.partner.city,
  zip_code: props.partner.zip_code,
  latitude: props.partner.latitude,
  longitude: props.partner.longitude,
});

const locationData = ref<LocationData | null>(null);

// Initialize location data
onMounted(() => {
  locationData.value = {
    city: props.partner.city,
    zipCode: props.partner.zip_code,
    latitude: props.partner.latitude,
    longitude: props.partner.longitude,
  };
});

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

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString();
};

const handleImagesUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const currentImageCount =
      (props.partner.images?.length || 0) - imagesToRemove.value.length;
    const maxNewImages = Math.min(15 - currentImageCount, target.files.length);

    const filesToProcess = Array.from(target.files).slice(0, maxNewImages);

    try {
      // Show progress
      uploadProgress.value.show = true;
      uploadProgress.value.overall = 0;
      uploadProgress.value.current = 0;
      uploadProgress.value.total = filesToProcess.length;
      uploadProgress.value.status = "Starting compression...";

      // Show loading state
      const loadingImages = filesToProcess.map((file) => ({
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
      const newImages: SelectedImage[] = compressedFiles.map((file) => {
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

const removeExistingImage = (imageId: number) => {
  imagesToRemove.value.push(imageId);
  form.remove_images = imagesToRemove.value;
};

const getImageUrl = (imagePath: string): string => {
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }
  return `/storage/${imagePath}`;
};

const submit = () => {
  const formData = new FormData();

  formData.append("title", form.title);
  formData.append("name_of_owner", form.name_of_owner || "");
  formData.append("category", form.category);
  formData.append("description", form.description);
  formData.append("city", form.city);
  formData.append("zip_code", form.zip_code);
  formData.append("latitude", form.latitude.toString());
  formData.append("longitude", form.longitude.toString());
  formData.append("_method", "PUT");

  // Add image files
  if (form.images && form.images.length > 0) {
    form.images.forEach((file, index) => {
      if (file instanceof File) {
        formData.append(`images[${index}]`, file);
      }
    });
  }

  // Add images to remove
  if (form.remove_images && form.remove_images.length > 0) {
    form.remove_images.forEach((imageId, index) => {
      formData.append(`remove_images[${index}]`, imageId.toString());
    });
  }

  // Submit using Inertia router with manual FormData
  router.post(route("partners.update", props.partner.id), formData, {
    onSuccess: () => {
      selectedImages.value.forEach((img) => URL.revokeObjectURL(img.preview));
      selectedImages.value = [];
      imagesToRemove.value = [];
    },
    onError: (errors) => {},
  });
};
</script>
