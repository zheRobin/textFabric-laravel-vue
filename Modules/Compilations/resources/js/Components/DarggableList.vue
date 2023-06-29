<template>
    <div class="max-w-7xl mx-auto my-auto py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-12">
            <div class="col-start-1 col-end-6 border-x border-y border-black"
                 @drop="onDrop($event, 1)"
                 @dragenter="onDragEnter($event)"
                 @dragover.prevent
                 @dragenter.prevent>
                <div>{{$t('Available presets')}}</div>
                <div class="w-3/4 mx-auto mt-5 mb-10">
                    <div class="text-center py-2 border-x border-y border-black mt-2 rounded"
                         v-for="item in items" :key="item.id"
                         @dragstart="onDragStart($event, item, 1)"
                         @drop="onDropOurColumn($event, item, 1)"
                         draggable="true">
                        {{ item.title }}
                    </div>
                </div>
            </div>
            <div class="col-start-7  border-x border-y border-black col-end-13"
                 @drop="onDrop($event, 2)"
                 @dragenter="onDragEnter($event)"
                 @dragover.prevent
                 @dragenter.preven>
                <div class="flex justify-between">
                    <div>{{$t('complication')}}</div>
                    <div class="flex m-4">
                        <div class="rounded-full p-2" style="border: 2px solid #6674F5;">
                            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-6" fill="#6674F5" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowBackIcon">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                            </svg>
                        </div>
                        <div class="rounded-full p-2 ml-2" style="border: 2px solid #6674F5;">
                            <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-6" fill="#6674F5" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowForwardIcon">
                                <path d="m12 4-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"></path>
                            </svg>
                        </div>
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
                                <div>{{ item.title }}</div>
                                <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-7 mr-2" fill="#6674F5" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="CachedIcon">
                                    <path d="m19 8-4 4h3c0 3.31-2.69 6-6 6-1.01 0-1.97-.25-2.8-.7l-1.46 1.46C8.97 19.54 10.43 20 12 20c4.42 0 8-3.58 8-8h3l-4-4zM6 12c0-3.31 2.69-6 6-6 1.01 0 1.97.25 2.8.7l1.46-1.46C15.03 4.46 13.57 4 12 4c-4.42 0-8 3.58-8 8H1l4 4 4-4H6z"></path>
                                </svg>
                            </div>
                            <div class="mt-4 grid grid-cols-8">
                                <div class="col-start-1 col-end-1 my-auto ml-4">
                                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-7 " focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ReorderIcon">
                                        <path d="M3 15h18v-2H3v2zm0 4h18v-2H3v2zm0-8h18V9H3v2zm0-6v2h18V5H3z"></path>
                                    </svg>
                                </div>
                                <span class="ml-5 col-start-2 col-end-9">
                                {{item.value}}
                            </span>
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

export default {
    setup() {
        const items = ref([
            {id: 1, title: 'text1', value: 'Text text text textet texte', position: 1},
            {id: 2, title: 'text2', value: 'Text text text textet texte2', position: 2},
            {id: 4, title: 'text4', value: 'Text text text textet texte4', position: 3},
        ]);

        const itemsRight = ref([
            {id: 5, title: 'text1', value: 'Text text text textet texte', position: 1},
            {id: 6, title: 'text2', value: 'Text text text textet texte2', position: 2},
            {id: 7, title: 'text3', value: 'Text text text textet texte3', position: 3},
            {id: 8, title: 'text4', value: 'Text text text textet texte4', position: 4},
        ]);

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
            console.log(e.target);
            if(id === 1){
                itemsRight.value.map(item => {
                    if(item.id === itemId){
                        console.log(itemId)
                        items.value.push(item);
                        console.log(items.value)

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
            onDropOurColumn
        };
    }
}
</script>
