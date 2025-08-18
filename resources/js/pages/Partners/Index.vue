<template>
  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Partners"
      title-icon="home"
      :home-route="route('dashboard')"
      :navigation-links="navigationLinks"
    />

    <div class="space-y-6 max-w-6xl mx-auto">
      <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
        <h1 class="mb-2 text-2xl font-semibold text-white">
          Partner Management Dashboard
        </h1>
        <p class="text-gray-300">
          Manage your partner locations, categories, and view analytics from
          this central hub.
        </p>
      </div>
      <!-- Success Message -->
      <div
        v-if="$page.props.flash?.success"
        class="liquid-glass text-white rounded-4xl p-4 mt-4 shadow-lg"
      >
        <div class="flex">
          <div class="rounded-full bg-green-500/20 p-2">
            <Icon name="check-circle" class="h-5 w-5 text-green-400" />
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-white">
              {{ $page.props.flash.success }}
            </p>
          </div>
        </div>
      </div>

      <!-- Partners Grid -->
      <div
        v-if="partners.data.length > 0"
        class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3 mt-4"
      >
        <div
          v-for="partner in partners.data"
          :key="partner.id"
          class="liquid-glass text-white rounded-4xl shadow-lg overflow-hidden backdrop-blur-sm"
        >
          <!-- Partner Images -->
          <div
            v-if="partner.images && partner.images.length > 0"
            class="relative h-48 overflow-hidden group"
          >
            <div
              :ref="(el) => (imageContainers[partner.id] = el)"
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

            <!-- Navigation Arrows (Desktop only) -->
            <div v-if="partner.images.length > 1" class="hidden md:block">
              <!-- Left Arrow -->
              <button
                @click="scrollImages(partner.id, 'left')"
                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
              >
                <Icon name="chevron-left" class="w-4 h-4" />
              </button>

              <!-- Right Arrow -->
              <button
                @click="scrollImages(partner.id, 'right')"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
              >
                <Icon name="chevron-right" class="w-4 h-4" />
              </button>
            </div>

            <!-- Image counter -->
            <div
              v-if="partner.images.length > 1"
              class="absolute top-2 right-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full"
            >
              {{ partner.images.length }} photos
            </div>

            <!-- Navigation dots -->
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

          <!-- Fallback to legacy single image -->
          <div v-else-if="partner.image" class="relative">
            <img
              :src="getImageUrl(partner.image)"
              :alt="partner.title"
              class="w-full h-48 object-cover"
            />
          </div>

          <div class="p-6">
            <!-- Category and Actions -->
            <div class="mb-4 flex items-start justify-between">
              <span
                class="inline-flex items-center rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-white border border-white/20"
              >
                {{ getCategoryName(partner.category) }}
              </span>
              <div class="flex space-x-2">
                <Link
                  :href="route('partners.edit', partner.id)"
                  class="text-gray-300 hover:text-white transition-colors"
                >
                  <Icon name="pencil" class="h-4 w-4" />
                </Link>
                <button
                  @click="confirmDelete(partner)"
                  class="text-red-400 hover:text-red-300 transition-colors"
                >
                  <Icon name="trash" class="h-4 w-4" />
                </button>
              </div>
            </div>

            <!-- Partner Info -->
            <h3 class="mb-2 text-lg font-semibold text-white">
              {{ partner.title }}
            </h3>
            <p v-if="partner.name_of_owner" class="mb-2 text-sm text-gray-300">
              Owner: {{ partner.name_of_owner }}
            </p>
            <p class="mb-3 text-sm text-gray-300 line-clamp-2">
              {{ partner.description }}
            </p>

            <!-- Partner Details -->
            <div class="space-y-1 text-xs text-gray-400">
              <div class="flex items-center">
                <Icon name="map-pin" class="mr-1 h-3 w-3" />
                {{ partner.city }}, {{ partner.zip_code }}
              </div>
              <div class="flex items-center">
                <Icon name="user" class="mr-1 h-3 w-3" />
                Created by {{ partner.user?.name }} on
                {{ formatDate(partner.created_at) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="liquid-glass text-white rounded-4xl p-12 text-center shadow-lg"
      >
        <Icon name="map-pin" class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-semibold text-white">No partners</h3>
        <p class="mt-1 text-sm text-gray-300">
          Get started by creating a new partner.
        </p>
        <div class="mt-6">
          <Link
            :href="route('partners.create')"
            class="inline-flex items-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20 transition-colors backdrop-blur-sm border border-white/20"
          >
            <Icon name="plus" class="mr-1 h-4 w-4" />
            Create Partner
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

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black/50 transition-opacity"
          @click="showDeleteModal = false"
        ></div>

        <!-- Modal Content -->
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
              <h3 class="text-lg font-semibold text-white">Delete partner</h3>
              <p class="mt-2 text-sm text-gray-300">
                Are you sure you want to delete "{{ partnerToDelete?.title }}"?
                This action cannot be undone.
              </p>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-300 bg-white/10 border border-white/20 rounded-md hover:bg-white/20 transition-colors backdrop-blur-sm"
            >
              Cancel
            </button>
            <button
              @click="deletePartner"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600/80 rounded-md hover:bg-red-600 transition-colors backdrop-blur-sm"
            >
              Delete
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
  -ms-overflow-style: none; /* IE and Edge */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none; /* Chrome, Safari and Opera */
}
</style>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import AppLayout from "@/layouts/AppLayout.vue";
import Icon from "@/components/Icon.vue";
import DashboardNavbar from "@/components/DashboardNavbar.vue";
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

// Extend the global Page interface to include flash messages
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

const getCategoryName = (categoryId: string): string => {
  const category = categories.find((cat) => cat.id === categoryId);
  return category ? `${category.icon} ${category.name}` : categoryId;
};

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString();
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
  // Replace HTML entities with clean text
  return label
    .replace(/&laquo;/g, "‹")
    .replace(/&raquo;/g, "›")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&amp;/g, "&");
};

const getImageUrl = (imagePath: string): string => {
  // If it's an external URL (starts with http/https), return as is
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }
  // Otherwise, treat as local storage path
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
