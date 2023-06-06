<script setup>
import { Head } from '@inertiajs/vue3';
import ApplicationLogo from 'Jetstream/Components/ApplicationLogo.vue';

defineProps({
    title: String,
});

</script>

<template>
    <div class="flex min-h-full">
        <Head :title="title" />

        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <ApplicationLogo class="block h-12 w-auto" />
                    <slot name="header" />
                </div>

                <div class="mt-8">
                    <div class="mt-6">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
        <div class="relative hidden w-0 flex-1 lg:block">
            <slot name="second-screen" />
        </div>
    </div>
</template>
