<style>
.custom-tooltip {
    position: relative;
}

.custom-tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
}

.custom-tooltip:hover::after {
    opacity: 1;
}
</style>

<template>
    <div class="text-sm flex  justify-between">
        <div style="color: #525151" class="ml-2 text-right text-sm leading-6 text-gray-700 sm:mt-0">{{ preset.name }}</div>
        <div>
            <button class="mr-2 custom-tooltip" v-if="activeCopyButton" :data-tooltip="copyStatus ? $t('Copied!') : $t('Click to copy')" @click="copy">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                </svg>
            </button>
            <button type="button" @click="refreshApi" :data-tooltip="$t('re-generate')" class="mr-4  custom-tooltip">
                <svg xmlns="http://www.w3.org/2000/svg" :class="loading ? 'animate-spin w-5 h-5' : 'w-5 h-5'" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                          clip-rule="evenodd" />
                </svg>
            </button>
        </div>

    </div>
    <div class="m-4">
        <div class="whitespace-pre-wrap">{{generatedContent}}</div>
    </div>
</template>

<script setup>
import {streamDemoItemCompletion} from "Modules/OpenAI/resources/js/event-streams";
import {onUnmounted, ref, watch} from "vue";

const loading = ref(true);

const props = defineProps({
    id: String,
    activeItem: Object,
    preset: Object,
});

const refreshApi = async () => {
    currentEventSource.value.close()
    generatedContent.value = '';
    setupStream();
    loading.value = true;
}

const copy = () => {
    copyStatus.value = false;
    navigator.clipboard.writeText(generatedContent.value)
        .then(() => {
            copyStatus.value = true;
            setTimeout(() => {
                copyStatus.value = false;
            }, 1000)
        })
        .catch((err) => {
            //
        });
}

const generatingContent = ref(false);
const generatedContent = ref('');
const currentEventSource = ref(null);
const activeCopyButton = ref(false);
const copyStatus = ref(false);

const triggerLoading = (value) => {
    loading.value = value;
}

watch(() => props.activeItem, (value, oldValue) => {
    if (oldValue) {
        generatedContent.value = '';
        currentEventSource.value.close()
        setupStream();
    }
});

const setupStream = () => {
    generatingContent.value = true;
    triggerLoading(true);
    if(loading.value){
        currentEventSource.value = streamDemoItemCompletion(props.activeItem, props.preset.system_prompt, props.preset.user_prompt, (data) => {
            activeCopyButton.value = false;
            // TODO: move away from callable
            generatedContent.value += data.content;
        }, () => {
            generatingContent.value = false;
            triggerLoading(false);
            activeCopyButton.value = true;
        })
    }
}

setupStream();
</script>
