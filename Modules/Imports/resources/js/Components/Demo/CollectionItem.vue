<script setup>
import {useForm} from "@inertiajs/vue3";
import CellInput from "Modules/Imports/resources/js/Components/CellInput.vue";
import {getActiveLanguage, trans} from "laravel-vue-i18n";
import {saveItem} from "Modules/Imports/resources/js/collection";
import {notify} from "notiwind";

const props = defineProps({
    item: Object,
    colsCount: Number,
    colNumber: Number,
});

const form = useForm({
    items: props.item.data
});

const updateItem = () => {
    saveItem(props.colNumber, props.item, getActiveLanguage());

    notify({
        group: "success",
        title: trans("Success"),
        text: trans("The item was updated!")
    }, 4000);
}
</script>

<template>
    <div v-for="(cell, index) in props.item" :key="`cell-${item.id}-${index}`" class="h-14 border">
        <CellInput v-model="cell.value"
                   @update:modelValue="updateItem"
                   :colsCount="colsCount"
                   :colNumber="colNumber"
                   :rowsCount="cell.length"
                   :rowNumber="index"
                   :editable="true" />
    </div>
</template>
