<script setup lang="ts">
import { cn } from "@/lib/utils";
import { computed } from "vue";

// Only import icons we actually use
import {
  X,
  MapPin,
  Pencil,
  CheckCircle,
  ChevronLeft,
  ChevronRight,
  Trash,
  User,
  Plus,
  AlertTriangle,
  ArrowLeft,
  Users,
  TrendingUp,
  Clock,
  ChartBar,
  Search,
  Languages,
} from "lucide-vue-next";
import Chart from "primevue/chart";

interface Props {
  name: string;
  class?: string;
  size?: number | string;
  color?: string;
  strokeWidth?: number | string;
}

const props = withDefaults(defineProps<Props>(), {
  class: "",
  size: 16,
  strokeWidth: 2,
});

const className = computed(() => cn("h-4 w-4", props.class));

// Map icon names to components
const iconMap: Record<string, any> = {
  "x-mark": X,
  "map-pin": MapPin,
  pencil: Pencil,
  "check-circle": CheckCircle,
  "chevron-left": ChevronLeft,
  "chevron-right": ChevronRight,
  trash: Trash,
  user: User,
  users: Users,
  "chart-bar": ChartBar,
  clock: Clock,
  "trending-up": TrendingUp,
  plus: Plus,
  "exclamation-triangle": AlertTriangle,
  "arrow-left": ArrowLeft,
  search: Search,
  language: Languages,
};

const icon = computed(() => {
  return iconMap[props.name] || MapPin; // fallback to MapPin
});
</script>

<template>
  <component
    :is="icon"
    :class="className"
    :size="size"
    :stroke-width="strokeWidth"
    :color="color"
  />
</template>
