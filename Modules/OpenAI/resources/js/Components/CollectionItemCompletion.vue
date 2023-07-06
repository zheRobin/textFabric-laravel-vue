<script setup>
import {ref, watch} from "vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {streamItemCompletion} from "Modules/OpenAI/resources/js/event-streams";
import Spinner from "Jetstream/Components/Spinner.vue";
import Dropdown from "Jetstream/Components/Dropdown.vue";
import DropdownLink from "Jetstream/Components/DropdownLink.vue";

const props = defineProps({
    item: Object,
    preset: Object,
    languageId: Number,
    languages: Array,
    updatePreset: Function,
});

const emit = defineEmits(['update:outputLanguage']);

watch(() => props.item, (value, oldValue) => {
    if (oldValue) {
        generate();
    }
});

watch(() => props.preset.id, () => {
    purgeStream();
});

const findLanguage = (languageId) => {
    if (languageId) {
        currentLanguage.value = props.languages.find((language) => language.id === languageId)
    } else {
        currentLanguage.value = null;
    }
}

const currentLanguage = ref(null);
findLanguage(props.languageId);

watch(() => props.languageId, (langId) => {
    findLanguage(langId);
});

const generatedContent = ref('');

const currentEventSource = ref(null);

const generatingContent = ref(false);

const translatingContent = ref(false);

const loading = ref(false);

const setupStream = () => {
    generatingContent.value = true;

    currentEventSource.value = streamItemCompletion(props.preset.id, props.item.id, (data) => {
        // TODO: move away from callable
        if (loading.value) {
            triggerLoading(false);
        }

        generatedContent.value += data.content;
    }, () => {
        generatingContent.value = false;
        triggerLoading(false);
    });
}

const generate = async () => {
    await purgeStream();
    triggerLoading(true);
    setupStream();
}

const clearOutput = () => {
    generatedContent.value = '';
}

const purgeStream = async () => {
    if (currentEventSource.value) {
        await currentEventSource.value.close();
    }

    clearOutput();
}

const triggerLoading = (value) => {
    loading.value = value;
}

const changeLanguage = (language) => {
    currentLanguage.value = language;
    emit('update:outputLanguage', language);
}

const translateContent = () => {
    translatingContent.value = true;
    triggerLoading(true);
    axios.post(route('deepl.translate-text'), {
        content: generatedContent.value,
        languageCode: currentLanguage.value.code,
    }).then((response) => {
        triggerLoading(false);
        generatedContent.value = response.data.content;
        translatingContent.value = false;
    }).catch(error => {
        triggerLoading(false);
        translatingContent.value = false;
    });
}
</script>

<template>
    <div class="overflow-hidden bg-gray-50 rounded mt-10 flex">
        <div class="px-6 py-3 flex justify-between items-center border-b">
            <div class="flex items-center">
                <Dropdown width="30" height="48">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button type="button" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                {{ currentLanguage ? currentLanguage.name : 'Language' }}
                                <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>
                        </span>
                    </template>
                    <template #content>
                        <div class="w-36">
                            <DropdownLink @click="changeLanguage(language)" v-for="language in languages" as="button">
                                {{ language.name }}
                            </DropdownLink>
                        </div>
                    </template>
                </Dropdown>

                <PrimaryButton @click="translateContent" v-if="generatedContent && currentLanguage && !generatingContent" class="inline-flex ml-3"> Translate </PrimaryButton>
            </div>

            <PrimaryButton @click="generate" :disabled="generatingContent" :class="{ 'opacity-50': generatingContent }">
                {{$t('Generate')}}
            </PrimaryButton>
        </div>

        <div class="sm:px-6 flex justify-between mb-2 mt-3">
            <label class="text-sm font-medium flex-1">{{$t('Preview')}}</label>
        </div>

        <div v-if="loading" class="min-h-44 flex justify-center items-center mb-6">
            <Spinner class="" />
        </div>

        <div v-else class="min-h-44 px-10 text-base leading-7 text-gray-900 mb-6 whitespace-pre-wrap">
            {{ generatedContent }}
        </div>
    </div>
</template>
