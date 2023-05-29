<script setup>
import {useForm} from "@inertiajs/vue3";
import {debounce} from "lodash";
import {notify} from "notiwind";
import ImageCard from "Jetstream/Components/ImageCard.vue";

const props = defineProps({
    item: Object
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
                group: "notification",
                title: "Success",
                text: "The item was updated!"
            }, 4000) // 4s
        }
    })
}, 1000);
</script>

<template>
    <div v-for="(cell, index) in form.items" :key="`cell-${item.id}-${index}`" class="h-14 flex justify-center">
        <ImageCard v-if="cell.path" :cell="cell" :item="item" />
        <input v-else
               v-model="cell.value"
               @input="updateItem"
               class="truncate bg-inherit w-full border-0 py-1.5 px-2 text-gray-900 ring-0 ring-gray-400 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-tf-blue-600 focus-visible:outline-none sm:text-sm sm:leading-6"
        />
    </div>
</template>
