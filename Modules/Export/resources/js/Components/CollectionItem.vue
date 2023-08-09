<script setup>
import {useForm} from "@inertiajs/vue3";
import {notify} from "notiwind";
import ImageCard from "Jetstream/Components/ImageCard.vue";
import CellInput from "Modules/Export/resources/js/Components/CellInput.vue";

const props = defineProps({
    item: Object,
    colsCount: Number,
    colNumber: Number,
    canUpdate: Boolean,
    index: String
});

const form = useForm({
    items: props.item
});
const updateItem = () => {
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
}
</script>

<template>
    <div class="h-auto items-center px-2 bg-gray-200 h-14 pt-5 border-t border-gray-300">
        <div class="font-semibold text-gray-600 "> {{ props.index.replace(/_def$/, '') }} </div>
    </div>
    <div v-for="(item, index) in form.items" :key="`cell-${item.id}-${index}`" class="grid h-auto border pt-2 pl-2" :class="(index+1) % 2 === 0 ? 'bg-gray-100' : 'bg-white'">
        {{item}}
    </div>
</template>
