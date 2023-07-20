<script setup>
import {XMarkIcon} from "@heroicons/vue/20/solid";
import Dropdown from "Jetstream/Components/Dropdown.vue";
import DropdownLink from "Jetstream/Components/DropdownLink.vue";

const props = defineProps({
    modelValue: Object,
    languages: Array,
    disabled: Boolean,
});

const emit = defineEmits(["update:modelValue"]);

const changeLanguage = (language) => {
    emit('update:modelValue', language);
}

</script>

<template>
    <span v-if="disabled" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500">
        {{ props.modelValue ? props.modelValue.name : $t('Language') }}
    </span>

    <Dropdown v-else align="left" width="30" height="48">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button type="button" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    {{ props.modelValue ? props.modelValue.name : $t('Language') }}
                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </button>
            </span>
        </template>
        <template #content>
            <div class="w-30 relative">
                <div class="sticky top-0 bg-white">
                    <button @click="changeLanguage(null)" class="block flex justify-between w-full px-4 py-2 text-left text-sm font-semibold border-b leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out">
                        {{ $t('Clear') }}
                        <XMarkIcon class="inline-flex w-5" />
                    </button>
                </div>
                <DropdownLink @click="changeLanguage(language)" v-for="language in props.languages" as="button">
                    {{ language.name }}
                </DropdownLink>
            </div>
        </template>
    </Dropdown>
</template>
