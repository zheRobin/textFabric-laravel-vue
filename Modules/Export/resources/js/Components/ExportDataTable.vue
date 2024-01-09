<script setup>
import Pagination from "Modules/Export/resources/js/Components/Pagination.vue";
import CollectionItemDatas from "./CollectionItemDatas.vue";
import CollectionItemCompletions from "./CollectionItemCompletions.vue";
import CollectionItemTranslations from "./CollectionItemTranslations.vue";
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
const rowsDCount = ref(null);
const rowsCCount = ref(null);
const rowsTCount = ref(null);
const gridColsCount = computed(() => `grid-cols-${colsCount.value + 1}`);
const gridRowsDCount = computed(() => rowsDCount.value);
const gridRowsCCount = computed(() => rowsCCount.value);
const gridRowsTCount = computed(() => rowsTCount.value);

const colNumberRange = ref([]);

const paginate = (destination = false) => {
    loading.value = true;

    const uri = destination
        ? destination
        : route('export.items.index', props.idPage);

    axios.get(`${uri}`)
        .then((response) => {
            colsCount.value = response.data.data.data.length;
            items.value = response.data.data;
            loading.value = false;

            rowsDCount.value = response.data.data.data[0]?.data?.length;
            rowsCCount.value = response.data.data.data[0]?.completions?.length;
            rowsTCount.value = response.data.data.data[0]?.translations?.length;
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

                <div v-if="!loading && items && items.data && items.data[0] && items.data[0].data" class="grid" :class="gridColsCount" v-for="indexNumber in gridRowsDCount" :key="`grid-row-${indexNumber}`">
                    <div class="truncate border p-2 font-medium bg-gray-200 whitespace-normal" :title="items.data[0].data[indexNumber-1].header">
                        {{ items.data[0].data[indexNumber-1].header }}
                    </div>
                    <CollectionItemDatas :item="items.data" :colsCount="colsCount" :indexNumber="indexNumber" />
                </div>
                <div v-if="!loading && items && items.data && items.data[0] && items.data[0].completions" class="grid" :class="gridColsCount" v-for="indexNumber in gridRowsCCount" :key="`grid-row-${indexNumber}`">
                    <div class="truncate border p-2 font-medium bg-gray-200 whitespace-normal" :title="items.data[0].completions[indexNumber-1].header">
                        {{ items.data[0].completions[indexNumber-1].header }}
                    </div>
                    <CollectionItemCompletions :item="items.data" :colsCount="colsCount" :indexNumber="indexNumber" />
                </div>
                <div v-if="!loading && items && items.data && items.data[0] && items.data[0].translations" class="grid" :class="gridColsCount" v-for="indexNumber in gridRowsTCount" :key="`grid-row-${indexNumber}`">
                    <div class="truncate border p-2 font-medium bg-gray-200 whitespace-normal" :title="items.data[0].translations[indexNumber-1].header">
                        {{ items.data[0].translations[indexNumber-1].header }}
                    </div>
                    <CollectionItemTranslations :item="items.data" :colsCount="colsCount" :indexNumber="indexNumber" />
                </div>
            </div>
        </div>
    </div>

</template>
