<script setup>
import {reactive} from "vue";

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    options: Array,
    modelValue: String,
});

const state = reactive({
    active: false,

});

const toggleDropdown = () => {
    state.active = !state.active;
}

const selectOption = (option) => {
    emit('update:modelValue', option);
    toggleDropdown();
}
</script>

<template>
        <button type="button" @click="toggleDropdown" class="mt-1 justify-between inline-flex w-full items-center px-4 py-2 shadow-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 transition ease-in-out duration-150">
            <span class="text-gray-400" v-if="!modelValue">Select an item</span>
            <span v-else> {{modelValue}} </span>
            <svg v-if="!state.active" id="arrow-down" class="w-6 h-6 fill-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            <svg v-if="state.active" id="arrow-up" class="w-6 h-6 fill-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
        </button>
        <div id="options" v-if="state.active" class="py-2 mt-2 bg-white rounded-lg shadow-xl">
            <button type="button" @click="selectOption(option)" v-for="option in props.options" :value="option" class="text-left w-full block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white">
                {{ option }}
            </button>
        </div>
</template>
