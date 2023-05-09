<script setup xmlns="http://www.w3.org/1999/html">
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: String,
})

defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})

defineExpose({ focus: () => input.value.focus() })
</script>

<template>
    <textarea
        ref="input"
        class="outline-none border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        @input="$emit('update:modelValue', $event.target.value)"
    >{{ modelValue }}</textarea>
</template>
