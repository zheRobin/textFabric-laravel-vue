<script setup>
import GuestLayout from 'Jetstream/Layouts/GuestLayout.vue';
import DashboardPanel from "Jetstream/Components/DashboardPanel.vue";
import CollectionDataTable from "Modules/Imports/resources/js/Components/Demo/CollectionDataTable.vue";
import {defaultHeaders, getItems} from "Modules/Imports/resources/js/collection";
import {ref} from "vue";
import {getActiveLanguage} from "laravel-vue-i18n";

const headers = ref([]);

const setupHeaders = () => {
    headers.value = defaultHeaders[getActiveLanguage()];
}
setupHeaders();

const items = ref([]);

const setupItems = () => {
    items.value = getItems(getActiveLanguage());
}
setupItems();

</script>

<template>
    <GuestLayout title="Import">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('Import') }}
            </h2>
        </template>

        <DashboardPanel>
            <CollectionDataTable v-if="items.length"
                                 :items="items"
                                 :headers="headers"
            />
        </DashboardPanel>
    </GuestLayout>
</template>
