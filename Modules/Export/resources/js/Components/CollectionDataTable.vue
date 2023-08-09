<script setup>
import Pagination from "Modules/Export/resources/js/Components/Pagination.vue";
import {computed} from "vue";
import CollectionItem from "Modules/Export/resources/js/Components/CollectionItem.vue";
import CollectionHeader from "Modules/Export/resources/js/Components/CollectionHeader.vue";
import {notify} from "notiwind";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
const props = defineProps({
    items: Object,
    headers: Array,
    idPage: Number,
    canUpdateCollection: Boolean,
    count: Number
});
const items = ref(props.items);
const loading = ref(false);

const colsCount = computed(() => items.value[Object.keys(items.value)[0]].length);
const gridColsCount = computed(() => `grid-cols-${colsCount.value + 1}`);
const colNumber = (index) => {
    return 5 * (5 - 1) + (index + 1);
}

const form = useForm({
    page: 1
})

const page = (id) => {
        loading.value = true;
        form.page = id;
        axios.post(route('export.pagination'), {page: form.page, id: props.idPage}).then((res) => {
            items.value = res.data.export;
            loading.value = false;
        })
}
</script>

<template>
    <div>
        <div class="items-center">
            <Pagination @update:page="page" :count="props.count" />

        </div>

        <div class="bg-white shadow">
            <div class=" text-sm" :class="gridColsCount">
<!--                <div>-->
<!--                    <div class="p-4 bg-gray-200 h-14" />-->
<!--                    <div v-for="(header, key) in items.data[0]" class="flex items-center px-2 bg-gray-200 h-14 border-t border-b border-gray-300">-->
<!--                        <CollectionHeader :canUpdate="canUpdateCollection" :header="key" />-->
<!--                    </div>-->
<!--                </div>-->
                <div class="grid" :class="gridColsCount">
                    <div class="p-4 bg-gray-200 h-14 text-center">
                        <span class="font-semibold text-gray-600"></span>
                    </div>
                    <div class="p-4 bg-gray-200 h-14 text-center" v-for="n in colsCount">
                        <span class="font-semibold text-gray-600">{{ n + (form.page !== 1 ? form.page : 0) }}</span>
                    </div>
                </div>
                    <div v-if="!loading" class="grid" :class="gridColsCount" v-for="(item, index) in items" :key="`item-${item.id}-${index}`">
                        <CollectionItem :canUpdate="canUpdateCollection" :item="item" :index="index" :colsCount="colsCount" :colNumber="index" />
                    </div>
            </div>
        </div>
    </div>

</template>
