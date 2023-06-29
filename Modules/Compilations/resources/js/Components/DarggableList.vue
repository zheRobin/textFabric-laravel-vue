<template>
    <div class="max-w-7xl mx-auto my-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="mx-auto px-6 py-6">
                <div class="grid grid-cols-12">
                    <div class="col-start-1 col-end-6 border-x border-y border-black"
                         @drop="onDrop($event, 1)"
                         @dragenter="onDragEnter($event)"
                         @dragover.prevent
                         @dragenter.prevent>
                        <div class="m-2">{{$t('Available presets')}}</div>
                        <div class="w-3/4 mx-auto mt-5 mb-10">
                            <div class="text-center py-2 border-x border-y border-black mt-2 rounded"
                                 v-for="item in items" :key="item.id"
                                 @dragstart="onDragStart($event, item, 1)"
                                 @drop="onDropOurColumn($event, item, 1)"
                                 draggable="true">
                                {{ item.name }}
                            </div>
                        </div>
                    </div>
                    <div class="col-start-7  border-x border-y border-black col-end-13"
                         @drop="onDrop($event, 2)"
                         @dragenter="onDragEnter($event)"
                         @dragover.prevent
                         @dragenter.preven>
                        <div class="flex justify-between">
                            <div class="m-2">{{$t('complication')}}</div>
                            <div class="flex m-4">
                                <button  @click="nextPrevElements('prev')">
                                    <div class="rounded-full p-2" :style="idItems === 0 ? 'border: 2px solid #F3F4F6' : 'border: 2px solid #6674F5'">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-6" :fill="idItems === 0 ? '#F3F4F6' : '#6674F5'" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowBackIcon">
                                            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                                        </svg>
                                    </div>
                                </button>
                                <button @click="nextPrevElements('next')">
                                    <div class="rounded-full p-2 ml-2" :style="idItems === lastElementNumber ? 'border: 2px solid #F3F4F6' : 'border: 2px solid #6674F5'">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-6"  :fill="idItems === lastElementNumber ? '#F3F4F6' : '#6674F5'" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowForwardIcon">
                                            <path d="m12 4-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"></path>
                                        </svg>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div>
                            <div class="w-3/4 mx-auto mt-5 mb-10">
                                <div class="py-2 border-x border-y border-black mt-2 rounded"
                                     v-for="item in itemsRight" :key="item.id"
                                     :id="item.title"
                                     @dragstart="onDragStart($event, item, 2)"
                                     @drop="onDropOurColumn($event, item, 2)"
                                     draggable="true">
                                    <div class="text-sm flex  justify-between">
                                        <div style="color: #525151" class="ml-2">{{ item.name }}</div>
                                        <button @click="refreshApi(item)">
                                            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-7 mr-2" fill="#6674F5" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="CachedIcon">
                                                <path d="m19 8-4 4h3c0 3.31-2.69 6-6 6-1.01 0-1.97-.25-2.8-.7l-1.46 1.46C8.97 19.54 10.43 20 12 20c4.42 0 8-3.58 8-8h3l-4-4zM6 12c0-3.31 2.69-6 6-6 1.01 0 1.97.25 2.8.7l1.46-1.46C15.03 4.46 13.57 4 12 4c-4.42 0-8 3.58-8 8H1l4 4 4-4H6z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="mt-4 grid grid-cols-8">
                                        <div class="col-start-1 col-end-1 my-auto ml-4">
                                            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-7 " focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ReorderIcon">
                                                <path d="M3 15h18v-2H3v2zm0 4h18v-2H3v2zm0-8h18V9H3v2zm0-6v2h18V5H3z"></path>
                                            </svg>
                                        </div>
                                        <span class="ml-5 col-start-2 col-end-9" v-if="item.value">
                                            {{item.value}}
                                        </span>
                                        <span v-else>
                                            <div role="status">
                                                <svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                                </svg>
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </span>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {ref} from 'vue';
import axios from "axios";
import {itemCompletion} from "Modules/OpenAI/resources/js/axios";
import Loading from "Jetstream/Components/Loading.vue";
export default {
    props: {
        presets: Array,
        previewItem: Object,
    },
    setup({presets, previewItem}) {
        const idItems = ref(0);
        const activeItem = ref(previewItem[idItems.value]);

        const generateAPI = async (dataID, activeItemID) => {
            const result = await itemCompletion(dataID, activeItemID);
            return result.data;
        }

        const items = ref([...presets]);

        const itemsRight = ref([]);
        const nextPrevElements = (item) => {
            if(itemsRight.value.length !== 0 && item === 'next' && idItems.value < previewItem.length){
                idItems.value += 1;
                itemsRight.value.map(async (item) => {
                    item.value = '';
                    item.value = await generateAPI(item.id, activeItem.value.id);
                })
            }else if(itemsRight.value.length !== 0 && item === 'prev' && idItems.value > 0){
                idItems.value -= 1;
                itemsRight.value.map(async (item) => {
                    item.value = '';
                    item.value = await generateAPI(item.id, activeItem.value.id);
                })
            }
        }
        const refreshApi = async (data) => {
            data.value = '';
            data.value = await generateAPI(data.id, activeItem.value.id);
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
                        generateAPI(item.id, activeItem.value.id).then((res) => {
                            item.value = res;
                        })
                        deleteItemById(items.value, itemId);
                    }
                })
            }
        }

        function onDropOurColumn (e, arr, column){
            const itemId = parseInt(e.dataTransfer.getData('itemId'))
            const dragStart = parseInt(e.dataTransfer.getData('dragStartColumn'))

            if(dragStart === column){
                if(column === 1){
                    const prev = items.value.findIndex(item => item.id === itemId);
                    const next = items.value.findIndex(item => item.id === arr.id);

                    const temp = items.value[prev];
                    items.value[prev] = items.value[next];
                    items.value[next] = temp;
                }else if(column === 2){
                    const prev = itemsRight.value.findIndex(item => item.id === itemId);
                    const next = itemsRight.value.findIndex(item => item.id === arr.id);

                    const temp = itemsRight.value[prev];
                    itemsRight.value[prev] = itemsRight.value[next];
                    itemsRight.value[next] = temp;
                }
            }
        }

        return {
            items,
            itemsRight,
            onDragStart,
            onDrop,
            onDragEnter,
            onDropOurColumn,
            refreshApi,
            Loading,
            nextPrevElements,
            idItems,
            lastElementNumber: previewItem.length
        };
    }
}
</script>
