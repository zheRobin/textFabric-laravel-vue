<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import UpdateCollectionForm from "Modules/Collections/resources/js/Pages/Partials/UpdateCollectionForm.vue";
import SectionBorder from "Jetstream/Components/SectionBorder.vue";
import DeleteCollectionForm from "Modules/Collections/resources/js/Pages/Partials/DeleteCollectionForm.vue";

defineProps({
    collection: Object,
    permissions: Object,
});
</script>

<template>
    <AppLayout title="Collection Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Collection Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <UpdateCollectionForm :collection="collection" :permissions="permissions" />

                <SectionBorder />

                <DeleteCollectionForm :collection="collection" />
            </div>
        </div>
    </AppLayout>
</template>
