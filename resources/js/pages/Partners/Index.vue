<template>
  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Partner"
      title-icon="home"
      :home-route="route('dashboard')"
      :navigation-links="navigationLinks"
    />

    <div class="space-y-6 max-w-6xl mx-auto">
      <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
        <h1 class="mb-2 text-2xl font-semibold text-white truncate">
          Partner‑Verwaltungs‑Dashboard
        </h1>
        <p class="text-gray-300">
          Verwalten Sie Ihre Partner‑Standorte, Kategorien und sehen Sie
          Analysen von dieser zentralen Übersicht.
        </p>
      </div>

      <!-- Partner‑Grid -->
      <div
        v-if="partners.data.length > 0"
        class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3 mt-4"
      >
        <Link
          v-for="partner in partners.data"
          :key="partner.id"
          :href="route('partners.show', partner.id)"
          class="liquid-glass text-white rounded-4xl shadow-lg overflow-hidden backdrop-blur-sm hover:shadow-xl transition-all duration-300 cursor-pointer block"
        >
          <!-- Partner‑Bilder -->
          <div
            v-if="partner.images && partner.images.length > 0"
            class="relative h-48 overflow-hidden group"
          >
            <div
              :ref="(el) => (imageContainers[partner.id] = el as HTMLElement)"
              class="flex overflow-x-auto h-full scrollbar-hide snap-x snap-mandatory"
              style="scrollbar-width: none; -ms-overflow-style: none"
            >
              <div
                v-for="image in partner.images"
                :key="image.id"
                class="flex-none w-full h-48 snap-start"
              >
                <img
                  :src="getImageUrl(image.path)"
                  :alt="partner.title"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>

            <!-- Navigations‑Pfeile (nur Desktop) -->
            <div v-if="partner.images.length > 1" class="hidden md:block">
              <!-- Linker Pfeil -->
              <button
                @click.stop="scrollImages(partner.id, 'left')"
                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
              >
                <Icon name="chevron-left" class="w-4 h-4" />
              </button>

              <!-- Rechter Pfeil -->
              <button
                @click.stop="scrollImages(partner.id, 'right')"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
              >
                <Icon name="chevron-right" class="w-4 h-4" />
              </button>
            </div>

            <!-- Bild‑Zähler -->
            <div
              v-if="partner.images.length > 1"
              class="absolute top-2 right-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full"
            >
              {{ partner.images.length }} Fotos
            </div>

            <!-- Navigations‑Punkte -->
            <div
              v-if="partner.images.length > 1"
              class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1"
            >
              <div
                v-for="(image, index) in partner.images"
                :key="`dot-${image.id}`"
                class="w-2 h-2 rounded-full bg-white bg-opacity-60"
              ></div>
            </div>
          </div>

          <!-- Fallback, einzelnes Bild -->
          <div v-else-if="partner.image" class="relative">
            <img
              :src="getImageUrl(partner.image)"
              :alt="partner.title"
              class="w-full h-48 object-cover"
            />
          </div>

          <div class="p-6">
            <!-- Kategorie und Aktionen -->
            <div class="mb-4 flex items-start justify-between">
              <span
                class="inline-flex items-center rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-white border border-white/20"
              >
                {{ getCategoryName(partner.category) }}
              </span>
              <div class="flex space-x-2">
                <Link
                  :href="route('partners.edit', partner.id)"
                  @click.stop
                  class="text-gray-300 hover:text-white transition-colors"
                >
                  <Icon name="pencil" class="h-4 w-4" />
                </Link>
                <button
                  @click.stop.prevent="confirmDelete(partner)"
                  class="text-red-400 hover:text-red-300 transition-colors"
                >
                  <Icon name="trash" class="h-4 w-4" />
                </button>
              </div>
            </div>

            <!-- Partner‑Infos -->
            <h3 class="mb-2 text-lg font-semibold text-white">
              {{ partner.title }}
            </h3>
            <p v-if="partner.name_of_owner" class="mb-2 text-sm text-gray-300">
              Inhaber: {{ partner.name_of_owner }}
            </p>
            <p class="mb-3 text-sm text-gray-300 line-clamp-2">
              {{ partner.description }}
            </p>

            <!-- Partner‑Details -->
            <div class="space-y-1 text-xs text-gray-400">
              <div class="flex items-center">
                <Icon name="map-pin" class="mr-1 h-3 w-3" />
                {{ partner.city }}, {{ partner.zip_code }}
              </div>
              <div class="flex items-center">
                <Icon name="user" class="mr-1 h-3 w-3" />
                Erstellt von {{ partner.user?.name }} am
                {{ formatDate(partner.created_at) }}
              </div>
            </div>
          </div>
        </Link>
      </div>

      <!-- Leerer Zustand -->
      <div
        v-else
        class="liquid-glass text-white rounded-4xl p-12 text-center shadow-lg"
      >
        <Icon name="map-pin" class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-semibold text-white">Keine Partner</h3>
        <p class="mt-1 text-sm text-gray-300">
          Starten Sie, indem Sie einen neuen Partner anlegen.
        </p>
        <div class="mt-6">
          <Link
            :href="route('partners.create')"
            class="inline-flex items-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20 transition-colors backdrop-blur-sm border border-white/20"
          >
            <Icon name="plus" class="mr-1 h-4 w-4" />
            Partner erstellen
          </Link>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="partners.links && partners.links.length > 3"
        class="flex justify-center"
      >
        <div class="liquid-glass text-white rounded-4xl p-4 shadow-lg">
          <nav class="flex items-center justify-center space-x-2">
            <template v-for="link in partners.links" :key="link.label">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'px-3 py-2 text-sm rounded-md transition-colors',
                  link.active
                    ? 'bg-white/20 text-white'
                    : 'text-gray-300 hover:bg-white/10',
                ]"
              >
                {{ cleanLabel(link.label) }}
              </Link>
              <span
                v-else
                :class="[
                  'px-3 py-2 text-sm rounded-md cursor-not-allowed',
                  'text-gray-500',
                ]"
              >
                {{ cleanLabel(link.label) }}
              </span>
            </template>
          </nav>
        </div>
      </div>
    </div>

    <!-- Bestätigungsmodal für Löschung -->
    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <!-- Hintergrund -->
        <div
          class="fixed inset-0 bg-black/50 transition-opacity"
          @click="showDeleteModal = false"
        ></div>

        <!-- Modal-Inhalt -->
        <div
          class="relative liquid-glass text-white rounded-4xl shadow-xl max-w-lg w-full p-6 backdrop-blur-lg"
        >
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500/20"
              >
                <Icon
                  name="exclamation-triangle"
                  class="h-6 w-6 text-red-400"
                />
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-white">Partner löschen</h3>
              <p class="mt-2 text-sm text-gray-300">
                Sind Sie sicher, dass Sie "{{ partnerToDelete?.title }}" löschen
                möchten? Diese Aktion kann nicht rückgängig gemacht werden.
              </p>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-300 bg-white/10 border border-white/20 rounded-md hover:bg-white/20 transition-colors backdrop-blur-sm"
            >
              Abbrechen
            </button>
            <button
              @click="deletePartner"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600/80 rounded-md hover:bg-red-600 transition-colors backdrop-blur-sm"
            >
              Löschen
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.scrollbar-hide {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE und Edge */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none; /* Chrome, Safari und Opera */
}
</style>

<script setup lang="ts">
import { ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Icon from "@/components/Icon.vue";
import DashboardNavbar from "@/components/DashboardNavbar.vue";
import { useCategories } from "@/composables/useCategories";
import { useTranslations } from "@/composables/useTranslations";

const page = usePage();
const user = page.props.auth.user as User;

const navigationLinks = [
  {
    label: "Partner",
    href: route("partners.index"),
  },
  {
    label: "Partner erstellen",
    href: route("partners.create"),
  },
  {
    label: "Startseite",
    href: route("dashboard"),
  },
];

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

interface Props {
  partners: {
    data: Partner[];
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
  };
}

declare global {
  namespace App.Data {
    interface PageProps {
      flash?: {
        success?: string;
        error?: string;
      };
    }
  }
}

defineProps<Props>();

const showDeleteModal = ref(false);
const partnerToDelete = ref<Partner | null>(null);
const imageContainers = ref<{ [key: number]: HTMLElement | null }>({});

const { trans } = useTranslations();
const { categories, getCategoryName } = useCategories();

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString("de-DE");
};

const confirmDelete = (partner: Partner) => {
  partnerToDelete.value = partner;
  showDeleteModal.value = true;
};

const deletePartner = () => {
  if (partnerToDelete.value) {
    router.delete(route("partners.destroy", partnerToDelete.value.id));
    showDeleteModal.value = false;
    partnerToDelete.value = null;
  }
};

const cleanLabel = (label: string): string => {
  return label
    .replace(/&laquo;/g, "‹")
    .replace(/&raquo;/g, "›")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&amp;/g, "&");
};

const getImageUrl = (imagePath: string): string => {
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }
  return `/storage/${imagePath}`;
};

const scrollImages = (partnerId: number, direction: "left" | "right") => {
  const container = imageContainers.value[partnerId];
  if (!container) return;

  const scrollAmount = container.clientWidth;
  const currentScroll = container.scrollLeft;

  if (direction === "left") {
    container.scrollTo({
      left: currentScroll - scrollAmount,
      behavior: "smooth",
    });
  } else {
    container.scrollTo({
      left: currentScroll + scrollAmount,
      behavior: "smooth",
    });
  }
};
</script>
