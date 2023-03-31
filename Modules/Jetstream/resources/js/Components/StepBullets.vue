<script setup>

defineProps({
    currentStep: {
        type: Number,
        default: 0
    },
    totalSteps: {
        type: Number,
        default: 0
    },
})
</script>

<template>
    <nav class="flex items-center justify-center" aria-label="Progress">
        <p class="text-sm font-medium">Step {{ currentStep }} of {{ totalSteps }}</p>
        <ol role="list" class="ml-8 flex items-center space-x-5">
            <li v-for="step in totalSteps" :key="step">
                <a v-if="currentStep > step" class="block h-2.5 w-2.5 rounded-full bg-tf-blue-500">
                    <span class="sr-only"></span>
                </a>
                <a v-else-if="step === currentStep" class="relative flex items-center justify-center" aria-current="step">
                    <span class="absolute flex h-5 w-5 p-px" aria-hidden="true">
                        <span class="h-full w-full rounded-full bg-tf-blue-50" />
                    </span>
                    <span class="relative block h-2.5 w-2.5 rounded-full bg-tf-blue-500" aria-hidden="true" />
                    <span class="sr-only"></span>
                </a>
                <a v-else class="block h-2.5 w-2.5 rounded-full bg-gray-200">
                    <span class="sr-only"></span>
                </a>
            </li>
        </ol>
    </nav>
</template>
