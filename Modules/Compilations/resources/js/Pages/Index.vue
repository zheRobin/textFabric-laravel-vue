<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import SelectOrCreateCompolations from "../Components/SelectOrCreateCompolations.vue";
import EmptyCollection from "Modules/Collections/resources/js/Components/EmptyCollection.vue";
import EmptyImport from "Modules/Imports/resources/js/Components/EmptyImport.vue";
import {ref} from 'vue'
import DarggableList from "Modules/Compilations/resources/js/Components/DarggableList.vue";

const props = defineProps({
    presets: Array,
    previewItem: Object,
    previewItemLength: Number,
    complications: Array,
    languages: Array,
    hasItems: Boolean,
    permissions: Object,
    title: Object
});

const loader = ref(true);
const dataRight = ref(null);
const activeCompilations = ref(null);
const itemRightData = (data) => {
    dataRight.value = data;
}

const onDelete = (data) => {
    loader.value = true;
    dataRight.value = null;
}

const selectedPresetData = async (data) => {
    loader.value = true;
    activeCompilations.value = await props.complications.find(item => item.id === data);
    loader.value = false;
}
</script>

<template>
    <AppLayout title="Compilations">
        <template #header>
            <div class="flex">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $t('Compilations') }}
                </h2>
            </div>
        </template>
        <div class="max-w-7xl mx-auto my-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                <div class="mx-auto px-6 py-6">
                    <EmptyCollection v-if="!$page.props.auth.user.current_collection" />

                    <EmptyImport v-else-if="!hasItems" />

                    <template v-else>
                        <SelectOrCreateCompolations :canManageCompilations="true" :complications="complications" @selectedPreset="selectedPresetData"  :positions="dataRight" @onDelete="onDelete"/>
                        <div class="text-center mt-5" v-if="loader || !activeCompilations">
                            <div class="dark:text-gray-400">{{$t('Select or create a new compilation...')}}</div>
                        </div>
                        <div class="flex gap-10" v-else>
                            <DarggableList :canEdit="true" :presets="presets" :previewItem="previewItem" :previewItemLength="previewItemLength" :compilation="activeCompilations" :languages="languages" @itemRight="itemRightData" :title="title" />
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
