<script setup>
import {useForm} from "@inertiajs/vue3";
import {debounce} from "lodash";
import {notify} from "notiwind";
import ImageCard from "Jetstream/Components/ImageCard.vue";
import CellInput from "Modules/Imports/Resources/js/Components/CellInput.vue";

const props = defineProps({
    item: Object,
    colsCount: Number,
    colNumber: Number,
});

const form = useForm({
    items: props.item.data
});

const updateItem = debounce( () => {
    form.put(route('collection-items.update', props.item),{
        errorBag: 'updateCollectionItem',
        preserveScroll: true,
        onSuccess: () => {
            notify({
                group: "success",
                title: "Success",
                text: "The item was updated!"
            }, 4000) // 4s
        }
    })
}, 1000);
</script>

<template>
    <div v-for="(cell, index) in form.items" :key="`cell-${item.id}-${index}`" class="h-14 border">
        <ImageCard v-if="cell.path" :cell="cell" :item="item" />

        <CellInput v-else
                   v-model="cell.value"
                   @update:modelValue="updateItem"
                   :colsCount="colsCount"
                   :colNumber="colNumber"
                   :rowsCount="form.items.length"
                   :rowNumber="index" />
    </div>
</template>
