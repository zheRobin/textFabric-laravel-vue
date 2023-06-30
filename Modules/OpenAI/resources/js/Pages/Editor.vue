<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import {PlusCircleIcon, ArrowDownTrayIcon, PlusIcon} from "@heroicons/vue/20/solid";
import {ref} from "vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import RangeSlider from "Jetstream/Components/RangeSlider.vue";
import TagInput from "Jetstream/Components/TagInput.vue";
import PromptEditor from "Modules/OpenAI/resources/js/Components/PromptEditor.vue";
import CollectionItemPreview from "Modules/OpenAI/resources/js/Components/CollectionItemPreview.vue";
import CollectionItemCompletion from "Modules/OpenAI/resources/js/Components/CollectionItemCompletion.vue";
import {notify} from "notiwind";

const props = defineProps({
    presets: Array,
    previewItem: Object,
    models: Array,
});

const collectionId = usePage().props.auth.user.current_collection.id;

const form = useForm({
    collection_id: collectionId,
    model: null,
    system_prompt: '',
    user_prompt: '',
    name: null,
    temperature: 1,
    top_p: 1,
    n: 1,
    max_tokens: 2048,
    presence_penalty: 0,
    frequency_penalty: 0,
    stop: [],
});

const emptyState = {
    collection_id: collectionId,
    model: null,
    system_prompt: '',
    user_prompt: '',
    name: null,
    temperature: 1,
    top_p: 1,
    n: 1,
    max_tokens: 2048,
    presence_penalty: 0,
    frequency_penalty: 0,
    stop: [],
};

const modelOptions = () => {
    const models = [];

    props.models.forEach((el) => {
        models.push({value: el,label: el});
    })

    return models;
}

const availableAttributes = usePage().props.auth.user.current_collection.headers;

const selectedPreset = ref(null);
const addingPreset = ref(false);

const previewItem = ref(null);

const presetOptions = () => {
    const presets = [];

    props.presets.forEach((el) => {
        presets.push({value: el.id,label: el.name});
    })

    return presets;
}

const changePreset = (value) => {
    selectedPreset.value = value;
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
    selectedPreset.value = null;
    addingPreset.value = true;
    form.defaults(emptyState);
    form.reset();
}

const savePreset = () => {
    if (selectedPreset.value) {
        form.patch(route('presets.update', selectedPreset.value), {
            errorBag: 'errors',
            preserveScroll: true,
            onError: (error) => {
                notify({
                    group: "error",
                    title: "Error",
                    text: error[Object.keys(error)[0]] ?? "Something wrong happens."
                }, 4000) // 4s
            }
        })
    }

    if (addingPreset.value) {
        form.post(route('presets.store'), {
            errorBag: 'errors',
            preserveScroll: true,
            onError: (error) => {
                notify({
                    group: "error",
                    title: "Error",
                    text: error[Object.keys(error)[0]] ?? "Something wrong happens."
                }, 4000) // 4s
            }
        })
    }

}

const deletePreset = () => {
    if (addingPreset.value) {
        addingPreset.value = false;
        // form.reset();
    }
}

const changePreviewItem = (value) => {
    previewItem.value = value;
}
</script>

<template>
    <AppLayout title="Editor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editor
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="mx-auto px-6 py-6">
                        <div class="flex border-b border-gray-200 pb-10 items-center">
                            <div class="items-center flex-1">
                                <label class="mr-2 font-medium">{{ $t('Preset') }}:</label>
                                <SelectMenu @update:modelValue="changePreset" v-model="selectedPreset" :options="presetOptions()" placeholder="Select" class="inline-flex" />
                                <button type="button" @click="addPreset" class="ml-2 inline-flex items-center gap-x-1.5 rounded-md bg-tf-blue-500 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-tf-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-500">
                                    {{ $t('Add') }}
                                    <PlusCircleIcon class="-mr-0.5 h-5 w-5" aria-hidden="true"></PlusCircleIcon>
                                </button>
                            </div>

                            <div v-if="selectedPreset || addingPreset" class="items-center">
                                <label class="mr-2 font-medium">{{$t('Name')}}:</label>
                                <TextInput v-model="form.name" type="text" class="w-36" />
                                <button @click="savePreset" type="button" class="ml-2 inline-flex items-center gap-x-1.5 rounded-md bg-tf-blue-500 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-tf-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-500">
                                    {{$t('Save')}}
                                    <ArrowDownTrayIcon class="-mr-0.5 h-5 w-5" aria-hidden="true"></ArrowDownTrayIcon>
                                </button>
                                <!--                                <button @click="deletePreset" type="button" class="ml-2 inline-flex items-center gap-x-1.5 rounded-md bg-red-500 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-500">-->
                                <!--                                    Delete-->
                                <!--                                    <MinusCircleIcon class="-mr-0.5 h-5 w-5" aria-hidden="true"></MinusCircleIcon>-->
                                <!--                                </button>-->
                            </div>
                        </div>

                        <div class="pt-10 lg:grid lg:grid-cols-3 lg:gap-x-8">

                            <!-- Prompt fields -->
                            <div>
                                <PromptEditor title="System" v-model="form.system_prompt" :attributes="availableAttributes" />
                                <CollectionItemPreview @itemChanged="changePreviewItem" :item="previewItem" class="mt-10" />
                            </div>

                            <div>
                                <PromptEditor title="User" v-model="form.user_prompt" :attributes="availableAttributes" />

                                <CollectionItemCompletion :preset="selectedPreset" :item="previewItem" />

                            </div>

                            <aside>
                                <h2 class="sr-only">{{$t('Filters')}}</h2>

                                <button type="button" class="inline-flex items-center lg:hidden">
                                    <span class="text-sm font-medium text-gray-700">{{$t('Filters')}}</span>
                                    <PlusIcon class="ml-1 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                                </button>

                                <div class="hidden lg:block space-y-6">
                                    <SelectMenu placeholder="Select a model" v-model="form.model" :options="modelOptions()" />

                                    <RangeSlider v-model="form.temperature" :min="0" :max="2" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{ $t('Temperature') }} </label>
                                        </template>
                                    </RangeSlider>

                                    <RangeSlider v-model="form.top_p" :min="0" :max="1" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{ $t('Top p') }} </label>
                                        </template>
                                    </RangeSlider>

                                    <RangeSlider v-model="form.n" :min="1" :max="20" :step="1">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> N </label>
                                        </template>
                                    </RangeSlider>

                                    <RangeSlider v-model="form.max_tokens" :min="1" :max="2048" :step="1">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{ $t("Max Tokens") }} </label>
                                        </template>
                                    </RangeSlider>

                                    <RangeSlider v-model="form.presence_penalty" :min="-2" :max="2" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{ $t("Presence Penalty") }} </label>
                                        </template>
                                    </RangeSlider>

                                    <RangeSlider v-model="form.frequency_penalty" :min="-2" :max="2" :step="0.01">
                                        <template #label>
                                            <label class="inline-flex text-sm font-medium"> {{$t('Frequency Penalty')}} </label>
                                        </template>
                                    </RangeSlider>

                                    <div>
                                        <label class="inline-flex text-sm font-medium mb-4"> {{$t('Stop Sequences')}} </label>
                                        <TagInput v-model="form.stop" />
                                    </div>

                                </div>
                            </aside>

                            <!--                            <div class="col-span-2">Preview</div>-->

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
