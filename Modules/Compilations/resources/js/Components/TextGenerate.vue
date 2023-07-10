<template>
    <div class="text-sm flex  justify-between">
        <div style="color: #525151" class="ml-2 text-right text-sm leading-6 text-gray-700 sm:mt-0">{{ item.name }}</div>
        <button type="button" @click="refreshApi" class="mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" :class="loading ? 'animate-spin w-5 h-5' : 'w-5 h-5'" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                      d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                      clip-rule="evenodd" />
            </svg>
        </button>
    </div>
    <div class="m-4">

        <span>{{generatedContent}}</span>
    </div>
</template>

<script setup>
import {streamItemCompletion} from "Modules/OpenAI/resources/js/event-streams";

import {ref, watch} from "vue";
const loading = ref(true);
const currentLanguage = ref(null);

const props = defineProps({
    id: Number,
    activeItem: Object,
    item: Object,
    languages: Array,
});

console.log(props.activeItem)
console.log(props.item.output_language_id)
const refreshApi = () => {
    loading.value = true;
    generatedContent.value = '';
    setupStream(props.id, props.activeItem);
}

const generatingContent = ref(false);
const generatedContent = ref('');
const currentEventSource = ref(null);

const triggerLoading = (value) => {
    loading.value = value;
}

const setupStream = () => {
    generatingContent.value = true;

    currentEventSource.value = streamItemCompletion(props.id, props.activeItem, (data) => {
        // TODO: move away from callable
        if (loading.value) {
            triggerLoading(false);
        }

        generatedContent.value += data.content;
    }, () => {
        if(props.item.output_language_id){
            translateContent(generatedContent.value, props.item.output_language_id)
        }
        generatingContent.value = false;
        triggerLoading(false);
    });
}

setupStream(props.id, props.activeItem);

const translateContent = async (content, lang) => {
    // translatingContent.value = true;
    triggerLoading(true);
    await findLanguage(lang)
    if(currentLanguage.value){
        axios.post(route('deepl.translate-text'), {
            content: content,
            languageCode: currentLanguage.value.code,
        }).then((response) => {
            triggerLoading(false);
            generatedContent.value = response.data.content;
        }).catch(error => {
            triggerLoading(false);
        });
    }
}

const findLanguage = (languageId) => {
    if (languageId) {
        currentLanguage.value = props.languages.find((language) => language.id === languageId)
    } else {
        currentLanguage.value = null;
    }
}

</script>
