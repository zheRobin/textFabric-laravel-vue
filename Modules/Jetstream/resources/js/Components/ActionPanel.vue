<script setup>

defineProps({
    title: String,
})
</script>

<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="text-base font-semibold leading-6 text-gray-900">
                <slot name="header" />
            </div>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <slot name="body" />
            </div>
            <div class="mt-5">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
