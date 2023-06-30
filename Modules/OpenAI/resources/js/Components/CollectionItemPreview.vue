<script setup>
import {ArrowLeftIcon, ArrowRightIcon} from '@heroicons/vue/20/solid';
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {showCollectionItem} from "Modules/OpenAI/resources/js/axios";
import {ref} from "vue";

const props = defineProps({
    item: Object,
});

const emit = defineEmits(['itemChanged']);

const currentItem = ref([]);

const currentPage = ref(1);
const lastPage = ref(null);

const collectionItem = (page = 1) => {
    showCollectionItem(page)
        .then((response) => {
            currentItem.value = response.data.data[0] ?? null;
            currentPage.value = response.data.current_page;
            lastPage.value = response.data.last_page;

            emit('itemChanged', currentItem.value);
        });
}

collectionItem();

const nextPage = () => {
    if ((currentPage.value + 1) <= lastPage.value ) {
        collectionItem(currentPage.value + 1);
    }
}

const previousItem = () => {
    if ((currentPage.value - 1) >= 1) {
        collectionItem(currentPage.value -1);
    }
}

</script>

<template>
    <div class="overflow-hidden bg-gray-50 rounded">
        <div class="px-4 py-4 sm:px-6">
            <h3 class="text-base font-semibold leading-7 text-gray-900">
                {{ $t("Item") }} #{{currentItem.id}}
            </h3>
        </div>

        <div class="h-44 overflow-scroll">
            <div v-for="item in currentItem.data" class="border-t border-gray-200">
                <dl class="divide-y divide-gray-100 cursor-default">
                    <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-900 truncate" :title="item.header">{{ item.header }}</dt>
                        <dd class="mt-1 text-right text-sm leading-6 text-gray-700 sm:mt-0">{{ item.value ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-6 border-t px-6 py-2">
            <div class="flex justify-between">
                <div>
                    <PrimaryButton v-if="currentPage > 1" @click="previousItem">
                        <ArrowLeftIcon class="w-4" /> {{ $t("Previous") }}
                    </PrimaryButton>
                </div>

                <div>
                    <PrimaryButton v-if="currentPage < lastPage" @click="nextPage">
                        {{$t('Next')}} <ArrowRightIcon class="w-4" />
                    </PrimaryButton>
                </div>
            </div>
        </div>

    </div>
</template>
