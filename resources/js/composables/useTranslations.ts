import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function useTranslations() {
  const page = usePage()

  const translations = computed(() => page.props.translations as any)
  const locale = computed(() => page.props.locale as string)

  const __ = (key: string, group = 'common', replacements: Record<string, string> = {}) => {
    const keys = key.split('.')
    let value = translations.value[group]
    
    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k]
      } else {
        return key // Return key if translation not found
      }
    }
    
    let result = value || key
    
    // Handle replacements
    for (const [placeholder, replacement] of Object.entries(replacements)) {
      result = result.replace(`:${placeholder}`, replacement)
    }
    
    return result
  }

  const trans = (key: string, replacements: Record<string, string> = {}) => {
    // For keys like 'common.welcome' or 'business.find_local_partners'
    const parts = key.split('.')
    if (parts.length >= 2) {
      const group = parts[0]
      const translationKey = parts.slice(1).join('.')
      return __(translationKey, group, replacements)
    }
    
    return __(key, 'common', replacements)
  }

  return {
    __,
    trans,
    translations,
    locale
  }
}