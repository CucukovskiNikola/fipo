import { ref, computed, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useLibreTranslate } from '@/composables/useLibreTranslate'

interface Partner {
  id: number | string
  description?: string
  original_lang?: string
  [key: string]: any
}

interface UsePartnerTranslationOptions {
  partner: Partner
  enableTranslation?: boolean
  priority?: 'high' | 'normal' | 'low'
  contextPrefix?: string
}

export function usePartnerTranslation(options: UsePartnerTranslationOptions) {
  const { partner, enableTranslation = false, priority = 'normal', contextPrefix = 'partner' } = options
  
  const page = usePage()
  const { autoTranslateDebounced, translateTextDebounced, isTranslating, isProcessingQueue } = useLibreTranslate()
  
  // Translation state
  const translatedDescription = ref("")
  
  // Compute whether translation should be enabled based on locale or manual prop
  const shouldTranslate = computed(() => {
    const currentLocale = page.props.locale || "de"
    return enableTranslation || currentLocale === "en"
  })
  
  // Display description with translation fallback
  const displayDescription = computed(() => {
    return shouldTranslate.value && translatedDescription.value
      ? translatedDescription.value
      : partner.description || ""
  })
  
  // Translation loading state
  const isTranslatingDescription = computed(() => {
    return shouldTranslate.value && (isTranslating.value || isProcessingQueue.value)
  })
  
  // Function to perform translations with optimization
  const performTranslations = async () => {
    if (!shouldTranslate.value || !partner.description) {
      translatedDescription.value = ""
      return
    }

    try {
      // Create unique debounce key for this partner and context
      const debounceKey = `${contextPrefix}-${partner.id}-description`
      
      // Check if we have a cached translation to show immediately
      const cacheKey = `de-en-${partner.description}`
      const cachedTranslation = localStorage.getItem(`translation_cache`)
      
      if (cachedTranslation) {
        try {
          const cache = JSON.parse(cachedTranslation)
          if (cache[cacheKey] && cache[cacheKey].expiresAt > Date.now()) {
            // Show cached translation immediately to prevent layout shift
            translatedDescription.value = cache[cacheKey].text
          }
        } catch (e) {
          // Cache parsing failed, continue with API call
        }
      }
      
      // Use appropriate translation method based on context
      if (contextPrefix === 'show') {
        // For detailed view, use direct translation with high priority
        const result = await translateTextDebounced(
          partner.description,
          "de",
          "en",
          priority,
          debounceKey
        )
        // Only update if we got a different result (API returned new translation)
        if (result !== partner.description) {
          translatedDescription.value = result
        }
      } else {
        // For card view, use auto-translation detection
        const result = await autoTranslateDebounced(
          partner.description,
          priority,
          debounceKey
        )
        // Only update if we got a different result (API returned new translation)
        if (result !== partner.description) {
          translatedDescription.value = result
        }
      }
    } catch (error) {
      console.error("Translation failed:", error)
      // Keep existing translation if available, don't clear it
      if (!translatedDescription.value) {
        translatedDescription.value = ""
      }
    }
  }

  // Clear translations
  const clearTranslations = () => {
    translatedDescription.value = ""
  }

  // Initialize translations on mount
  onMounted(() => {
    performTranslations()
  })

  // Watch for changes to enableTranslation prop or locale
  watch(
    [() => enableTranslation, () => page.props.locale, () => partner.description],
    ([newTranslationValue, newLocale, newDescription]) => {
      console.log(
        "Translation state changed - enableTranslation:",
        newTranslationValue,
        "locale:",
        newLocale,
        "hasDescription:",
        !!newDescription
      )
      
      if (shouldTranslate.value && newDescription) {
        performTranslations()
      } else {
        clearTranslations()
      }
    }
  )

  return {
    // State
    translatedDescription,
    displayDescription,
    shouldTranslate,
    isTranslatingDescription,
    
    // Methods
    performTranslations,
    clearTranslations
  }
}