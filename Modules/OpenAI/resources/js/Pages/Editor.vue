<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import {XCircleIcon, MinusCircleIcon, PencilSquareIcon, PlusCircleIcon, ArrowDownTrayIcon, PlusIcon} from "@heroicons/vue/20/solid";
import {ref} from "vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import RangeSlider from "Jetstream/Components/RangeSlider.vue";
import PromptEditor from "Modules/OpenAI/resources/js/Components/PromptEditor.vue";
import {notify} from "notiwind";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";
import DangerButton from "Jetstream/Components/DangerButton.vue";
import RenamePreset from "Modules/OpenAI/resources/js/Components/RenamePreset.vue";
import DeletePreset from "Modules/OpenAI/resources/js/Components/DeletePreset.vue";
import ItemCompletionPreview from "Modules/OpenAI/resources/js/Components/ItemCompletionPreview.vue";

const props = defineProps({
    selectedPreset: Object,
    presets: Array,
    previewItem: Object,
    models: Array,
    languages: Array,
});

const presetObj = ref(null);
const selectedPreset = ref(null);

const form = useForm({
    collection_id: usePage().props.auth.user.current_collection.id,
    model: 'gpt-3.5-turbo',
    system_prompt: '',
    user_prompt: '',
    name: null,
    temperature: 1,
    top_p: 1,
    presence_penalty: 0,
    frequency_penalty: 0,
    input_language_id: null,
    output_language_id: null,
});

const modelOptions = () => {
    const models = [];

    props.models.forEach((el) => {
        models.push({value: el,label: el});
    })

    return models;
}

const availableAttributes = usePage().props.auth.user.current_collection.headers;

const addingPreset = ref(!props.presets.length);

const presetOptions = () => {
    const presets = [];

    props.presets.forEach((el) => {
        presets.push({value: el.id,label: el.name});
    })

    return presets;
}

const languageOptions = () => {
    const languages = [];

    props.languages.forEach((el) => {
        languages.push({value: el.id,label: el.name});
    })

    return languages;
}

const changePreset = (value) => {
    selectedPreset.value = value;
    presetObj.value = getPreset(value);
    addingPreset.value = false;
    const preset = getPreset(value);

    if (preset) {
        fillPresetForm(preset);
    }
}

const getPreset = (presetId) => {
    if (!presetId) {
        return null;
    }

    return props.presets.find((el) => el.id === presetId);
}

const fillPresetForm = (preset) => {
    Object.getOwnPropertyNames(preset).forEach((property) => {
        if (form.hasOwnProperty(property)) {
            form[property] = preset[property];
        }
    });
}

const addPreset = () => {
    addingPreset.value = true;
    presetObj.value = null;
    form.defaults({model: 'gpt-3.5-turbo', system_prompt: '', user_prompt: '', name: null, temperature: 1, top_p: 1, presence_penalty: 0, frequency_penalty: 0, input_language_id: null, output_language_id: null});
    form.reset();
}

const cancelPreset = () => {
    addingPreset.value = false;
    changePreset(selectedPreset.value);
}

const savePreset = () => {
    if (addingPreset.value) {
        createPreset();
    } else if (selectedPreset.value) {
        updatePreset();
    }
}

const createPreset = () => {
    form.post(route('presets.store'), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: () => {
            changePreset(props.selectedPreset.id);
            notify({
                group: "success",
                title: "Success",
                text: "Preset created!"
            }, 4000)
        },
        onError: (error) => {
            notify({
                group: "error",
                title: "Error",
                text: error[Object.keys(error)[0]] ?? "Something wrong happens."
            }, 4000)
        }
    })
}

const updatePreset = () => {
    form.patch(route('presets.update', selectedPreset.value), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: () => {
            notify({
                group: "success",
                title: "Success",
                text: "Preset updated!"
            }, 4000)
        },
        onError: (error) => {
            notify({
                group: "error",
                title: "Error",
                text: error[Object.keys(error)[0]] ?? "Something wrong happens."
            }, 4000) // 4s
        }
    })
}

const renamePreset = (value) => {
    form.name = value;
    updatePreset();
}

const deletePreset = () => {
    form.delete(route('presets.destroy', selectedPreset.value), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: () => {
            notify({
                group: "success",
                title: "Success",
                text: "Preset deleted!"
            }, 4000)
        },
    })
}

const changeInputLanguage = (language) => {
    form.input_language_id = language.id;
}

const changeOutputLanguage = (language) => {
    form.output_language_id = language.id;
}
</script>

<template>
    <AppLayout title="Editor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('Editor') }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="mx-auto px-6 py-6">
                        <div class="flex border-b border-gray-200 pb-8 items-center">
                            <div class="items-center flex flex-1">
                                <template v-if="!presets.length || addingPreset">
                                    <label class="mr-2 font-medium">Name:</label>
                                    <TextInput v-model="form.name" type="text" class="w-36"/>
                                    <PrimaryButton @click="savePreset" class="ml-2 gap-x-1.5">
                                        {{$t('Save')}}
                                        <ArrowDownTrayIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                    </PrimaryButton>
                                    <SecondaryButton v-if="presets.length" class="ml-2 gap-x-1.5" @click="cancelPreset">
                                        {{$t('Cancel')}}
                                        <XCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                    </SecondaryButton>
                                </template>

                                <template v-else>
                                    <label class="mr-2 font-medium">Preset:</label>
                                    <SelectMenu @update:modelValue="changePreset" v-model="selectedPreset" :options="presetOptions()" class="w-72" placeholder="Select" />
                                    <PrimaryButton @click="addPreset" class="ml-2 gap-x-1.5">
                                        {{$t('Add')}}
                                        <PlusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                    </PrimaryButton>
                                    <PrimaryButton v-if="selectedPreset" @click="savePreset" class="ml-2 gap-x-1.5">
                                        {{$t('save')}}
                                        <ArrowDownTrayIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                    </PrimaryButton>
                                    <RenamePreset v-if="selectedPreset" :name="form.name" @rename="renamePreset">
                                        <PrimaryButton class="ml-2 gap-x-1.5">
                                            {{$t('Rename')}}
                                            <PencilSquareIcon  class="-mr-0.5 w-4" aria-hidden="true" />
                                        </PrimaryButton>
                                    </RenamePreset>
                                    <DeletePreset @delete="deletePreset" v-if="selectedPreset" :name="form.name">
                                        <DangerButton class="ml-2 gap-x-1.5">
                                            {{$t('Delete')}}
                                            <MinusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                        </DangerButton>
                                    </DeletePreset>
                                </template>
                            </div>
                        </div>

                        <!-- Filters -->
                        <section aria-labelledby="filter-heading" class="py-8">
                            <div class="flex items-center justify-between space-x-6">
                                <SelectMenu placeholder="Select a model" v-model="form.model" :options="modelOptions()" />

                                <div class="w-56">
                                    <RangeSlider v-model="form.temperature" :min="0" :max="2" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{ $t('Temperature') }} </label>
                                        </template>
                                    </RangeSlider>
                                </div>

                                <div class="w-56">
                                    <RangeSlider v-model="form.top_p" :min="0" :max="1" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{ $t('Top p') }} </label>
                                        </template>
                                    </RangeSlider>
                                </div>

                                <div class="w-56">
                                    <RangeSlider v-model="form.presence_penalty" :min="-2" :max="2" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{ $t("Presence Penalty") }} </label>
                                        </template>
                                    </RangeSlider>
                                </div>

                                <div class="w-56">
                                    <RangeSlider v-model="form.frequency_penalty" :min="-2" :max="2" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{$t('Frequency Penalty')}} </label>
                                        </template>
                                    </RangeSlider>
                                </div>
                            </div>
                        </section>

                        <!-- Prompt fields -->
                        <div class=" lg:grid lg:grid-cols-2 lg:gap-x-8">
                            <div class="mt-6 lg:mt-0 bg-gray-50 rounded p-4">
                                <PromptEditor title="System" v-model="form.system_prompt" :attributes="availableAttributes" />
                            </div>

                            <div class="mt-6 lg:mt-0 bg-gray-50 rounded p-4">
                                <PromptEditor title="User" v-model="form.user_prompt" :attributes="availableAttributes" />
                            </div>
                        </div>

                        <ItemCompletionPreview @update:inputLanguage="changeInputLanguage"
                                               @update:outputLanguage="changeOutputLanguage"
                                               :preset="presetObj"
                                               :currentInputLanguage="form.input_language_id"
                                               :currentOutputLanguage="form.output_language_id"
                                               :languages="languages" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
