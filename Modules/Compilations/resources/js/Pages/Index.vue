<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import DraggableList from '../Components/DarggableList.vue'
import ManageItems from "../../../../OpenAI/resources/js/Components/ManageItems.vue";


const props = defineProps({
    presets: Array,
    previewItem: Object,
});
</script>

<template>
    <AppLayout title="Compilations">
        <template #header>
            <div class="flex">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $t('complication') }}
                </h2>
                <ManageItems :presets="presets"/>
            </div>

        </template>
        <DraggableList :presets="presets" :previewItem="previewItem"/>
    </AppLayout>
</template>
