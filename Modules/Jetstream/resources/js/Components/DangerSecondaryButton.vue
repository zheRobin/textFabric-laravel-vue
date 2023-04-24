<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center justify-center px-4 py-1.5 bg-white rounded-md font-semibold text-xs text-red-600 border-2 border-red-600 uppercase tracking-widest hover:bg-red-50 active:bg-red-50 active:border-red-700 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
