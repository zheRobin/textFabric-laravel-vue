<script setup>
import ImageCard from "Jetstream/Components/ImageCard.vue";
import Pagination from "Jetstream/Components/Pagination.vue";

defineProps({
    items: Object,
    links: Array,
    headers: Array,
});

const makeTitle = function (slug) {
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
            <div class="grid grid-cols-6 text-sm">
                <div class="divide-y divide-gray-400 ">
                    <div class="p-4 bg-gray-200 h-10" />
                    <div class="p-4 bg-gray-200 h-12 truncate" v-for="header in headers">
                        {{ makeTitle(header.name) }}
                    </div>
                </div>

                <div class="divide-x divide-y divide-gray-400" v-for="item in items">
                    <div class="p-4 bg-gray-200 h-10 text-center" />
                    <div v-for="cell in item.data" class="h-12 flex justify-center">
                        <ImageCard v-if="cell.path" :source="`teams/${cell.path}`"/>
                        <textarea v-else rows="1"
                                  v-model="cell.value"
                                  class="resize-none bg-inherit truncate w-full border-0 py-1.5 text-gray-900 shadow-sm ring-0 ring-gray-400 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-tf-blue-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>
            </div>
        </div>

        <Pagination :links="links"/>
    </div>

</template>
