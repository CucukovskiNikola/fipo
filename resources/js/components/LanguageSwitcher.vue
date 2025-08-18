<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button 
        variant="ghost" 
        size="sm"
        class="text-gray-300 hover:text-white hover:bg-white/10"
      >
        <Icon name="globe-alt" class="h-4 w-4 mr-2" />
        {{ currentLanguageLabel }}
        <Icon name="chevron-down" class="h-4 w-4 ml-1" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-40 bg-black/90 border-white/20 backdrop-blur-sm">
      <DropdownMenuItem
        v-for="language in languages"
        :key="language.code"
        @click="switchLanguage(language.code)"
        class="flex items-center cursor-pointer text-white hover:bg-white/10 focus:bg-white/10"
        :class="{ 'bg-white/10': currentLocale === language.code }"
      >
        <span class="mr-2">{{ language.flag }}</span>
        {{ language.name }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import Icon from '@/components/Icon.vue';

interface Language {
  code: string;
  name: string;
  flag: string;
}

const page = usePage();

const languages: Language[] = [
  { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
  { code: 'en', name: 'English', flag: '🇬🇧' },
];

const currentLocale = computed(() => {
  return page.props.locale || 'de';
});

const currentLanguageLabel = computed(() => {
  const current = languages.find(lang => lang.code === currentLocale.value);
  return current ? current.name : 'Deutsch';
});

const switchLanguage = (locale: string) => {
  // Get current route and preserve all parameters
  const currentPath = window.location.pathname;
  const currentSearch = window.location.search;
  
  // Send a POST request to switch language
  router.post('/language', { 
    locale: locale,
    redirect_to: currentPath + currentSearch
  }, {
    preserveState: false,
    preserveScroll: true,
  });
};
</script>