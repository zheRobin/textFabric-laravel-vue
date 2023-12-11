<script setup>
import {ref, watch} from "vue";
import {useFocus} from '@vueuse/core';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    editable: Boolean,
});
const emit = defineEmits(['update:modelValue']);

const cell = ref();
const { focused } = useFocus(cell);

watch(focused, (focused) => {
    if (!focused) {
        const content = props.modelValue === null
            ? ''
            : props.modelValue;

        if (content !== cell.value.innerText) {
            emit('update:modelValue', cell.value.innerText);
        }
    }
})
</script>

<template>
    <div role="textbox" ref="cell" :contenteditable="editable" v-text="modelValue"/>
</template>
