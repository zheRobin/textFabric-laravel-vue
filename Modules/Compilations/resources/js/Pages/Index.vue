<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import DraggableList from '../Components/DarggableList.vue'
import ManageItems from "../../../../OpenAI/resources/js/Components/ManageItems.vue";
import SelectOrCreateCompolations from "../Components/SelectOrCreateCompolations.vue";
import {ref, watch} from 'vue'
const props = defineProps({
    presets: Array,
    previewItem: Object,
    complications: Array,
    languages: Array,
});

console.log(props.previewItem, "ID")

const loader = ref(true);


const dataRight = ref();
const activeCompilations = ref();
const itemRightData = (data) => {
    dataRight.value = data;
    console.log(data)
}

const onDelete = (data) => {
    console.log(data)
    loader.value = true;
}

const selectedPresetData = async (data) => {
    loader.value = true; // Встановлюємо loader у стан загрузки
    activeCompilations.value = await props.complications.find(item => item.id === data);
    loader.value = false; // Встановлюємо loader у стан завантаження завершено
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
                    <SelectOrCreateCompolations :complications="complications" @selectedPreset="selectedPresetData"  :positions="dataRight" @onDelete="onDelete"/>
                    <div class="text-center mt-5" v-if="loader || !activeCompilations">
                        <div class="dark:text-gray-400">Select or create a new compilation</div>
                    </div>
                    <div class="flex gap-10" v-else>
                        <DraggableList :presets="presets" :previewItem="previewItem" :compilation="activeCompilations" :languages="languages" @itemRight="itemRightData" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
