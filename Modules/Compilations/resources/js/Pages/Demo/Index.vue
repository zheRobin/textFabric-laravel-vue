<script setup>
import GuestLayout from 'Jetstream/Layouts/GuestLayout.vue';
import DraggableList from "Modules/Compilations/resources/js/Components/Demo/DraggableList.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import DashboardPanel from "Jetstream/Components/DashboardPanel.vue";
import {ref} from 'vue'
import {getPresets} from "Modules/OpenAI/resources/js/presets";
import {getActiveLanguage} from "laravel-vue-i18n";

const presets = getPresets(getActiveLanguage());


const compilationOptions = () => {
    return [
        {
            value: 'website-content',
            label: 'Website Content'
        }
    ];
}
const selectedCompilation = ref(compilationOptions()[0].value);
</script>

<template>
    <GuestLayout title="Compilations">
        <template #header>
            <div class="flex">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $t('Compilations') }}
                </h2>
            </div>
        </template>

        <DashboardPanel>
            <div class="flex border-b border-gray-200 pb-8 items-center">
                <div class="items-center flex flex-1">
                    <label class="mr-2 font-medium dark:text-white">{{$t('Compilations')}}:</label>
                    <SelectMenu class="w-60" v-model="selectedCompilation" :options="compilationOptions()" :placeholder="$t('Select')" />
                </div>
            </div>

            <div v-if="!selectedCompilation" class="text-center mt-5 text-gray-700">
                <span>
                    {{ $t('Select or create a new compilation...') }}
                </span>
            </div>

            <div class="flex gap-10" v-if="selectedCompilation">
                <DraggableList :canEdit="true" :presets="presets" :demo="true" />
            </div>
        </DashboardPanel>
    </GuestLayout>
</template>
