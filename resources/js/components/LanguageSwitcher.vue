<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" size="sm"
        class="text-white/80 hover:text-white hover:bg-white/10 border border-white/20 backdrop-blur-sm rounded-2xl px-4 py-1.5 text-sm">
        <Icon name="globe-alt" class="h-4 w-4" />
        {{ currentLanguageLabel }}
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent
      class="w-40 p-2 rounded-lg bg-gray-600 border border-white/10 text-white backdrop-blur-3xl shadow-md">
      <DropdownMenuItem v-for="language in languages" :key="language.code" @click="switchLanguage(language.code)"
        class="flex items-center cursor-pointer text-white/80 hover:text-white hover:bg-white/40">
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
  // Send a POST request to switch language - the controller handles the redirect
  router.post('/language/switch', {
    locale: locale
  }, {
    preserveState: false,
    preserveScroll: false
  });
};
</script>