<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-white font-semibold">Cookie Audit Trail</h3>
      <button
        @click="clearAuditTrail"
        class="text-xs text-red-400 hover:text-red-300 transition-colors"
      >
        Clear Trail
      </button>
    </div>

    <div v-if="auditTrail.length === 0" class="text-center py-8 text-white/50">
      <Icon name="cookie" class="h-5 w-5" />
      <p class="text-sm">No audit trail entries yet</p>
    </div>

    <div v-else class="space-y-3 max-h-64 overflow-y-auto">
      <div
        v-for="(entry, index) in auditTrail"
        :key="index"
        class="bg-white/5 rounded-lg p-3 border border-white/10"
      >
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-3">
            <div
              class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
              :class="getActionIconClass(entry.action)"
            >
              <Icon
                name="cookie"
                :class="getActionIcon(entry.action)"
                class="text-sm"
              />
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-white text-sm font-medium">
                  {{ getActionLabel(entry.action) }}
                </span>
                <span
                  v-if="entry.cookieName"
                  class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full font-mono"
                >
                  {{ entry.cookieName }}
                </span>
              </div>
              <p class="text-white/60 text-xs">
                {{ formatTimestamp(entry.timestamp) }}
              </p>
              <div
                v-if="entry.metadata && Object.keys(entry.metadata).length > 0"
                class="mt-2"
              >
                <details class="text-xs">
                  <summary
                    class="text-white/50 hover:text-white/70 cursor-pointer"
                  >
                    Metadata
                  </summary>
                  <div
                    class="mt-1 p-2 bg-black/20 rounded text-white/60 font-mono text-xs"
                  >
                    <pre>{{ JSON.stringify(entry.metadata, null, 2) }}</pre>
                  </div>
                </details>
              </div>
            </div>
          </div>
          <div class="text-xs text-white/40">
            #{{ auditTrail.length - index }}
          </div>
        </div>
      </div>
    </div>

    <div
      class="text-xs text-white/40 text-center pt-2 border-t border-white/10"
    >
      Showing {{ auditTrail.length }} of {{ auditTrail.length }} entries
    </div>
  </div>
</template>

<script setup>
import { useCookieManager } from "@/composables/useCookieManager";
import Icon from "./Icon.vue";

const { auditTrail } = useCookieManager();

const getActionIcon = (action) => {
  const icons = {
    set: "pi-plus",
    delete: "pi-trash",
    consent_updated: "pi-check",
    consent_withdrawn: "pi-times",
    analytics_enabled: "pi-chart-line",
    marketing_enabled: "pi-bullseye",
    default: "pi-info-circle",
  };
  return `pi ${icons[action] || icons.default}`;
};

const getActionIconClass = (action) => {
  const classes = {
    set: "bg-green-500/20 text-green-400",
    delete: "bg-red-500/20 text-red-400",
    consent_updated: "bg-blue-500/20 text-blue-400",
    consent_withdrawn: "bg-orange-500/20 text-orange-400",
    analytics_enabled: "bg-purple-500/20 text-purple-400",
    marketing_enabled: "bg-pink-500/20 text-pink-400",
    default: "bg-gray-500/20 text-gray-400",
  };
  return classes[action] || classes.default;
};

const getActionLabel = (action) => {
  const labels = {
    set: "Cookie Set",
    delete: "Cookie Deleted",
    consent_updated: "Consent Updated",
    consent_withdrawn: "Consent Withdrawn",
    analytics_enabled: "Analytics Enabled",
    marketing_enabled: "Marketing Enabled",
    default: "Unknown Action",
  };
  return labels[action] || labels.default;
};

const formatTimestamp = (timestamp) => {
  const date = new Date(timestamp);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffMins = Math.floor(diffMs / (1000 * 60));
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (diffMins < 1) return "Just now";
  if (diffMins < 60) return `${diffMins}m ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  if (diffDays < 7) return `${diffDays}d ago`;

  return date.toLocaleDateString();
};

const clearAuditTrail = () => {
  if (confirm("Clear all audit trail entries? This cannot be undone.")) {
    localStorage.removeItem("cookie_audit_trail");
    auditTrail.value = [];
  }
};
</script>
