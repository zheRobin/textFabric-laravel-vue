<script setup>
import Pagination from "Jetstream/Components/Pagination.vue";
import {computed} from "vue";
import Dropdown from "Jetstream/Components/Dropdown.vue";
import CollectionItem from "Modules/Imports/resources/js/Components/CollectionItem.vue";

const props = defineProps({
    items: Object,
    headers: Array,
});

const colsCount = computed(() => `grid-cols-${props.items.data.length + 1}`);

const colNumber = (index) => {
    return props.items.per_page * (props.items.current_page - 1) + (index + 1);
};

const makeTitle = (slug) => {
    if (!slug) {
        return 'Undefined';
    }

    const title = slug.split('_').join(' ');

    return title.charAt(0).toUpperCase() + title.slice(1);
}

</script>

<template>
    <div>
        <div class="overflow-hidden bg-white shadow">
            <div class="grid text-sm border-gray-400" :class="colsCount">
                <div class="divide-y divide-gray-400 ">
                    <div class="p-4 bg-gray-200 h-14" />
                    <div v-for="header in headers" class="flex items-center px-2 bg-gray-200 h-14 truncate" :title="makeTitle(header.name)">
                        <div class="truncate">
                            <span>
                                {{ makeTitle(header.name) }}
                            </span>
                            <Dropdown align="right" width="60">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            type
                                            <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                            </Dropdown>
                        </div>
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

        <Pagination :links="items.links"/>
    </div>

</template>
