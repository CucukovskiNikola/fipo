<script setup lang="ts">
import { Head, Link, useForm, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onUnmounted } from "vue";

import DeleteUser from "@/components/DeleteUser.vue";
import Icon from "@/components/Icon.vue";
import DashboardNavbar from "@/components/DashboardNavbar.vue";
import InputError from "@/components/InputError.vue";
import { type User } from "@/types";

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
  meta?: {
    title: string;
    description: string;
    keywords?: string;
  };
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth.user as User;

// Navigation configuration
const navigationLinks = [
  {
    label: "Partners",
    href: route("partners.index"),
  },
  {
    label: "Dashboard",
    href: route("dashboard"),
  },
  {
    label: "Home",
    href: "/",
  },
];

const form = useForm({
  name: user.name,
  email: user.email,
});

const passwordForm = useForm({
  current_password: "",
  password: "",
  password_confirmation: "",
});

const activeTab = ref("profile");

const submit = () => {
  form.patch(route("profile.update"), {
    preserveScroll: true,
  });
};

const submitPassword = () => {
  passwordForm.put(route("password.update"), {
    preserveScroll: true,
    onSuccess: () => passwordForm.reset(),
  });
};

const logout = () => {
  router.post(route("logout"));
};
</script>

<template>
  <Head>
    <title>{{ meta?.title || "findemich - Profileinstellungen" }}</title>
    <meta
      name="description"
      :content="
        meta?.description ||
        'Verwalten Sie Ihr findemich-Profil, Kontoeinstellungen und Präferenzen. Aktualisieren Sie persönliche Informationen, Sicherheitseinstellungen und Anzeigeoptionen.'
      "
    />
  </Head>

  <div class="min-h-screen text-white p-2 bg-black bg-image">
    <!-- Navigation -->
    <DashboardNavbar
      title="Profileinstellungen"
      title-icon="user"
      :home-route="route('dashboard')"
      :navigation-links="navigationLinks"
    />

    <div class="space-y-6 max-w-6xl mx-auto">
      <!-- User Info Card -->
      <div class="liquid-glass text-white rounded-4xl p-8 mt-4 shadow-lg">
        <div class="flex items-center space-x-6">
          <div
            class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center"
          >
            <Icon name="user" class="h-10 w-10 text-white" />
          </div>
          <div>
            <h1 class="text-2xl font-semibold text-white mb-1">
              {{ user.name }}
            </h1>
            <p class="text-gray-300">{{ user.email }}</p>
            <div class="flex items-center mt-2">
              <div
                v-if="user.email_verified_at"
                class="flex items-center text-green-400 text-sm"
              >
                <Icon name="check-circle" class="h-4 w-4 mr-1" />
                E-Mail verifiziert
              </div>
              <div v-else class="flex items-center text-yellow-400 text-sm">
                <Icon name="exclamation-triangle" class="h-4 w-4 mr-1" />
                E-Mail nicht verifiziert
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Settings Tabs -->
      <div class="liquid-glass text-white rounded-4xl p-8 shadow-lg">
        <div class="border-b border-white/20 mb-6">
          <nav class="flex space-x-8">
            <button
              @click="activeTab = 'profile'"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                activeTab === 'profile'
                  ? 'border-white text-white'
                  : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300',
              ]"
            >
              Profilinformationen
            </button>
            <button
              @click="activeTab = 'password'"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                activeTab === 'password'
                  ? 'border-white text-white'
                  : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300',
              ]"
            >
              Passwort ändern
            </button>
            <button
              @click="activeTab = 'account'"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                activeTab === 'account'
                  ? 'border-white text-white'
                  : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300',
              ]"
            >
              Kontoeinstellungen
            </button>
          </nav>
        </div>

        <!-- Profile Information Tab -->
        <div v-if="activeTab === 'profile'" class="space-y-6">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label
                for="name"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Vollständiger Name <span class="text-red-400">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                placeholder="Geben Sie Ihren vollständigen Namen ein"
              />
              <InputError
                :message="form.errors.name"
                class="mt-1 text-red-400"
              />
            </div>

            <div>
              <label
                for="email"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                E-Mail-Adresse <span class="text-red-400">*</span>
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                placeholder="Geben Sie Ihre E-Mail-Adresse ein"
              />
              <InputError
                :message="form.errors.email"
                class="mt-1 text-red-400"
              />
            </div>

            <div
              v-if="mustVerifyEmail && !user.email_verified_at"
              class="p-4 bg-yellow-500/20 border border-yellow-500/40 rounded-md"
            >
              <p class="text-sm text-yellow-200">
                Ihre E-Mail-Adresse ist nicht verifiziert.
                <Link
                  :href="route('verification.send')"
                  method="post"
                  as="button"
                  class="text-yellow-100 underline hover:text-white transition-colors"
                >
                  Klicken Sie hier, um die Bestätigungs-E-Mail erneut zu senden.
                </Link>
              </p>

              <div
                v-if="status === 'verification-link-sent'"
                class="mt-2 text-sm font-medium text-green-400"
              >
                Ein neuer Bestätigungslink wurde an Ihre E-Mail-Adresse
                gesendet.
              </div>
            </div>

            <div class="flex items-center space-x-4">
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-white/10 border border-white/20 rounded-md text-white hover:bg-white/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ form.processing ? "Speichern..." : "Änderungen speichern" }}
              </button>

              <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
              >
                <p
                  v-show="form.recentlySuccessful"
                  class="text-sm text-green-400"
                >
                  Erfolgreich gespeichert!
                </p>
              </Transition>
            </div>
          </form>
        </div>

        <!-- Change Password Tab -->
        <div v-if="activeTab === 'password'" class="space-y-6">
          <form @submit.prevent="submitPassword" class="space-y-6">
            <div>
              <label
                for="current_password"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Aktuelles Passwort <span class="text-red-400">*</span>
              </label>
              <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                required
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                placeholder="Aktuelles Passwort eingeben"
              />
              <InputError
                :message="passwordForm.errors.current_password"
                class="mt-1 text-red-400"
              />
            </div>

            <div>
              <label
                for="password"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Neues Passwort <span class="text-red-400">*</span>
              </label>
              <input
                id="password"
                v-model="passwordForm.password"
                type="password"
                required
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                placeholder="Neues Passwort eingeben"
              />
              <InputError
                :message="passwordForm.errors.password"
                class="mt-1 text-red-400"
              />
            </div>

            <div>
              <label
                for="password_confirmation"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Passwort bestätigen <span class="text-red-400">*</span>
              </label>
              <input
                id="password_confirmation"
                v-model="passwordForm.password_confirmation"
                type="password"
                required
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                placeholder="Passwort bestätigen"
              />
              <InputError
                :message="passwordForm.errors.password_confirmation"
                class="mt-1 text-red-400"
              />
            </div>

            <div class="flex items-center space-x-4">
              <button
                type="submit"
                :disabled="passwordForm.processing"
                class="px-4 py-2 bg-white/10 border border-white/20 rounded-md text-white hover:bg-white/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{
                  passwordForm.processing
                    ? "Aktualisieren..."
                    : "Passwort aktualisieren"
                }}
              </button>

              <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
              >
                <p
                  v-show="passwordForm.recentlySuccessful"
                  class="text-sm text-green-400"
                >
                  Passwort erfolgreich aktualisiert!
                </p>
              </Transition>
            </div>
          </form>
        </div>

        <!-- Account Settings Tab -->
        <div v-if="activeTab === 'account'" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Account Actions -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-white">Schnellaktionen</h3>

              <div class="space-y-3">
                <Link
                  :href="route('dashboard')"
                  class="flex items-center p-3 bg-white/10 rounded-md hover:bg-white/20 transition-colors"
                >
                  <Icon name="home" class="h-5 w-5 text-white mr-3" />
                  <span class="text-white">Zum Dashboard</span>
                </Link>

                <Link
                  :href="route('partners.index')"
                  class="flex items-center p-3 bg-white/10 rounded-md hover:bg-white/20 transition-colors"
                >
                  <Icon
                    name="building-office"
                    class="h-5 w-5 text-white mr-3"
                  />
                  <span class="text-white">Partner verwalten</span>
                </Link>

                <button
                  @click="logout"
                  class="flex items-center w-full p-3 bg-red-600/20 border border-red-500/40 rounded-md hover:bg-red-600/30 transition-colors"
                >
                  <Icon
                    name="arrow-right-on-rectangle"
                    class="h-5 w-5 text-red-400 mr-3"
                  />
                  <span class="text-red-400">Abmelden</span>
                </button>
              </div>
            </div>

            <!-- Account Info -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-white">
                Kontoinformationen
              </h3>

              <div class="space-y-3 text-sm">
                <div class="flex justify-between p-3 bg-white/10 rounded-md">
                  <span class="text-gray-300">Mitglied seit</span>
                  <span class="text-white">{{
                    new Date(user.created_at).toLocaleDateString()
                  }}</span>
                </div>

                <div class="flex justify-between p-3 bg-white/10 rounded-md">
                  <span class="text-gray-300">Zuletzt aktualisiert</span>
                  <span class="text-white">{{
                    new Date(user.updated_at).toLocaleDateString()
                  }}</span>
                </div>

                <div class="flex justify-between p-3 bg-white/10 rounded-md">
                  <span class="text-gray-300">Konto-ID</span>
                  <span class="text-white font-mono">#{{ user.id }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Danger Zone -->
          <div class="border-t border-red-500/20 pt-6">
            <h3 class="text-lg font-semibold text-red-400 mb-4">
              Gefahrenbereich
            </h3>
            <DeleteUser />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
