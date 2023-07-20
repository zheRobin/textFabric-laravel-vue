<script setup>
import {ref} from "vue";
import { Link } from '@inertiajs/vue3';
import { DocumentPlusIcon } from '@heroicons/vue/20/solid';
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import EmptyState from "Jetstream/Components/EmptyState.vue";
import CollectionDataTable from "Modules/Imports/resources/js/Components/CollectionDataTable.vue";
import UploadFileForm from "Modules/Imports/resources/js/Pages/Partials/UploadFileForm.vue";
import DashboardPanel from "Jetstream/Components/DashboardPanel.vue";

const props = defineProps({
    items: Object,
    permissions: Object,
});

const hasItems = ref(!!props.items.data.length);

</script>

<template>
    <AppLayout title="Import">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('Import') }}
            </h2>
        </template>

        <DashboardPanel>
            <EmptyState v-if="!$page.props.auth.user.current_collection" class="mt-16"
                        title="No collection selected"
                        description="Get started by selecting or creating a collection." >
                <template #icon>
                    <DocumentPlusIcon />
                </template>
                <template #action>
                    <Link class="inline-flex justify-center rounded-md bg-tf-blue-500 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-tf-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-600"
                          :href="route('collections.create')">
                        {{ $t("New Collection") }}
                    </Link>
                </template>
            </EmptyState>

            <template v-else>
                <UploadFileForm v-if="permissions.canUpdateCollection" :hasItems="hasItems" class="px-2 sm:px-0 mb-2" />

                <CollectionDataTable v-if="hasItems"
                                     :canUpdateCollection="permissions.canUpdateCollection"
                                     :items="items"
                                     :headers="$page.props.auth.user.current_collection.headers" class="" />
            </template>
        </DashboardPanel>
    </AppLayout>
</template>
