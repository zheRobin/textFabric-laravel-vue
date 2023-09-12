<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import {onUnmounted, ref, onMounted, computed} from 'vue';
import { notify } from "notiwind";
import { useForm } from "@inertiajs/vue3";
import TextGenerate from "./TextGenerate.vue";
const emit = defineEmits(['idItemsPage']);

const props = defineProps({
    presets: Array,
    previewItem: Object,
    previewItemLength: Number,
    compilation: Object,
    languages: Array,
    canEdit: Boolean,
    title: Object
});

const windowWidth = ref(window.innerWidth);

onMounted(() => {
    window.addEventListener('resize', () => {
        windowWidth.value = window.innerWidth;
    });
});

const isMobile = computed(() => {
    return windowWidth.value < 563;
});

const isTablet = computed(() => {
    return windowWidth.value >= 563 && windowWidth.value < 1024;
});

const truncatedTitleHeader = computed(() => {
    if (isMobile.value) {
        return titleHeader.value.length > 10 ? titleHeader.value.slice(0, 3) + '...' : titleHeader.value;
    } else if (isTablet.value) {
        return titleHeader.value.length > 15 ? titleHeader.value.slice(0, 20) + '...' : titleHeader.value;
    } else {
        return  titleHeader.value;
    }
});


const {presets, previewItem, compilation, previewItemLength} = props;
const loading = ref(false);
const idItems = ref(0);
const activeItem = ref(previewItem);

const lastElementNumber = previewItemLength;

const form = useForm({
    id: props.compilation.id,
    name: props.compilation.name,
    owner: props.compilation.owner,
    preset_ids: []
})

const activePresets = compilation.preset_ids;

const existingPresets = presets.filter(preset => activePresets.includes(preset.id));
const nonExistingPresets = presets.filter(preset => !activePresets.includes(preset.id));

const sortedPresets = existingPresets.sort((a, b) => {
    const aIndex = activePresets.indexOf(a.id);
    const bIndex = activePresets.indexOf(b.id);
    return aIndex - bIndex;
});
const titleHeader = ref(null);
const findElementHeader = () => {
    let title = '';
    if(activeItem.value.data !== undefined){
        title = activeItem.value.data.find(item => item.header === props.title.name);
        if(title){
            titleHeader.value = title.value;
        }
    }
    console.log(title);
    console.log(titleHeader.value);
}
const itemsRight = ref(sortedPresets);
const items = ref(nonExistingPresets);

let interval;
const nextPrevElements = (item) => {
    console.log('next');
    if (itemsRight.value.length !== 0 && item === 'next' && idItems.value < previewItemLength - 1 || itemsRight.value.length !== 0 && item === 'prev' && idItems.value > 0) {

        loading.value = true;
        interval = setTimeout(() => {
            if (itemsRight.value.length !== 0 && item === 'next' && idItems.value < previewItemLength - 1) {
                console.log(idItems.value);
                idItems.value += 1;
                console.log(idItems.value);
                axios.get(`/compilations/get-item/${idItems.value}`).then((res)=>{
                    activeItem.value = res.data.item;
                    loading.value = false;
                    findElementHeader();

                })
            } else if (itemsRight.value.length !== 0 && item === 'prev' && idItems.value > 0) {
                idItems.value -= 1;
                axios.get(`/compilations/get-item/${idItems.value}`).then((res)=>{
                    activeItem.value = res.data.item;
                    loading.value = false;
                    findElementHeader();

                })
            }
        }, 100)
    }
}
findElementHeader();

onUnmounted(() => {
    clearInterval(interval);

})
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
const updatePreset = (right) => {
    console.log(right)
    const newPresetIds = [];
    if(right){
        right.map(item => newPresetIds.push(item.id))
        form.preset_ids = newPresetIds;
    }
    form.patch(route('compilations.update'), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: () => {
        },
        onError: (error) => {
            notify({
                group: "error",
                title: "Error",
                text: error[Object.keys(error)[0]] ?? "Something wrong happens."
            }, 4000) // 4s
        }
    })
}
function onDrop(e, id) {
    const itemId = parseInt(e.dataTransfer.getData('itemId'))
    if(id === 1){
        itemsRight.value.map(item => {
            if(item.id === itemId){
                items.value.push(item);
                deleteItemById(itemsRight.value, itemId);
                emit('itemRight', itemsRight.value)
                updatePreset(itemsRight.value, itemId)
            }
        })
    }else{
        items.value.map(item => {
            if(item.id === itemId){
                itemsRight.value.push(item);
                deleteItemById(items.value, itemId);
                emit('itemRight', itemsRight.value)
                updatePreset(itemsRight.value, itemId)
            }
        })
    }
}
const updateGenerate = ref(false);
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
            updatePreset(itemsRight.value);
            emit('itemRight', itemsRight.value)
        }
    }
}

</script>

<template>
    <div class="w-2/5 mt-5 bg-gray-50 rounded p-4"
         @drop="onDrop($event, 1)"
         @dragenter="onDragEnter($event)"
         @dragover.prevent
         @dragenter.prevent>
        <div class="text-base font-semibold leading-7 text-gray-900">{{$t('Available presets')}}</div>
        <div class="mx-auto mt-5  mb-10">
            <div class="text-center py-2 border-x border-y bg-white mt-2 rounded text-sm font-medium text-gray-900 truncate"
                 v-for="item in items" :key="item.id"
                 @dragstart="onDragStart($event, item, 1)"
                 @drop="onDropOurColumn($event, item, 1)"
                 :draggable="canEdit">
                {{ item.name }}
            </div>
        </div>
    </div>
    <div class="w-3/5 mt-5 bg-gray-50 rounded pt-4 pl-4 pr-4"
         @drop="onDrop($event, 2)"
         @dragenter="onDragEnter($event)"
         @dragover.prevent
         @dragenter.preven>
        <div class="flex md:flex-row flex-col justify-between">
            <div class="text-base font-semibold leading-7 text-gray-900">{{$t('Compilation')}}</div>
            <div class="flex md:justify-normal justify-end" v-if="itemsRight.length !== 0">
                <div v-if="titleHeader" class="text-sm font-medium text-gray-900 truncate mt-1.5">
                    {{ truncatedTitleHeader }}
                    <span class="ml-2 mr-2">-</span>
                    <span class="ml-2 mr-4">{{ `#${idItems + 1}` }}</span>
                </div>
<!--                <div v-if="titleHeader" class="text-sm font-medium text-gray-900 truncate mt-1.5">-->
<!--                    {{titleHeader.length > 20 ? titleHeader.slice(0, 25) + '...' : titleHeader.length}}-->
<!--                    <span class="ml-2 mr-2">-</span>-->
<!--                    <span class="ml-2 mr-4">-->
<!--                        {{ `#${idItems + 1}` }}-->
<!--                    </span>-->
<!--                </div>-->
                <div v-else>
                    <span class="ml-2 mr-4">
                        {{ `#${idItems + 1}` }}
                    </span>
                </div>
                <div>
                   <span class="isolate inline-flex rounded-md shadow-sm">
                    <button type="button" @click="nextPrevElements('prev')" :class="idItems === 0 ? '' : 'hover:bg-gray-50 focus:z-10'" class="relative inline-flex items-center rounded-l-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                      <span class="sr-only">Previous</span>
                      <ChevronLeftIcon class="h-5 w-5 sm:h-4 sm:w-4" :class="idItems === 0 ? '' : 'text-black'" aria-hidden="true" />
                    </button>
                    <button type="button" @click="nextPrevElements('next')" :class="idItems === lastElementNumber - 1 ? '' : 'hover:bg-gray-50 focus:z-10'" class="relative -ml-px inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                      <span class="sr-only">Next</span>
                      <ChevronRightIcon class="h-5 w-5 sm:h-4 sm:w-4" :class="idItems === lastElementNumber - 1 ? '' : 'text-black'" aria-hidden="true"/>
                    </button>
                  </span>
                </div>
            </div>
        </div>
        <div>
            <div class=" mx-auto mt-5 mb-10" v-if="itemsRight.length !== 0">
                <div v-if="loading === true">{{$t('Loading')}}</div>
                <div v-else class="py-2 border-x border-y bg-white mt-2 rounded text-sm font-medium text-gray-900"
                     v-for="item in itemsRight" :key="item.id"
                     :id="item.title"
                     @dragstart="onDragStart($event, item, 2)"
                     @drop="onDropOurColumn($event, item, 2)"
                     :draggable="canEdit">
                    <TextGenerate :key="item.id" :languages="languages" :id="item.id" :activeItem="activeItem" :item="item"/>
                </div>
            </div>
            <div class=" mx-auto mt-5 mb-10" v-else>
                <div class="text-base font-semibold leading-7 text-gray-500 text-center mt-10">{{$t('Drag, drop rearrange available presets here')}}</div>
            </div>
        </div>
    </div>
</template>
