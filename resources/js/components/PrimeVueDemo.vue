<script setup lang="ts">
import { ref } from 'vue'
import Button from 'primevue/button'

const message = ref('Hello PrimeVue!')

const showAlert = () => {
    alert('PrimeVue Button clicked!')
}
</script>

<template>
    <div class="p-8">
        <h1 class="text-3xl font-bold mb-6">{{ message }}</h1>
        
        <div class="space-y-4">
            <p class="text-lg">Testing PrimeVue components:</p>
            
            <div class="flex gap-4">
                <Button label="Click Me!" @click="showAlert" />
                <Button label="Secondary" severity="secondary" />
                <Button label="Success" severity="success" />
            </div>
        </div>
    </div>
</template>