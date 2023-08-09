<script setup>
import {computed, ref, watch} from "vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {streamDemoItemCompletion} from "Modules/OpenAI/resources/js/event-streams";
import Spinner from "Jetstream/Components/Spinner.vue";
import {notify} from "notiwind";
import {trans} from "laravel-vue-i18n";
import CopyToClipboard from "Jetstream/Components/CopyToClipboard.vue";

const props = defineProps({
    item: Object,
    preset: Object,
});

const generationText = computed(() => {
    return props.needPresetUpdate
        ? `${trans('Save')} & ${trans('Generate')}`
        :  trans('Generate');
});

watch(() => props.item, (value, oldValue) => {
    if (oldValue) {
        generate();
    }
});

watch(() => props.preset, () => {
    purgeStream();
});

const generatedContent = ref('');

const currentEventSource = ref(null);

const generatingContent = ref(false);

const loading = ref(false);

const setupStream = () => {
    generatingContent.value = true;

    currentEventSource.value = streamDemoItemCompletion(props.item, props.preset.system_prompt, props.preset.user_prompt, (data) => {
        // TODO: move away from callable
        if (loading.value) {
            triggerLoading(false);
        }

        generatedContent.value += data.content;
    }, () => {
        generatingContent.value = false;
        triggerLoading(false);
    }, (error) => {
        generatingContent.value = false;
        triggerLoading(false);
        notify({
            group: "error",
            title: "Error",
            text: error
        }, 4000)
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

</script>

<template>
    <div class="bg-gray-50 rounded mt-10">
        <div class="px-6 py-3 flex justify-end items-center border-b">
            <div class="flex items-center">
                <CopyToClipboard v-if="!generatingContent && generatedContent" :content="generatedContent" class="mr-2" />

                <PrimaryButton @click="generate" :disabled="generatingContent" :class="{ 'opacity-50': generatingContent }">
                    {{ generationText }}
                </PrimaryButton>
            </div>
        </div>

        <div v-if="loading" class="min-h-56 flex justify-center items-center">
            <Spinner class="" />
        </div>

        <div v-else class="overflow-hidden min-h-56 pt-3 px-10 text-base leading-7 text-gray-900 pb-6 whitespace-pre-wrap">
            {{ generatedContent }}
        </div>
    </div>
</template>
