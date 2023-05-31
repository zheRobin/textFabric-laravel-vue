<script setup>
import Pagination from "Jetstream/Components/Pagination.vue";
import {computed} from "vue";
import CollectionItem from "Modules/Imports/resources/js/Components/CollectionItem.vue";
import CollectionHeader from "Modules/Imports/resources/js/Components/CollectionHeader.vue";

const props = defineProps({
    items: Object,
    headers: Array,
});

const colsCount = computed(() => `grid-cols-${props.items.data.length + 1}`);

const colNumber = (index) => {
    return props.items.per_page * (props.items.current_page - 1) + (index + 1);
};
</script>

<template>
    <div>
        <div class="bg-white shadow">
            <div class="grid text-sm border-gray-400" :class="colsCount">
                <div class="divide-y divide-gray-400 ">
                    <div class="p-4 bg-gray-200 h-14" />
                    <div v-for="header in headers" class="flex items-center px-2 bg-gray-200 h-14">
                        <CollectionHeader :header="header" />
                    </div>
                </div>

                <div class="divide-x divide-y divide-gray-400" v-for="(item, index) in items.data" :key="`item-${item.id}-${index}`" :class="{'bg-gray-100': index % 2 !== 0}">
                    <div class="p-4 bg-gray-200 h-14 text-center">
                        <span class="font-semibold text-gray-600"> {{ colNumber(index) }} </span>
                    </div>
                    <CollectionItem :item="item" />
                </div>
            </div>
        </div>

        <Pagination :links="items.links" />
    </div>

</template>
