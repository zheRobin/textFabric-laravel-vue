<script setup>
import {ref} from "vue";
import CollectionItemPreview from "Modules/OpenAI/resources/js/Components/Demo/CollectionItemPreview.vue";
import CollectionItemCompletion from "Modules/OpenAI/resources/js/Components/Demo/CollectionItemCompletion.vue";

const props = defineProps({
    preset: Object,
});

const previewItem = ref(null);

const changePreviewItem = (value) => {
    previewItem.value = value;
}

</script>

<template>
    <div class=" lg:grid lg:grid-cols-2 lg:gap-x-8">
        <div>
            <CollectionItemPreview @itemChanged="changePreviewItem"
                                   :item="previewItem"
                                   class="mt-10" />
        </div>

        <div>
            <CollectionItemCompletion :preset="preset"
                                      :item="previewItem"
                                      class="flex-col" />
        </div>
    </div>
</template>
