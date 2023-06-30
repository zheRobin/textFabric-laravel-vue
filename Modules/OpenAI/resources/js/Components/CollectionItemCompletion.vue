<script setup>
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {ref} from "vue";

const props = defineProps({
    item: Object,
    preset: Number,
})

const generatedText = ref('');

const setupStream = () => {
    const eventSource = new EventSource(route('openai.item-completion', {
        preset: props.preset,
        item: props.item.id
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

const generate = () => {
    generatedText.value = '';
    setupStream();
}

</script>

<template>
    <div class="overflow-hidden bg-gray-50 rounded mt-10 justify-center">
        <div class="px-4 py-4 sm:px-6">
            <h3 class="text-base font-semibold leading-7 text-gray-900">Preview</h3>
        </div>

        <div class="h-44 px-10 text-base leading-7 text-gray-900 overflow-scroll">
            {{ generatedText }}
        </div>

        <div class="mt-6 border-t px-6 py-2 text-right">
            <PrimaryButton @click="generate"> Generate </PrimaryButton>
        </div>
    </div>
</template>
