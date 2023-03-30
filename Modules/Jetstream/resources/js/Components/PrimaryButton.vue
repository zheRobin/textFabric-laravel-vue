<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex uppercase tracking-widest justify-center rounded-md bg-tf-blue-500 py-2 px-3 text-xs font-semibold text-white shadow-sm hover:bg-tf-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-600">
        <slot />
    </button>
</template>
