<script setup>
import {ref, watch} from "vue";
import {ArrowLeftIcon, ArrowRightIcon} from '@heroicons/vue/20/solid';
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {iterateCollectionItems} from "Modules/OpenAI/resources/js/axios";
import Dropdown from "Jetstream/Components/Dropdown.vue";
import DropdownLink from "Jetstream/Components/DropdownLink.vue";

const props = defineProps({
    languages: Array,
    languageId: Number,
});

const emit = defineEmits(['itemChanged', 'update:inputLanguage']);

const findLanguage = (languageId) => {
    if (languageId) {
        currentLanguage.value = props.languages.find((language) => language.id === languageId)
    } else {
        currentLanguage.value = null;
    }
}

const currentLanguage = ref(null);
findLanguage(props.languageId);

watch(() => props.languageId, (langId) => {
    findLanguage(langId);
});

const currentItem = ref([]);

const currentPage = ref(1);
const lastPage = ref(null);

const collectionItem = (page) => {
    iterateCollectionItems(page)
        .then((response) => {
            currentItem.value = response.data.data[0] ?? null;
            currentPage.value = response.data.current_page;
            lastPage.value = response.data.last_page;

            emit('itemChanged', currentItem.value);
    });
}

collectionItem(currentPage);

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

const changeLanguage = (language) => {
    currentLanguage.value = language;
    emit('update:inputLanguage', language);
}
</script>

<template>
    <div class="overflow-hidden bg-gray-50 rounded">
        <div class="px-6 py-3 items-center">
            <div class="flex justify-between items-center">
                <!-- TODO: move into separate component -->
                <Dropdown align="left" width="30" height="48">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button type="button" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                {{ currentLanguage ? currentLanguage.name : 'Language' }}
                                <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>
                        </span>
                    </template>
                    <template #content>
                        <div class="w-30">
                            <DropdownLink @click="changeLanguage(language)" v-for="language in props.languages" as="button">
                                {{ language.name }}
                            </DropdownLink>
                        </div>
                    </template>
                </Dropdown>

                <PrimaryButton v-if="currentPage > 1" @click="previousItem">
                    <ArrowLeftIcon class="w-4" /> {{ $t("Previous") }}
                </PrimaryButton>

                <PrimaryButton v-if="currentPage < lastPage" @click="nextPage">
                    {{$t('Next')}} <ArrowRightIcon class="w-4" />
                </PrimaryButton>
            </div>
        </div>

        <div class="min-h-44 overflow-scroll">
            <div class="border-t border-gray-200">
                <dl class="divide-y divide-gray-100 cursor-default">
                    <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-900 truncate" :title="$t('ID')"> {{ `#${$t('ID')}` }} </dt>
                        <dd class="mt-1 text-right text-sm leading-6 text-gray-700 sm:mt-0">{{ currentPage }}</dd>
                    </div>
                </dl>
            </div>
            <div v-for="item in currentItem.data" class="border-t border-gray-200">
                <dl class="divide-y divide-gray-100 cursor-default">
                    <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-900 truncate" :title="item.header">{{ item.header }}</dt>
                        <dd class="mt-1 text-right text-sm leading-6 text-gray-700 sm:mt-0">{{ item.value ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</template>
