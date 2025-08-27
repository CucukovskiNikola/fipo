<template>
  <button
    @click="toggle"
    :disabled="disabled"
    :class="[
      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900',
      {
        'bg-gradient-to-r from-blue-500 to-purple-500': modelValue && !disabled,
        'bg-gray-600': !modelValue && !disabled,
        'bg-gray-700 cursor-not-allowed opacity-50': disabled
      }
    ]"
  >
    <span
      :class="[
        'inline-block h-4 w-4 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out',
        {
          'translate-x-6': modelValue,
          'translate-x-1': !modelValue
        }
      ]"
    />
    <span class="sr-only">Toggle setting</span>
  </button>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const toggle = () => {
  if (!props.disabled) {
    emit('update:modelValue', !props.modelValue)
  }
}
</script>