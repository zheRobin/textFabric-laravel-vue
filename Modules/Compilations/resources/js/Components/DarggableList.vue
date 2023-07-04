<template>
    <div class="col-start-1 col-end-6 mt-5 bg-gray-50 rounded p-4"
         @drop="onDrop($event, 1)"
         @dragenter="onDragEnter($event)"
         @dragover.prevent
         @dragenter.prevent>
        <div class="text-base font-semibold leading-7 text-gray-900">{{$t('Available presets')}}</div>
        <div class="w-3/4 mx-auto mt-5 mb-10">
            <div class="text-center py-2 border-x border-y bg-white mt-2 rounded text-sm font-medium text-gray-900 truncate"
                 v-for="item in items" :key="item.id"
                 @dragstart="onDragStart($event, item, 1)"
                 @drop="onDropOurColumn($event, item, 1)"
                 draggable="true">
                {{ item.name }}
            </div>
        </div>
    </div>
    <div class="col-start-7 col-end-13 mt-6 bg-gray-50 rounded p-4"
         @drop="onDrop($event, 2)"
         @dragenter="onDragEnter($event)"
         @dragover.prevent
         @dragenter.preven>
        <div class="flex justify-between">
            <div class="text-base font-semibold leading-7 text-gray-900">{{$t('Compilations')}}</div>
            <div class="flex m-4">
                <button  @click="nextPrevElements('prev')">
                    <div class="rounded-full p-2" :style="idItems === 0 ? 'border: 2px solid #F3F4F6' : 'border: 2px solid #6674F5'">
                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-6" :fill="idItems === 0 ? '#F3F4F6' : '#6674F5'" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowBackIcon">
                            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                        </svg>
                    </div>
                </button>
                <button @click="nextPrevElements('next')">
                    <div class="rounded-full p-2 ml-2" :style="idItems === lastElementNumber - 1 ? 'border: 2px solid #F3F4F6' : 'border: 2px solid #6674F5'">
                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-6"  :fill="idItems === lastElementNumber - 1 ? '#F3F4F6' : '#6674F5'" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowForwardIcon">
                            <path d="m12 4-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </div>
        <div>
            <div class="w-3/4 mx-auto mt-5 mb-10">
                <div v-if="loading === true">Loading</div>
                <div v-else class="py-2 border-x border-y bg-white mt-2 rounded text-sm font-medium text-gray-900"
                     v-for="item in itemsRight" :key="item.id"
                     :id="item.title"
                     @dragstart="onDragStart($event, item, 2)"
                     @drop="onDropOurColumn($event, item, 2)"
                     draggable="true">
                    <TextGenerate :key="item.id" :id="item.id" :activeItem="activeItem" :item="item"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import TextGenerate from "./TextGenerate.vue";
const emit = defineEmits(['idItemsPage']);
const props = defineProps({
        presets: Array,
        previewItem: Object,
        compilation: Object,
});
const {presets, previewItem, compilation} = props;
const loading = ref(false);
const idItems = ref(0);
const activeItem = ref(previewItem[idItems.value]);
const lastElementNumber = previewItem.length;

const activePresets = JSON.parse(compilation.preset_ids);

const existingPresets = presets.filter(preset => activePresets.includes(preset.id));
const nonExistingPresets = presets.filter(preset => !activePresets.includes(preset.id));

const sortedPresets = existingPresets.sort((a, b) => {
    const aIndex = activePresets.indexOf(a.id);
    const bIndex = activePresets.indexOf(b.id);
    return aIndex - bIndex;
});


const itemsRight = ref(sortedPresets);
const items = ref(nonExistingPresets);
const generatedText = ref();

const nextPrevElements = (item) => {
    loading.value = true;
    setTimeout(() => {
        if(itemsRight.value.length !== 0 && item === 'next' && idItems.value < previewItem.length - 1){
            idItems.value += 1;
            activeItem.value = previewItem[idItems.value];
            loading.value = false;
        }else if(itemsRight.value.length !== 0 && item === 'prev' && idItems.value > 0){
            idItems.value -= 1;
            activeItem.value = previewItem[idItems.value];
            loading.value = false;
        }
        loading.value = false;
    }, 100)

}
function onDragStart(e, item, start) {
    e.dataTransfer.dropEffect = 'move'
    e.dataTransfer.effectAllowed = 'move'
    e.dataTransfer.setData('dragStartColumn',start.toString())
    e.dataTransfer.setData('itemId', item.id.toString())
}
function deleteItemById(array, id) {
    const index = array.findIndex(item => item.id === id);

    if (index !== -1) {
        array.splice(index, 1);
    }
}
function onDragEnter(e) {
}
function onDrop(e, id) {
    const itemId = parseInt(e.dataTransfer.getData('itemId'))
    if(id === 1){
        itemsRight.value.map(item => {
            if(item.id === itemId){
                items.value.push(item);

                deleteItemById(itemsRight.value, itemId);
            }
        })
    }else{
        items.value.map(item => {
            if(item.id === itemId){
                itemsRight.value.push(item);
                deleteItemById(items.value, itemId);
            }
        })
    }
}

function onDropOurColumn (e, arr, column) {
    const itemId = parseInt(e.dataTransfer.getData('itemId'))
    const dragStart = parseInt(e.dataTransfer.getData('dragStartColumn'))

    if (dragStart === column) {
        if (column === 1) {
            const prev = items.value.findIndex(item => item.id === itemId);
            const next = items.value.findIndex(item => item.id === arr.id);

            const temp = items.value[prev];
            items.value[prev] = items.value[next];
            items.value[next] = temp;
        } else if (column === 2) {
            const prev = itemsRight.value.findIndex(item => item.id === itemId);
            const next = itemsRight.value.findIndex(item => item.id === arr.id);

            const temp = itemsRight.value[prev];
            itemsRight.value[prev] = itemsRight.value[next];
            itemsRight.value[next] = temp;
        }
    }
}
</script>
