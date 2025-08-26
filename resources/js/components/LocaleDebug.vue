<template>
  <div class="fixed bottom-4 right-4 bg-black/80 text-white p-3 rounded-lg text-xs z-50 max-w-xs">
    <div class="font-bold mb-2">🔍 Locale Debug</div>
    <div class="space-y-1">
      <div><strong>Current Locale:</strong> {{ currentLocale }}</div>
      <div><strong>URL Path:</strong> {{ currentPath }}</div>
      <div><strong>Route Name:</strong> {{ routeName }}</div>
      <div><strong>Session Locale:</strong> {{ sessionLocale }}</div>
      <div><strong>Middleware:</strong> {{ middlewareInfo }}</div>
      <div><strong>Page Props:</strong> {{ JSON.stringify(pageProps) }}</div>
    </div>
    <button 
      @click="refreshPage" 
      class="mt-2 px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"
    >
      Refresh
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()

const currentLocale = computed(() => page.props.locale || 'unknown')
const sessionLocale = computed(() => {
  // Try to get from localStorage as fallback
  return localStorage.getItem('locale') || 'not-stored'
})
const currentPath = computed(() => window.location.pathname)
const routeName = computed(() => {
  const path = currentPath.value
  if (path.startsWith('/en/')) return 'English route'
  if (path.startsWith('/partners')) return 'German partners route'
  if (path.startsWith('/en/partners')) return 'English partners route'
  if (path === '/') return 'German home'
  if (path === '/en') return 'English home'
  return 'Unknown route'
})
const middlewareInfo = computed(() => {
  const path = currentPath.value
  if (path.startsWith('/en/')) return 'English middleware'
  if (path.startsWith('/partners')) return 'German middleware'
  return 'Unknown middleware'
})
const pageProps = computed(() => ({
  locale: page.props.locale,
  hasTranslations: !!page.props.translations,
  hasAuth: !!page.props.auth
}))

const refreshPage = () => {
  router.reload()
}
</script>
