<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

// Components
import InputError from "@/components/InputError.vue";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Label } from "@/components/ui/label";

const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  password: "",
});

const deleteUser = (e: Event) => {
  e.preventDefault();

  form.delete(route("profile.destroy"), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  form.clearErrors();
  form.reset();
};
</script>

<template>
  <div class="space-y-6">
    <div
      class="space-y-4 rounded-lg border border-red-500/40 bg-red-600/10 p-4"
    >
      <div class="relative space-y-0.5 text-red-400">
        <p class="font-medium">⚠️ Gefahrenzone</p>
        <p class="text-sm">
          Gehen Sie bitte vorsichtig vor, dies kann nicht rückgängig gemacht
          werden.
        </p>
      </div>
      <Dialog>
        <DialogTrigger as-child>
          <button
            class="px-4 py-2 bg-red-600/80 border border-red-500/40 rounded-md text-white hover:bg-red-600 transition-colors"
          >
            Konto löschen
          </button>
        </DialogTrigger>
        <DialogContent
          class="liquid-glass text-white rounded-4xl backdrop-blur-lg border border-white/20"
        >
          <form class="space-y-6" @submit="deleteUser">
            <DialogHeader class="space-y-3">
              <DialogTitle class="text-white"
                >Möchten Sie Ihr Konto wirklich löschen?</DialogTitle
              >
              <DialogDescription class="text-gray-300">
                Sobald Ihr Konto gelöscht ist, werden auch alle zugehörigen
                Ressourcen und Daten dauerhaft gelöscht. Bitte geben Sie Ihr
                Passwort ein, um die endgültige Löschung Ihres Kontos zu
                bestätigen.
              </DialogDescription>
            </DialogHeader>

            <div class="grid gap-2">
              <Label for="password" class="sr-only">Password</Label>
              <input
                id="password"
                type="password"
                name="password"
                ref="passwordInput"
                v-model="form.password"
                placeholder="Geben Sie zur Bestätigung Ihr Passwort ein"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-400/50 focus:border-transparent"
              />
              <InputError
                :message="form.errors.password"
                class="text-red-400"
              />
            </div>

            <DialogFooter class="gap-2 flex justify-end space-x-3">
              <DialogClose as-child>
                <button
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 bg-white/10 border border-white/20 rounded-md text-white hover:bg-white/20 transition-colors"
                >
                  Stornieren
                </button>
              </DialogClose>

              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-red-600/80 border border-red-500/40 rounded-md text-white hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ form.processing ? "Löschen..." : "Konto löschen" }}
              </button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  </div>
</template>
