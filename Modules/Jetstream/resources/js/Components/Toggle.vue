<script setup>
import {Switch, SwitchGroup, SwitchLabel} from "@headlessui/vue";

const props = defineProps({
    modelValue: Boolean,
    disabled: Boolean,
});

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <SwitchGroup as="div" class="flex items-center">
        <Switch :disabled="disabled" :model-value="props.modelValue" @update:modelValue="value => emit('update:modelValue', value)" :class="[props.modelValue ? 'bg-tf-blue-500' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-tf-blue-500 focus:ring-offset-2']">
            <span aria-hidden="true" :class="[props.modelValue ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
        </Switch>
        <SwitchLabel as="span" class="ml-3 text-sm">
            <slot name="label" />
        </SwitchLabel>
    </SwitchGroup>
</template>
