<script setup>
import {computed, ref} from "vue";
import GuestLayout from "Jetstream/Layouts/GuestLayout.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import PromptEditor from "Modules/OpenAI/resources/js/Components/PromptEditor.vue";
import ItemCompletionPreview from "Modules/OpenAI/resources/js/Components/Demo/ItemCompletionPreview.vue";
import DashboardPanel from "Jetstream/Components/DashboardPanel.vue";
import {getPresets} from "Modules/OpenAI/resources/js/presets";
import {getActiveLanguage} from "laravel-vue-i18n";

const presets = getPresets(getActiveLanguage());

const selectedPreset = ref(null);

const selectedPresetOption = computed(() => {
    return selectedPreset.value
        ? selectedPreset.value.slug
        : null;
})

const showMainPanel = computed(() => !!selectedPreset.value);

const presetOptions = () => {
    const presetsOptions = [];

    presets.forEach((el) => {
        presetsOptions.push({value: el.slug,label: el.name});
    })

    return presetsOptions;
}

const changePreset = (value) => {
    selectedPreset.value = presets.find((el) => el.slug === value);
}
</script>

<template>
    <GuestLayout title="Editor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('Editor') }}
            </h2>
        </template>

        <DashboardPanel>
            <div class="flex border-b border-gray-200 pb-8 items-center">
                <div class="items-center flex flex-1">
                    <label class="mr-2 font-medium">Preset:</label>
                    <SelectMenu @update:modelValue="changePreset" v-model="selectedPresetOption" :options="presetOptions()" class="w-60" placeholder="Select" />
                </div>
            </div>

            <div v-if="!showMainPanel" class="text-center mt-5 text-gray-700">
            <span>
                Select or create a new preset...
            </span>
            </div>

            <template v-if="showMainPanel">
                <!-- Prompt fields -->
                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 pt-8">
                    <div class="mt-6 lg:mt-0 bg-gray-50 rounded p-4">
                        <PromptEditor :canEdit="false" title="System" v-model="selectedPreset.system_prompt" />
                    </div>

                    <div class="mt-6 lg:mt-0 bg-gray-50 rounded p-4">
                        <PromptEditor :canEdit="false" title="User" v-model="selectedPreset.user_prompt" />
                    </div>
                </div>

                <ItemCompletionPreview :preset="selectedPreset"/>
            </template>
        </DashboardPanel>
    </GuestLayout>
</template>
