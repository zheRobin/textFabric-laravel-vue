<script setup>
import Pagination from "Modules/Export/resources/js/Components/Pagination.vue";
import {computed} from "vue";
import {ref} from "vue";
import axios from "axios";

const props = defineProps({
    headers: Array,
    idPage: Number,
    canUpdateCollection: Boolean,
    count: Number,
    itemsPagination: Object
});

const items = ref(false);
const loading = ref(false);

const colsCount = ref(null);
const rowsCount = ref(null);
const gridColsCount = computed(() => `grid-cols-${colsCount.value + 1}`);
const gridRowsCount = computed(() => rowsCount.value);

const colNumberRange = ref([]);

const paginate = (destination = false) => {
    loading.value = true;

    const uri = destination
        ? destination
        : route('export.items.index', props.idPage);

    axios.get(`${uri}`, {})
        .then((response) => {
            colsCount.value = response.data.data.data.length;
            items.value = response.data.data;
            loading.value = false;

            rowsCount.value = response.data.data.data[0].data.length;
            colNumberRange.value = Array.from({length: items.value?.to - items.value?.from + 1}, (x, i) => i + items.value?.from);
        });
}
paginate();
</script>

<template>
    <div>
        <div class="items-center" v-if="items && !loading">
            <Pagination @update:pagination="paginate" :links="items.links" />
        </div>

        <div class="bg-white shadow">
            <div class=" text-sm" :class="gridColsCount">

                <div class="grid" :class="gridColsCount">
                    <div class="p-4 bg-gray-200 h-14 text-center">
                        <span class="font-semibold text-gray-600"></span>
                    </div>
                    <div class="p-4 bg-gray-200 h-14 text-center" v-for="n in colNumberRange">
                        <span class="font-semibold text-gray-600">{{ n }}</span>
                    </div>
                </div>

                <div v-if="!loading" class="grid" :class="gridColsCount" v-for="indexNumber in gridRowsCount" :key="`grid-row-${indexNumber}`">
                    <div class="truncate border p-2 font-medium bg-gray-200 whitespace-normal" :title="items.data[0].data[indexNumber-1].header">
                        {{ items.data[0].data[indexNumber-1].header }}
                    </div>
                    <div v-for="(item, index) in items.data" :key="`grid-cell-${indexNumber}-${index}`" class="flex flex-col flex-auto h-auto border p-2">
                        {{ items.data[index].data[indexNumber-1].value }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
