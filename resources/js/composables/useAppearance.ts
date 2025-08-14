import { ref } from 'vue';

type Appearance = 'light';

export function updateTheme(value: Appearance) {
    if (typeof window === 'undefined') {
        return;
    }

    document.documentElement.classList.remove('dark');
}

export function initializeTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    document.documentElement.classList.remove('dark');
}

const appearance = ref<Appearance>('light');

export function useAppearance() {
    return {
        appearance,
    };
}
