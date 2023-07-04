<template>
    <div class="text-sm flex  justify-between">
        <div style="color: #525151" class="ml-2 text-right text-sm leading-6 text-gray-700 sm:mt-0">{{ item.name }}</div>
        <button @click="refreshApi(item)">
            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-7 mr-2" fill="#6674F5" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="CachedIcon">
                <path d="m19 8-4 4h3c0 3.31-2.69 6-6 6-1.01 0-1.97-.25-2.8-.7l-1.46 1.46C8.97 19.54 10.43 20 12 20c4.42 0 8-3.58 8-8h3l-4-4zM6 12c0-3.31 2.69-6 6-6 1.01 0 1.97.25 2.8.7l1.46-1.46C15.03 4.46 13.57 4 12 4c-4.42 0-8 3.58-8 8H1l4 4 4-4H6z"></path>
            </svg>
        </button>
    </div>
    <div class="mt-4 grid grid-cols-8">
        <div class="col-start-1 col-end-1 my-auto ml-4">
            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-7 " focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ReorderIcon">
                <path d="M3 15h18v-2H3v2zm0 4h18v-2H3v2zm0-8h18V9H3v2zm0-6v2h18V5H3z"></path>
            </svg>
        </div>
        <span class="ml-5 col-start-2 col-end-9">{{generatedText}}</span>
    </div>
</template>

<script setup>

import {ref, watch} from "vue";

const props = defineProps({
    id: Number,
    activeItem: Object,
    item: Object,
});
const refreshApi = () => {
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
            return;
        }

        if (event.data) {
            generatedText.value = generatedText.value + event.data;
        }
    })
}

setupStream(props.id, props.activeItem);

</script>
