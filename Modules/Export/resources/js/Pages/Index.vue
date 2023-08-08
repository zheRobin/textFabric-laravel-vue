<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import SelectMenuForDownload from "Modules/Export/resources/js/Components/SelectMenu.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";
import DialogModal from "Jetstream/Components/DialogModal.vue";
import ApiModal from "Jetstream/Components/ApiModal.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import CollectionDataTable from "Modules/Export/resources/js/Components/CollectionDataTable.vue";
import axios from "axios";
import {notify} from "notiwind";
import { ref, watch } from "vue"
import {useForm, usePage} from "@inertiajs/vue3";
import {options} from "Modules/Export/resources/js/optionsForDownload";
import ConfirmationModal from "Jetstream/Components/ConfirmationModal.vue";
import DangerButton from "Jetstream/Components/DangerButton.vue";
import Pagination from "Modules/Jetstream/resources/js/Components/Pagination.vue";
import EmptyCollection from "Modules/Collections/resources/js/Components/EmptyCollection.vue";
import EmptyImport from "Modules/Imports/resources/js/Components/EmptyImport.vue";
import {MinusCircleIcon, EyeIcon} from "@heroicons/vue/20/solid";
import Events from "Modules/Export/resources/js/Components/Events.vue";

const props = defineProps({
    languages: Array,
    complications: Array,
    exports: Array,
    exportCount: Number,
    active: Array
});
console.log(props.active, 'active')
const activeLanguages = ref([]);
const activeModal = ref(false);
const selectedCompilations = ref(null);
const activeGenerations = ref(null);
const loading = ref(false);
const form = useForm({
    id: null,
    compilations: null,
    languages: [],
    value: null,
})

const searchQuery = ref("");
const progress = ref(0);
const page = usePage();
const exports = ref(props.exports);

async function fetchProgress() {
    try {
        const response = await axios.get('/get-progress');
        progress.value = response.data.progress;
    } catch (error) {
        console.error('Error fetching progress:', error);
    }
}
if(props.active){
    loading.value = true;
    activeGenerations.value = {
        value: 0,
        label: props.complications.find(item => item.id === parseInt(localStorage.getItem('selectedCompilations'))).name
    }
}



const generateActive = ref(false);

let intervalFromBack;
if(localStorage.getItem('progress') >= 0 && !generateActive.value){
    progress.value = parseInt(localStorage.getItem('progress'));
    intervalFromBack = setInterval(() => {
        if(progress.value <= 99){
            if(progress.value >= 75){
                progress.value += 1;
            }else{
                progress.value += Math.ceil(Math.random() * 20);
            }
            localStorage.setItem('progress', progress.value);
        }
    }, 4000);
    if(progress.value === 99){
        window.clearInterval(intervalFromBack);
    }
}

const generate = async () => {
    generateActive.value = true;
    window.clearInterval(intervalFromBack);
    if (!loading.value) {
        progress.value = parseInt(localStorage.getItem('progress'));
        let interval = setInterval(() => {
            if(progress.value <= 99){
                if(progress.value >= 75){
                    progress.value += 1;
                }else{
                    progress.value += Math.ceil(Math.random() * 20);
                }
                localStorage.setItem('progress', progress.value);
            }
        }, 4000);
        if(progress.value === 99){
            window.clearInterval(interval);
        }
        if (selectedCompilations.value) {
            loading.value = true;
            activeGenerations.value = dataLabel.find(
                (item) => item.value === selectedCompilations.value
            );
            axios.post(route('export.generate'), {compilations: form.compilations});
        }
    }
    localStorage.setItem('progress', 0);
    progress.value = 0;
};
const changePreset = (value) => {
    localStorage.setItem('selectedCompilations', value)
    selectedCompilations.value = value;
    console.log(value);
}

const showModal = (id, value) => {
    activeViewJson.value = value;
    form.id = id;
    form.value = value;
    activeModal.value = true;
}

const style = {
    enable: 'ml-2 mt-2 inline-flex items-center gap-x-0.5 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 pointer cursor-pointer',
    disable: 'ml-2 mt-2 inline-flex items-center gap-x-0.5 rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 pointer cursor-pointer'
}

const showModalDelete = (id) => {
    form.id = id;
    confirmingExportDeletion.value = true;
}

const deleteExport = () => {
    confirmingExportDeletion.value = true;
    axios.post(route('export.delete'), {id: form.id}).then((res) => {
        activeGenerations.value = null;
        loading.value = false;
        confirmingExportDeletion.value = false;
        form.id = null;
        exports.value = res.data;

        notify({
            group: "success",
            title: "Success",
            text: "Export file deleted!"
        }, 4000)
    });
}

const translation = () => {
    console.log('translate')
    axios.post(route('export.translation'), {id: form.id, value: form.value, languages: form.languages}).then((res) => {
        activeGenerations.value = null;
        loading.value = false;
        console.log(activeModal.value);
        activeModal.value = false;
        console.log(activeModal.value);

        notify({
            group: "success",
            title: "Success",
            text: "The translation was successful"
        }, 4000)
        activeViewJson.value = exports.value.exports[0].value;
    });
}

const download = () => {
    axios.post(route('export.download'), {id: form.id, format: selectedDownloadFormat.value}, { responseType: "blob" })
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            if(selectedDownloadFormat.value === '.xml'){
                link.setAttribute('download', 'data.xml');
            }else if(selectedDownloadFormat.value === '.json'){
                link.setAttribute('download', 'data.json');
            }else if (selectedDownloadFormat.value === '.csv') { // Corrected condition for CSV
                link.setAttribute('download', 'data.csv');
            }else if (selectedDownloadFormat.value === '.xlsx') { // Corrected condition for CSV
                link.setAttribute('download', 'data.xlsx');
            }else if(selectedDownloadFormat.value === '.xls'){
                link.setAttribute('download', 'data.xls');
            }
            document.body.appendChild(link);
            link.click();
            activeDownloadModal.value = false;
        })
        .catch(error => {
            console.log(error);
        });
}

const changeDownloadFormat = (value) => {
    selectedDownloadFormat.value = value;
}

const setLanguage = (value) => {
    let foundEN = activeLanguages.value.includes(value);
    if (foundEN) {
        let indexToRemove = activeLanguages.value.indexOf(value);
        activeLanguages.value.splice(indexToRemove, 1);
        form.languages = activeLanguages.value;
    } else {
        activeLanguages.value.push(value);
        form.languages = activeLanguages.value;
    }
}
const activeViewModal = ref(false);
const closeViewModal = () => {
    form.value = null;
    activeViewJson.value = null;
    activeViewModal.value = false;
}
const countViewPages = ref (null);
const activeViewJson = ref(null);
const showViewModal = (item) => {
    form.value = null;
    form.id = item.id;
    axios.get(`/export/${item.id}`).then((res) => {
        activeViewJson.value = res.data.export;
        countViewPages.value = res.data.count;
        activeViewModal.value = true;
    });
}

const activeDownloadModal = ref(false);

const showDownloadModal = (id) => {
    form.id = id;
    activeDownloadModal.value = true;
}

const closeDownloadModal = () => {
    console.log('close')
    selectedDownloadFormat.value = null;
    activeDownloadModal.value = false;
}

const closeModal = () => {
    form.id = null;
    form.value = null;
    activeModal.value = false;
}
const dataLabel = props.complications.map(item => {
    return {
        value: item.id,
        label: item.name
    }
});
const confirmingExportDeletion = ref(false);
const selectedDownloadFormat = ref(null);

const search = (event) => {
    console.log(form);
    axios
        .post(route('export.search'), {query: searchQuery.value})
        .then((response) => {
            exports.value = response.data;
        })
        .catch((error) => {
            console.error(error);
        });
}

const generationDone = (data) => {
    loading.value = false;
    form.compilations = null;
    activeGenerations.value = null;
    searchQuery.value = '';
    search();
    progress.value = 0;
}
</script>

<template>
    <AppLayout title="Export">
        <template #header>
            <div class="flex">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $t('Export') }}
                </h2>
                <Events @update:generationDone="generationDone"/>
            </div>
        </template>
        <div class="max-w-7xl mx-auto my-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                <div class="mx-auto px-6 py-6">
                    <EmptyCollection v-if="!$page.props.auth.user.current_collection" />

                    <EmptyImport v-else-if="!hasItems" />

                    <template v-else>
                        <div class="flex border-b border-gray-200 pb-8 items-center">
                            <label class="mr-2 mt-1 font-medium dark:text-white">{{$t('Compilation')}}:</label>
                            <SelectMenu @update:modelValue="changePreset" v-model="form.compilations" :options="dataLabel" id="employees" class="w-60" />
                            <PrimaryButton v-if="!loading" class="ml-2 gap-x-1.5" @click="generate">
                                Generate
                            </PrimaryButton>
                        </div>
                        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                            <div class="border-b border-gray-200 pb-8 mb-8">
                                <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">Currently running compilations</h2>
                                <div class="flex justify-between mt-6" v-if="activeGenerations">
                                    <div class="my-3">{{activeGenerations.label}}</div>
                                    <div class="flex">
                                        <div aria-label="Loading..." role="status" class="flex items-center space-x-2">
                                            <span class="text-xs font-medium text-gray-500">Loading... {{progress}} %</span>
                                        </div>
                                        <!--                                    <div class="mt-2">-->
                                        <!--                                        <SecondaryButton class="ml-3">-->
                                        <!--                                            Cancel-->
                                        <!--                                        </SecondaryButton>-->
                                        <!--                                    </div>-->
                                    </div>
                                </div>
                                <div v-else class="mt-6 text-center">Nothing is being generated now</div>
                            </div>
                            <div class="flex justify-between">
                                <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">History of created compilations</h2>
                                <div>
                                    <div class="relative mt-2 flex items-center">
                                        <input type="text" @keyup="search" v-model="searchQuery" name="search" placeholder="Search..." id="search" class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                        <div @click="search" class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5 cursor-pointer">
                                            <kbd class="inline-flex items-center rounded border border-gray-200 px-1 font-sans text-xs text-gray-400">⌘K</kbd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="exports.data.length>0">
                                <ul role="list" class="divide-y divide-gray-100 mt-5">
                                    <li v-for="item in exports.data" class="flex justify-between gap-x-6 py-5">
                                        <div class="flex gap-x-4">
                                            <div class="min-w-0 flex-auto">
                                                <p class="text-sm font-semibold leading-6 text-gray-900">{{ item.name }}</p>
                                            </div>
                                        </div>
                                        <div class="hidden sm:flex sm:flex-col sm:items-end">
                                            <p class="text-sm leading-6 text-gray-900">
                                                <PrimaryButton @click="showViewModal(item)" class="ml-2 gap-x-1.5">
                                                    View
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                        <path d="M6 3a3 3 0 00-3 3v1.5a.75.75 0 001.5 0V6A1.5 1.5 0 016 4.5h1.5a.75.75 0 000-1.5H6zM16.5 3a.75.75 0 000 1.5H18A1.5 1.5 0 0119.5 6v1.5a.75.75 0 001.5 0V6a3 3 0 00-3-3h-1.5zM12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5zM4.5 16.5a.75.75 0 00-1.5 0V18a3 3 0 003 3h1.5a.75.75 0 000-1.5H6A1.5 1.5 0 014.5 18v-1.5zM21 16.5a.75.75 0 00-1.5 0V18a1.5 1.5 0 01-1.5 1.5h-1.5a.75.75 0 000 1.5H18a3 3 0 003-3v-1.5z" />
                                                    </svg>

                                                </PrimaryButton>
                                                <PrimaryButton @click="showModal(item.id, item.data)" class="ml-2 gap-x-1.5">
                                                    Translation
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                        <path fill-rule="evenodd" d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z" clip-rule="evenodd" />
                                                    </svg>
                                                </PrimaryButton>
                                                <PrimaryButton @click="showDownloadModal(item.id)" class="ml-2 gap-x-1.5">
                                                    Download
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4" id="download"><path fill="white" d="M21,14a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V15a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V15A1,1,0,0,0,21,14Zm-9.71,1.71a1,1,0,0,0,.33.21.94.94,0,0,0,.76,0,1,1,0,0,0,.33-.21l4-4a1,1,0,0,0-1.42-1.42L13,12.59V3a1,1,0,0,0-2,0v9.59l-2.29-2.3a1,1,0,1,0-1.42,1.42Z"></path></svg>
                                                </PrimaryButton>
                                                <DangerButton @click="showModalDelete(item.id)" class="ml-2 gap-x-1.5">
                                                    Delete
                                                    <MinusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                                </DangerButton>
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                                <Pagination :links="exports.links" />
                            </div>
                            <div v-else class="mt-6 text-center">Not found</div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <DialogModal :show="activeModal" @close="closeModal">
            <template #title>
                Translation
            </template>

            <template #content>
                <div class="mt-4">
                    <InputLabel for="title" value="Select languages" />
                    <span @click="setLanguage(key)" v-for="(item, key) in props.languages" :class="activeLanguages.includes(key) ? style.enable : style.disable">
                      {{ item }}
                      <button v-if="activeLanguages.includes(key)" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-500/20">
                        <span class="sr-only">Remove</span>
                        <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-gray-600/50 group-hover:stroke-gray-600/75">
                          <path d="M4 4l6 6m0-6l-6 6" />
                        </svg>
                        <span class="absolute -inset-1"></span>
                      </button>
                    </span>
                </div>
            </template>

            <template #footer>
                <PrimaryButton @click="translation">
                    Translate
                </PrimaryButton>
                <SecondaryButton class="ml-3" @click="closeModal">
                    Close
                </SecondaryButton>
            </template>
        </DialogModal>
        <DialogModal :show="activeDownloadModal" @close="closeDownloadModal">
            <template #title>
                Download
            </template>

            <template #content>
                <div class="mt-5">
                    <InputLabel value="Select format"></InputLabel>
                    <SelectMenuForDownload @update:modelValue="changeDownloadFormat" v-model="selectedDownloadFormat" class="mt-3"  :options="options"/>
                </div>
            </template>

            <template #footer>
                <PrimaryButton @click="download">
                    Download
                </PrimaryButton>
                <SecondaryButton class="ml-3" @click="closeDownloadModal">
                    Close
                </SecondaryButton>
            </template>
        </DialogModal>
        <ApiModal :show="activeViewModal" @close="closeViewModal">
            <template #title>
                View
            </template>

            <template #content>
                <CollectionDataTable :items="activeViewJson" :count="countViewPages" :idPage="form.id" :headers="$page.props.auth.user.current_collection.headers" class="" />
            </template>

            <template #footer>
                <SecondaryButton @click="activeViewModal = false">
                    Cancel
                </SecondaryButton>
            </template>
        </ApiModal>

        <ConfirmationModal :show="confirmingExportDeletion" @close="confirmingExportDeletion = false">
            <template #title>
                Delete Export
            </template>

            <template #content>
                Are you sure you want to delete this export?
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingExportDeletion = false">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="deleteExport"
                >
                    Delete Export
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
