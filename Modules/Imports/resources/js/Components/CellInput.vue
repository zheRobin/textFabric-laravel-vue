<script setup>
import {computed, ref} from "vue";
import {useFocus} from '@vueuse/core';

const props = defineProps({
    modelValue: String,
    colsCount: Number,
    colNumber: Number,
    rowsCount: Number,
    rowNumber: Number,
    minHeight: Number,
});

defineEmits(['update:modelValue']);

const container = ref(null);

const cell = ref();
const { focused } = useFocus(cell);

const activeCellMinWidth = computed( () => {
    return container.value
        ? container.value.offsetWidth + 'px'
        : 'auto';
});

const activeCellMaxWidth = computed( () => {
    return container.value
        ? container.value.offsetWidth * (props.colsCount - props.colNumber) + (props.colsCount - props.colNumber) + 'px'
        : 'auto';
});

const activeCellMinHeight = computed( () => {
    return container.value
        ? container.value.offsetHeight + 'px'
        : 'auto';
});

const activeCellMaxHeight = computed( () => {
    return container.value
        ? container.value.offsetHeight * (props.rowsCount - props.rowNumber) + (props.rowsCount - props.rowNumber) + 'px'
        : 'auto';
});
</script>

<template>
    <div class="relative w-full h-full" ref="container">
        <div class="w-full h-full" :style="{'width': focused ? activeCellMaxWidth : activeCellMinWidth, 'position': focused ? 'absolute' : 'auto'}">
            <div class="overflow-scroll focus:absolute focus:z-20 focus:bg-white border-1 py-3 px-2 text-gray-900 ring-0 ring-gray-400 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-600 focus-visible:outline-none sm:text-sm sm:leading-6" aria-multiline="true" role="textbox"
                 :class="focused ? '' : 'truncate'"
                 ref="cell"
                 contenteditable="true"
                 :style="{'min-width': activeCellMinWidth, 'max-width': activeCellMaxWidth, 'min-height': activeCellMinHeight, 'max-height': activeCellMaxHeight}"
                 @input="$emit('update:modelValue', $event.target.innerText)"
            >
                {{ modelValue }}
            </div>
        </div>
    </div>
</template>
