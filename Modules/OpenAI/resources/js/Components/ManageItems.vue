<script setup>
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {notify} from "notiwind";

const props = defineProps({
    presets: Array,
});

const form = useForm({
    presets: props.presets[0].name,
    id: null,
})

const inputActive = ref(true);
const idItem = ref(null);
const saveName = () => {
    if(idItem.value && idItem.value){
        inputActive.value = true;
        form.id = idItem;

        form.post(route('openai.update'))
        form.id = null;
    }
}

const deletePresets = (itemName) => {
    idItem.value = props.presets.find(item => item.name === itemName).id;
    form.id = idItem;

    form.post(route('openai.delete'));

    form.presets = props.presets[0].name;
    form.id = null;
}

const changeName = (itemName) => {
    idItem.value = props.presets.find(item => item.name === itemName).id;
    inputActive.value = false;
}
</script>

<template>
    <div class="flex items-center ml-auto">
        <div>
            <span>{{ $t('compilations') }}:</span>
        </div>
            <div class="flex ml-2">
                <div v-if="inputActive">
                    <select v-model="form.presets" id="location" name="location" class="block w-full rounded-md border-0 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option v-for="item in props.presets" :key="item.id">{{item.name}}</option>
                    </select>
                </div>
                <div v-else>
                    <input v-model="form.presets" type="email" name="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="you@example.com" />
                </div>
                <div class="flex ml-2">
                    <button type="button" @click="() => changeName(form.presets)" class="inline-flex items-center gap-x-1.5  rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-4" focusable="false" fill="#6674F5" aria-hidden="true" viewBox="0 0 24 24" data-testid="EditIcon">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                        </svg>
                        {{$t('edit_name')}}
                    </button>
                </div>
                <div class="flex ml-2">
                    <button type="button" @click="saveName" class="inline-flex items-center gap-x-1.5  rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-4" focusable="false" fill="#6674F5" aria-hidden="true" viewBox="0 0 24 24" data-testid="EditIcon">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                        </svg>
                        {{$t('save')}}
                    </button>
                </div>
<!--                <div class="flex ml-2">-->
<!--                    <button type="button" class="inline-flex items-center gap-x-1.5  rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">-->
<!--                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-4" focusable="false" fill="#6674F5" aria-hidden="true" viewBox="0 0 24 24" data-testid="CreateNewFolderIcon">-->
<!--                            <path d="M20 6h-8l-2-2H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-1 8h-3v3h-2v-3h-3v-2h3V9h2v3h3v2z"></path>-->
<!--                        </svg>-->
<!--                        {{$t('create_new')}}-->
<!--                    </button>-->
<!--                </div>-->
                <div class="flex ml-2">
                    <button @click="() => deletePresets(form.presets)" type="button" class="inline-flex items-center gap-x-1.5  rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium MuiBox-root css-1om0hkc w-4" fill="#B71F54" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="CreateNewFolderIcon">
                            <path d="M20 6h-8l-2-2H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-1 8h-3v3h-2v-3h-3v-2h3V9h2v3h3v2z"></path>
                        </svg>
                        {{$t('delete')}}
                    </button>
                </div>
            </div>
    </div>
</template>
