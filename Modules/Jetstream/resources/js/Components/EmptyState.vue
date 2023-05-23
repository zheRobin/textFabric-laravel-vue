<script setup>

defineProps({
    title: String,
    description: String,
})
</script>

<template>
    <div class="text-center">
        <div class="mx-auto h-12 w-12 text-tf-blue-50">
            <slot name="icon" />
        </div>
        <h3 class="mt-2 text-sm font-semibold text-gray-900">{{ title }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ description }}</p>
        <div class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>
