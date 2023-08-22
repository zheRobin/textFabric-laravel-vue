<script setup>
import {useForm} from "@inertiajs/vue3";

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
</script>

<template>
    <div v-for="(item, index) in form.items" :key="`cell-${item.id}-${index}`" class="flex flex-col flex-auto h-auto border pt-2 pl-2" :class="(index+1) % 2 === 0 ? 'bg-gray-100' : 'bg-white'">
        {{item.value}}
    </div>
</template>
