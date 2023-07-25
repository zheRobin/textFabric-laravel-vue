<script setup>
import Pagination from "Jetstream/Components/Pagination.vue";
import {computed} from "vue";
import CollectionItem from "Modules/Export/resources/js/Components/CollectionItem.vue";
import CollectionHeader from "Modules/Export/resources/js/Components/CollectionHeader.vue";

const props = defineProps({
    items: Object,
    headers: Array,
    canUpdateCollection: Boolean,
});

console.log(props.items.data[0], 'items')

const colsCount = computed(() => props.items.data[0].length);
const gridColsCount = computed(() => `grid-cols-${colsCount.value + 1}`);
console.log(props.items.data[0], 'value')
const colNumber = (index) => {
    return 5 * (5 - 1) + (index + 1);
}
</script>

<template>
    <div>
<!--        <Pagination :links="items.links" />-->

        <div class="bg-white shadow">
            <div class=" text-sm" :class="gridColsCount">
<!--                <div>-->
<!--                    <div class="p-4 bg-gray-200 h-14" />-->
<!--                    <div v-for="(header, key) in items.data[0]" class="flex items-center px-2 bg-gray-200 h-14 border-t border-b border-gray-300">-->
<!--                        <CollectionHeader :canUpdate="canUpdateCollection" :header="key" />-->
<!--                    </div>-->
<!--                </div>-->
                <div class="grid grid-cols-4" v-for="(item, index) in items.data[0]" :key="`item-${item.id}-${index}`" :class="{'bg-gray-100': index % 2 !== 0}">
                    <CollectionItem :canUpdate="canUpdateCollection" :item="item" :index="index" :colsCount="colsCount" :colNumber="index" />
                </div>
            </div>
        </div>
    </div>

</template>
