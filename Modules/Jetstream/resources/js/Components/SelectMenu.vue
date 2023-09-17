<script setup>
import {computed} from "vue"
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
import {trans} from 'laravel-vue-i18n';

const props = defineProps({
    options: Array,
    modelValue: [String, Number, Array],
    label: String,
    placeholder: {
        type: String,
        default: trans('Select option'),
    },
    multiple: Boolean,
});

const emit = defineEmits(["update:modelValue"]);

const label = computed(() => {
    return props.options
        .filter(option => {
            if (Array.isArray(props.modelValue)) {
                return props.modelValue.includes(option.value);
            }

            return props.modelValue === option.value;
        })
        .map(option => option.label)
        .join(", ");
});
</script>

<template>
    <Listbox as="div"
             :model-value="props.modelValue"
             :multiple="props.multiple"
             @update:modelValue="value => emit('update:modelValue', value)">
        <ListboxLabel v-show="props.label" class="block text-sm font-medium leading-6 text-gray-900">Plan type</ListboxLabel>
        <div class="relative">
            <ListboxButton class="relative w-full cursor-default rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-tf-blue-500 sm:text-sm sm:leading-6">
                <span v-if="label" class="block truncate">{{ label }}</span>
                <span v-else class="text-gray-500"> {{ placeholder }} </span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </span>
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ListboxOption as="template" v-for="option in options" :key="option.value" :value="option.value" v-slot="{ active, selected }" :title="option.label">
                        <li :class="[active ? 'bg-tf-blue-500 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                            <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{ option.label }}</span>

                            <span v-if="selected" :class="[active ? 'text-white' : 'text-tf-blue-500', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
