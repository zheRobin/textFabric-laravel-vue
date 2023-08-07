<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import CollectionDataTable from "Modules/Imports/resources/js/Components/CollectionDataTable.vue";
import UploadFileForm from "Modules/Imports/resources/js/Pages/Partials/UploadFileForm.vue";
import DashboardPanel from "Jetstream/Components/DashboardPanel.vue";
import EmptyCollection from "Modules/Collections/resources/js/Components/EmptyCollection.vue";

const props = defineProps({
    items: Object,
    permissions: Object,
});

</script>

<template>
    <AppLayout title="Import">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('Import') }}
            </h2>
        </template>

        <DashboardPanel>
            <EmptyCollection v-if="!$page.props.auth.user.current_collection" />

            <template v-else>
                <UploadFileForm v-if="permissions.canUpdateCollection" :hasItems="!!items.data.length" class="px-2 sm:px-0 mb-2" />

                <CollectionDataTable v-if="items.data.length"
                                     :canUpdateCollection="permissions.canUpdateCollection"
                                     :items="items"
                                     :headers="$page.props.auth.user.current_collection.headers" class="" />
            </template>
        </DashboardPanel>
    </AppLayout>
</template>
