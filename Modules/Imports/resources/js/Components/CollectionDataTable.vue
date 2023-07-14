<script setup>
import Pagination from "Jetstream/Components/Pagination.vue";
import {computed} from "vue";
import CollectionItem from "Modules/Imports/resources/js/Components/CollectionItem.vue";
import CollectionHeader from "Modules/Imports/resources/js/Components/CollectionHeader.vue";

const props = defineProps({
    items: Object,
    headers: Array,
});

const colsCount = computed(() => props.items.data.length);
const gridColsCount = computed(() => `grid-cols-${colsCount.value + 1}`);

const colNumber = (index) => {
    return props.items.per_page * (props.items.current_page - 1) + (index + 1);
}
</script>

<template>
    <div>
        <Pagination :links="items.links" />

        <div class="bg-white shadow">
            <div class="grid text-sm border-gray-400" :class="gridColsCount">
                <div>
                    <div class="p-4 bg-gray-200 h-14" />
                    <div v-for="header in headers" class="flex items-center px-2 bg-gray-200 h-14 border-t border-b border-gray-300">
                        <CollectionHeader :header="header" />
                    </div>
                </div>

                <div class="" v-for="(item, index) in items.data" :key="`item-${item.id}-${index}`" :class="{'bg-gray-100': index % 2 !== 0}">
                    <div class="p-4 bg-gray-200 h-14 text-center">
                        <span class="font-semibold text-gray-600"> {{ colNumber(index) }} </span>
                    </div>
                    <CollectionItem :item="item" :colsCount="colsCount" :colNumber="index" />
                </div>
            </div>
        </div>
    </div>

</template>
