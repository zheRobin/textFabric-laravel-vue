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
    <div class="mt-4 grid grid-cols-8">
        <div class="col-start-1 col-end-1 my-auto ml-4">
            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-7 " focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ReorderIcon">
                <path d="M3 15h18v-2H3v2zm0 4h18v-2H3v2zm0-8h18V9H3v2zm0-6v2h18V5H3z"></path>
            </svg>
        </div>
        <span class="mr-6 col-start-2 col-end-9">{{generatedText}}</span>
    </div>
</template>

<script setup>

import {ref, watch} from "vue";
const loading = ref(true);
const props = defineProps({
    id: Number,
    activeItem: Object,
    item: Object,
});
const refreshApi = () => {
    loading.value = true;
    generatedText.value = '';
    setupStream(props.id, props.activeItem);
}

const generatedText = ref('');
const setupStream = (dataID, activeItemID) => {
    const eventSource = new EventSource(route('openai.item-completion', {
        preset: dataID,
        item: activeItemID
    }));

    eventSource.addEventListener('update', event => {
        if (event.data === "<END_STREAMING_SSE>") {
            eventSource.close();
            loading.value = false;
            return;
        }

        if (event.data) {
            generatedText.value = generatedText.value + event.data;
        }
    })
}

setupStream(props.id, props.activeItem);

</script>
