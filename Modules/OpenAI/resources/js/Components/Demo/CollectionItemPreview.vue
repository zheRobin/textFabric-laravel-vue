<script setup>
import {computed, ref} from "vue";
import {ChevronLeftIcon, ChevronRightIcon} from '@heroicons/vue/20/solid';
import {getItems} from "Modules/Imports/resources/js/collection";
import {getActiveLanguage} from "laravel-vue-i18n";

const emit = defineEmits(['itemChanged']);

const items = ref([]);

const setupItems = () => {
    items.value = getItems(getActiveLanguage());
}
setupItems();

const currentPage = ref(0);

const lastPage = ref(items.value.length);

const currentItem = ref([]);

const itemData = computed(() => {
    return currentItem.value
        ? currentItem.value
        : [];
});

const nextItem = () => {
    if ((currentPage.value + 1) <= lastPage.value ) {
        currentPage.value = currentPage.value + 1;
        changeItem();
    }
}

const nextButtonDisabled = computed(() => {
    return currentPage.value + 1 === lastPage.value;
});

const previousItem = () => {
    if ((currentPage.value) >= 1) {
        currentPage.value = currentPage.value - 1;
        changeItem();
    }
}

const previousButtonDisabled = computed(() => {
    return currentPage.value === 0;
})

const changeItem = () => {
    currentItem.value = items.value[currentPage.value];

    emit('itemChanged', currentItem.value);
}
changeItem();

</script>

<template>
    <div class="bg-gray-50 rounded">
        <div class="px-6 py-3 items-center">
            <div class="flex justify-end items-center">
                <div class="flex items-center">
                    <div class="text-sm font-medium text-gray-900 truncate">
                        {{ items[currentPage][1].value }}
                        <span class="ml-2">-</span>
                        <span class="ml-2 mr-4">
                            {{ `#${currentPage + 1}` }}
                        </span>
                    </div>
                    <span class="isolate inline-flex rounded-md shadow-sm">
                        <button type="button" @click="previousItem" :disabled="previousButtonDisabled" :class="!previousButtonDisabled ? 'hover:bg-gray-50 focus:z-10' : ''" class="relative inline-flex items-center rounded-l-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                            <span class="sr-only"> {{ $t('Previous') }} </span>
                            <ChevronLeftIcon class="h-4 w-4" :class="!previousButtonDisabled ? 'text-black' : ''" aria-hidden="true" />
                        </button>
                        <button type="button" @click="nextItem" :disabled="nextButtonDisabled" :class="!nextButtonDisabled ? 'hover:bg-gray-50 focus:z-10' : ''" class="relative -ml-px inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                            <span class="sr-only"> {{ $t('Next') }} </span>
                            <ChevronRightIcon class="h-4 w-4" :class="!nextButtonDisabled ? 'text-black' : ''" aria-hidden="true"/>
                        </button>
                    </span>
                </div>
            </div>
        </div>

        <div  class="min-h-56">
            <div v-for="(item, index) in itemData" :key="`item-${index}`" class="border-t border-gray-200">
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
