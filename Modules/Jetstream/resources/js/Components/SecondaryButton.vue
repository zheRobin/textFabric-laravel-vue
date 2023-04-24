<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex justify-center rounded-md bg-gray-200 py-2 px-3 uppercase tracking-widest text-xs font-semibold shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-600">
        <slot />
    </button>
</template>
