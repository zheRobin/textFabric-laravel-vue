<script setup>
import {ref} from "vue";
import CollectionItemPreview from "Modules/OpenAI/resources/js/Components/CollectionItemPreview.vue";
import CollectionItemCompletion from "Modules/OpenAI/resources/js/Components/CollectionItemCompletion.vue";

const props = defineProps({
    preset: Object,
});

const previewItem = ref(null);

const changePreviewItem = (value) => {
    previewItem.value = value;
}

</script>

<template>
    <CollectionItemPreview @itemChanged="changePreviewItem" :item="previewItem" class="mt-10" />

    <CollectionItemCompletion :preset="preset" :item="previewItem" />
</template>
