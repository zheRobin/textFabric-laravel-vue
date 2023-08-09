<script setup>
import {computed} from "vue";
import CollectionItem from "Modules/Imports/resources/js/Components/Demo/CollectionItem.vue";
import CollectionHeader from "Modules/Imports/resources/js/Components/Demo/CollectionHeader.vue";

const props = defineProps({
    items: Object,
    headers: Array,
    canUpdateCollection: Boolean,
});

const colsCount = computed(() => props.items.length);
const gridColsCount = computed(() => `grid-cols-${colsCount.value + 1}`);

</script>

<template>
    <div>
        <div class="bg-white shadow">
            <div class="grid text-sm border-gray-400" :class="gridColsCount">
                <div>
                    <div class="p-4 bg-gray-200 h-14" />
                    <div v-for="header in headers" class="flex items-center px-2 bg-gray-200 h-14 border-t border-b border-gray-300">
                        <CollectionHeader :header="header" />
                    </div>
                </div>

                <div class="" v-for="(item, index) in items" :key="`item-${item.id}-${index}`" :class="{'bg-gray-100': index % 2 !== 0}">
                    <div class="p-4 bg-gray-200 h-14 text-center">
                        <span class="font-semibold text-gray-600"> {{ index + 1 }} </span>
                    </div>
                    <CollectionItem :item="item" :colsCount="colsCount" :colNumber="index" />
                </div>
            </div>
        </div>
    </div>

</template>
