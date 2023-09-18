<script setup>
import {useForm} from "@inertiajs/vue3";
import {notify} from "notiwind";
import ImageCard from "Jetstream/Components/ImageCard.vue";
import CellInput from "Modules/Imports/resources/js/Components/CellInput.vue";
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    item: Object,
    colsCount: Number,
    colNumber: Number,
    canUpdate: Boolean,
});

const form = useForm({
    items: props.item.data
});

const updateItem = () => {
    form.put(route('collection-items.update', props.item),{
        errorBag: 'updateCollectionItem',
        preserveScroll: true,
        onSuccess: () => {
            notify({
                group: "success",
                title: trans("Success"),
                text: trans("The item was updated!")
            }, 4000) // 4s
        }
    })
}
</script>

<template>
    <div v-for="(cell, index) in form.items" :key="`cell-${item.id}-${index}`" class="h-14 border">
        <ImageCard :canUpload="canUpdate" v-if="cell.path" :cell="cell" :item="item" />

        <CellInput v-else
                   v-model="cell.value"
                   @update:modelValue="updateItem"
                   :colsCount="colsCount"
                   :colNumber="colNumber"
                   :rowsCount="form.items.length"
                   :rowNumber="index"
                   :editable="canUpdate" />
    </div>
</template>
