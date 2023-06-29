<script setup>
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {itemCompletion} from "Modules/OpenAI/resources/js/axios";
import {ref} from "vue";

const props = defineProps({
    item: Object,
    preset: Number,
})

const generatedText = ref(null);

const generate = () => {
    itemCompletion(props.preset, props.item.id)
        .then((response) => {
            generatedText.value = response.data;
        });
}

</script>

<template>
    <div class="overflow-hidden bg-gray-50 rounded mt-10 justify-center">
        <div class="px-4 py-4 sm:px-6">
            <h3 class="text-base font-semibold leading-7 text-gray-900">{{$t('Preview')}}</h3>
        </div>

        <div class="h-44 px-4  text-base text-center leading-7 text-gray-900 overflow-scroll">
            {{ generatedText }}
        </div>

        <div class="mt-6 border-t px-6 py-2 text-right">
            <PrimaryButton @click="generate"> {{$t('Generate')}} </PrimaryButton>
        </div>
    </div>
</template>
