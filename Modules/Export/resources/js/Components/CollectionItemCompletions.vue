<script setup>
import { useForm } from "@inertiajs/vue3";
import CellInput from "./CellInput.vue";
import { notify } from "notiwind";
import { trans } from "laravel-vue-i18n";
const props = defineProps({
    item: Object,
    colsCount: Number,
    colNumber: Number,
    indexNumber: Number,
    canUpdate: Boolean,
    index: String
});
const form = useForm({
    items: props.item
});
const updateItem = () => {
    form.put(route('export.item.update', props.item[0].export_id), {
        errorBag: () => {
            notify({
                group: "error",
                title: "Error",
                text: "Update failed"
            }, 4000)
        },
        // preserveScroll: true,
        onSuccess: () => {
            notify({
                group: "success",
                title: trans("Success"),
                text: trans("The item was updated!")
            }, 4000)
        }
    })
}
</script>

<template>
    <div v-for="(item, index) in form.items" :key="`grid-cell-${indexNumber}-${index}`" class="flex flex-col flex-auto h-auto border pt-2 pl-2" :class="(index+1) % 2 === 0 ? 'bg-gray-100' : 'bg-white'">
        <CellInput
            v-model="item.completions[indexNumber-1].value"
            :editable="true"
            @update:modelValue="updateItem"
            :colsCount="colsCount"
            :colNumber="colNumber"
            :rowNumber="index"
            :rowsCount="form.items.length"
        />
    </div>
</template>
