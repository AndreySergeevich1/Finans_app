<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'text',
    },
    modelValue: {
        type: [String, Number, null],
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :type="type"
        :value="modelValue ?? ''"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
        v-bind="$attrs"
    />
</template>
