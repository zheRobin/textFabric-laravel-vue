<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import CreateCollectionForm from "Modules/Collections/resources/js/Pages/Partials/CreateCollectionForm.vue";
</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('Create Collection') }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <CreateCollectionForm />
            </div>
        </div>
    </AppLayout>
</template>
