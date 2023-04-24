<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean, String],
        default: false,
    },
    value: {
        type: [String, Number],
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>


<template>
    <input
        v-model="proxyChecked"
        type="checkbox"
        :value="value"
        class="h-4 w-4 rounded border-gray-300 text-tf-blue-500 focus:ring-tf-blue-500"
    >
</template>
