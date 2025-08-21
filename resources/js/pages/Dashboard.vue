<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import { type BreadcrumbItem, type User } from "@/types";
import { Link, Head, usePage, router } from "@inertiajs/vue3";
import Icon from "@/components/Icon.vue";
import DashboardNavbar from "@/components/DashboardNavbar.vue";
import { computed, ref, onMounted, onUnmounted } from "vue";
import { route } from "ziggy-js";

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

interface Partner {
  id: number;
  title: string;
  created_at: string;
  created_by: string;
  action: string;
}

interface Stat {
  name: string;
  value: string;
  icon: string;
  change: string;
  changeType: "increase" | "decrease" | "neutral";
}

interface Props {
  stats: Stat[];
  recentPartners: Partner[];
  totalUsers: number;
  activeUsers: number;
  meta?: {
    title: string;
    description: string;
    keywords?: string;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: "Dashboard",
    href: "/dashboard",
  },
];

const quickActions = [
  {
    title: "Create Partner",
    description: "Add a new partner location",
    icon: "plus",
    route: "partners.create",
  },
  {
    title: "View All Partners",
    description: "Browse and manage partners",
    icon: "users",
    route: "partners.index",
  },
  {
    title: "Edit Partners",
    description: "Update partner information",
    icon: "pencil",
    route: "partners.index",
  },
  {
    title: "Delete Partners",
    description: "Remove partner locations",
    icon: "trash",
    route: "partners.index",
  },
  {
    title: "Map Overview",
    description: "View partners on map",
    icon: "map",
    route: "map",
  },
];

const formatTimeAgo = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInMinutes = Math.floor(
    (now.getTime() - date.getTime()) / (1000 * 60)
  );

  if (diffInMinutes < 1) return "Just now";
  if (diffInMinutes < 60)
    return `${diffInMinutes} minute${diffInMinutes === 1 ? "" : "s"} ago`;

  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24)
    return `${diffInHours} hour${diffInHours === 1 ? "" : "s"} ago`;

  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 30)
    return `${diffInDays} day${diffInDays === 1 ? "" : "s"} ago`;

  return date.toLocaleDateString();
};
</script>

<template>
  <Head>
    <title>{{ meta?.title || "findemich - Admin Dashboard" }}</title>
    <meta
      name="description"
      :content="
        meta?.description ||
        'findemich Admin Dashboard - Manage business partners, view analytics, and oversee platform operations with comprehensive admin tools.'
      "
    />
  </Head>

  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Dashboard"
      title-icon="home"
      :home-route="route('dashboard')"
      :navigation-links="navigationLinks"
    />

    <div class="space-y-6 max-w-6xl mx-auto">
      <!-- Welcome Section -->
      <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
        <h1 class="mb-2 text-2xl font-semibold text-white">
          Partner Management Dashboard
        </h1>
        <p class="text-gray-300">
          Manage your partner locations, categories, and view analytics from
          this central hub.
        </p>
      </div>

      <!-- Quick Actions -->
      <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
        <h2 class="mb-6 text-lg font-semibold text-white">Quick Actions</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <Link
            v-for="action in quickActions"
            :key="action.title"
            :href="route(action.route)"
            class="group rounded-lg border border-white/20 p-6 transition-all hover:border-white/40 hover:bg-white/10 backdrop-blur-sm"
          >
            <div class="flex items-start space-x-4">
              <div
                class="rounded-md bg-white/10 p-2 group-hover:bg-white/20 transition-colors"
              >
                <Icon :name="action.icon" class="h-6 w-6 text-white" />
              </div>
              <div>
                <h3 class="font-medium text-white">{{ action.title }}</h3>
                <p class="mt-1 text-sm text-gray-300">
                  {{ action.description }}
                </p>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="stat in props.stats"
          :key="stat.name"
          class="liquid-glass text-white rounded-4xl p-6 shadow-lg"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-300">{{ stat.name }}</p>
              <p class="text-2xl font-semibold text-white">{{ stat.value }}</p>
            </div>
            <div class="rounded-md bg-white/10 p-3">
              <Icon :name="stat.icon" class="h-6 w-6 text-white" />
            </div>
          </div>
          <div class="mt-4 flex items-center text-sm">
            <span
              :class="[
                stat.changeType === 'increase'
                  ? 'text-green-400'
                  : stat.changeType === 'decrease'
                    ? 'text-red-400'
                    : 'text-gray-300',
              ]"
            >
              {{ stat.change }}
            </span>
          </div>
        </div>
      </div>

      <!-- Recent Activity & Overview -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Activity -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <div class="mb-6 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Recent Activity</h2>
            <Link
              :href="route('partners.index')"
              class="text-sm text-gray-300 hover:text-white transition-colors"
            >
              View all →
            </Link>
          </div>
          <div class="space-y-4">
            <div
              v-if="props.recentPartners.length === 0"
              class="text-center py-8"
            >
              <Icon
                name="building-office"
                class="mx-auto h-8 w-8 text-gray-400"
              />
              <p class="mt-2 text-sm text-gray-300">
                No recent partner activity
              </p>
            </div>
            <div
              v-else
              v-for="partner in props.recentPartners"
              :key="partner.id"
              class="flex items-center space-x-3 rounded-md bg-white/10 p-3 backdrop-blur-sm"
            >
              <div class="rounded-full bg-green-500/20 p-2">
                <Icon name="building-office" class="h-4 w-4 text-green-400" />
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-white">
                  "{{ partner.title }}" {{ partner.action }} by
                  {{ partner.created_by }}
                </p>
                <p class="text-xs text-gray-300">
                  {{ formatTimeAgo(partner.created_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- System Overview -->
        <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
          <h2 class="mb-6 text-lg font-semibold text-white">System Overview</h2>
          <div class="space-y-4">
            <div
              class="flex items-center justify-between rounded-md bg-white/10 p-3 backdrop-blur-sm"
            >
              <div class="flex items-center space-x-3">
                <Icon name="check-circle" class="h-5 w-5 text-green-400" />
                <span class="text-sm font-medium text-white"
                  >Partner
                                    System</span
                >
              </div>
              <span class="text-xs text-gray-300">Operational</span>
            </div>
            <div
              class="flex items-center justify-between rounded-md bg-white/10 p-3 backdrop-blur-sm"
            >
              <div class="flex items-center space-x-3">
                <Icon name="users" class="h-5 w-5 text-blue-400" />
                <span class="text-sm font-medium text-white">Total Users</span>
              </div>
              <span class="text-xs text-gray-300">{{ props.totalUsers }}</span>
            </div>
            <div
              class="flex items-center justify-between rounded-md bg-white/10 p-3 backdrop-blur-sm"
            >
              <div class="flex items-center space-x-3">
                <Icon name="clock" class="h-5 w-5 text-purple-400" />
                <span class="text-sm font-medium text-white"
                  >Active This
                                    Week</span
                >
              </div>
              <span class="text-xs text-gray-300">{{ props.activeUsers }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
