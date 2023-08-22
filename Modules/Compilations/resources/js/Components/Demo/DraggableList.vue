<script setup>
import {computed, ref} from 'vue';
import TextGenerate from "Modules/Compilations/resources/js/Components/Demo/TextGenerate.vue";
import {getItems} from "Modules/Imports/resources/js/collection";
import {getActiveLanguage} from "laravel-vue-i18n";
import {getPresets} from "Modules/OpenAI/resources/js/presets";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/vue/20/solid";

const items = ref([]);

const setupItems = () => {
    items.value = getItems(getActiveLanguage());
}
setupItems();

const presets = getPresets(getActiveLanguage());



const currentPage = ref(0);

const lastPage = ref(items.value.length);

const currentItem = ref([]);

const changeItem = () => {
    currentItem.value = items.value[currentPage.value];
}
changeItem();

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
});

const props = defineProps({
    canEdit: Boolean,
    demo: Boolean
});

const availablePresets = ref(props.demo ? null : presets);
const presetsToComplete = ref(props.demo ? presets : []);

function deleteItemById(array, id) {
    const index = array.findIndex(item => item.slug === id);

    if (index !== -1) {
        array.splice(index, 1);
    }
}

function onDragStart(e, preset, start) {
    e.dataTransfer.dropEffect = 'move';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('dragStartColumn', start.toString());
    e.dataTransfer.setData('itemId', preset.slug);
}

function onDrop(e, id) {
    const itemId = e.dataTransfer.getData('itemId');

    if (id === 1) {
        presetsToComplete.value.map((el) => {
            if (el.slug === itemId) {
                availablePresets.value.push(el);
                deleteItemById(presetsToComplete.value, itemId);
            }
        })
    } else {
        availablePresets.value.map((el) => {
            if (el.slug === itemId) {
                presetsToComplete.value.push(el);
                deleteItemById(availablePresets.value, itemId);
            }
        })
    }
}

function onDropOurColumn (e, arr, column) {
    const itemId = e.dataTransfer.getData('itemId');
    const dragStart = parseInt(e.dataTransfer.getData('dragStartColumn'))

    if (dragStart === column) {
        if (column === 1) {
            const prev = availablePresets.value.findIndex(item => item.slug === itemId);
            const next = availablePresets.value.findIndex(item => item.slug === arr.slug);

            const temp = availablePresets.value[prev];
            availablePresets.value[prev] = availablePresets.value[next];
            availablePresets.value[next] = temp;
        } else if (column === 2) {
            const prev = presetsToComplete.value.findIndex(item => item.slug === itemId);
            const next = presetsToComplete.value.findIndex(item => item.slug === arr.slug);

            const temp = presetsToComplete.value[prev];
            presetsToComplete.value[prev] = presetsToComplete.value[next];
            presetsToComplete.value[next] = temp;
        }
    }
}
</script>

<template>
    <div class="w-2/5 mt-5 bg-gray-50 rounded p-4"
         @drop="onDrop($event, 1)"
         @dragover.prevent
         @dragenter.prevent
    >
        <div class="text-base font-semibold leading-7 text-gray-900">{{$t('Available presets')}}</div>
        <div class="mx-auto mt-5  mb-10">
            <div class="text-center py-2 border-x border-y bg-white mt-2 rounded text-sm font-medium text-gray-900 truncate"
                 v-for="preset in availablePresets" :key="preset.slug"
                 @dragstart="onDragStart($event, preset, 1)"
                 @drop="onDropOurColumn($event, preset, 1)"
                 :draggable="canEdit">
                {{ preset.name }}
            </div>
        </div>
    </div>
    <div class="w-3/5 mt-5 bg-gray-50 rounded pt-4 pl-4 pr-4"
         @drop="onDrop($event, 2)"
         @dragover.prevent
         @dragenter.prevent
    >
        <div class="flex justify-between">
            <div class="text-base font-semibold leading-7 text-gray-900">{{$t('Compilation')}}</div>
            <div class="flex" v-if="presetsToComplete.length">
                <div class="mr-4 mt-1">
                    {{ `#${currentPage + 1}` }}
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
        <div>
            <div class=" mx-auto mt-5 mb-10" v-if="presetsToComplete.length">
                <div class="py-2 border-x border-y bg-white mt-2 rounded text-sm font-medium text-gray-900"
                     v-for="item in presetsToComplete" :key="item.slug"
                     :id="item.slug"
                     @dragstart="onDragStart($event, item, 2)"
                     @drop="onDropOurColumn($event, item, 2)"
                     :draggable="canEdit">
                    <TextGenerate :key="`${item.slug}-generate`" :id="`${item.slug}-generate`" :activeItem="currentItem" :preset="item"/>
                </div>
            </div>
            <div v-else class="mx-auto mt-5 mb-10">
                <div class="text-base font-semibold leading-7 text-gray-500 text-center mt-10">Drag, drop rearrange available presets here</div>
            </div>
        </div>
    </div>
</template>
