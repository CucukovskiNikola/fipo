import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function useCategories() {
  const page = usePage()
  const translations = computed(() => page.props.translations as any)
  const locale = computed(() => page.props.locale as string)

  const categories = computed(() => [
    {
      id: 'restaurant',
      name: translations.value?.categories?.restaurant || 'Restaurant',
      icon: '🍽️'
    },
    {
      id: 'hotel',
      name: translations.value?.categories?.hotel || 'Hotel',
      icon: '🏨'
    },
    {
      id: 'einkaufszentrum',
      name: translations.value?.categories?.einkaufszentrum || 'Einkaufszentrum',
      icon: '🛍️'
    },
    {
      id: 'unterhaltung',
      name: translations.value?.categories?.unterhaltung || 'Unterhaltung',
      icon: '🎬'
    },
    {
      id: 'gesundheitswesen',
      name: translations.value?.categories?.gesundheitswesen || 'Gesundheitswesen',
      icon: '🏥'
    },
    {
      id: 'bildung',
      name: translations.value?.categories?.bildung || 'Bildung',
      icon: '🏫'
    },
    {
      id: 'tankstelle',
      name: translations.value?.categories?.tankstelle || 'Tankstelle',
      icon: '⛽'
    },
    {
      id: 'bank',
      name: translations.value?.categories?.bank || 'Bank',
      icon: '🏦'
    },
    {
      id: 'park',
      name: translations.value?.categories?.park || 'Park',
      icon: '🌳'
    },
    {
      id: 'fitnessstudio',
      name: translations.value?.categories?.fitnessstudio || 'Fitnessstudio',
      icon: '💪'
    },
    {
      id: 'cafe',
      name: translations.value?.categories?.cafe || 'Café',
      icon: '☕'
    },
    {
      id: 'apotheke',
      name: translations.value?.categories?.apotheke || 'Apotheke',
      icon: '💊'
    },
    {
      id: 'supermarkt',
      name: translations.value?.categories?.supermarkt || 'Supermarkt',
      icon: '🛒'
    },
    {
      id: 'kirche',
      name: translations.value?.categories?.kirche || 'Kirche',
      icon: '⛪'
    },
    {
      id: 'bibliothek',
      name: translations.value?.categories?.bibliothek || 'Bibliothek',
      icon: '📚'
    },
    {
      id: 'buero',
      name: translations.value?.categories?.buero || 'Büro',
      icon: '🏢'
    },
    {
      id: 'andere',
      name: translations.value?.categories?.andere || 'Andere',
      icon: '📍'
    }
  ])

  const getCategoryName = (categoryId: string) => {
    const category = categories.value.find(cat => cat.id === categoryId)
    return category?.name || categoryId
  }

  return {
    categories,
    getCategoryName
  }
}
