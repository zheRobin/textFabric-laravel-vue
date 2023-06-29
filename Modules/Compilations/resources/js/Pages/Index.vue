<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import DraggableList from '../Components/DarggableList.vue'


const props = defineProps({
    presets: Array,
    previewItem: Object,
});
</script>

<template>
    <AppLayout title="Compilations">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('complication') }}
            </h2>
        </template>
        <DraggableList :presets="presets" :previewItem="previewItem"/>
    </AppLayout>
</template>
