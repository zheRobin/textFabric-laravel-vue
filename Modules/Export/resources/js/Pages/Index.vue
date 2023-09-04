<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import SelectMenuForDownload from "Modules/Export/resources/js/Components/SelectMenu.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";
import DialogModal from "Jetstream/Components/DialogModal.vue";
import ApiModal from "Jetstream/Components/ApiModal.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import axios from "axios";
import {notify} from "notiwind";
import { ref } from "vue"
import {useForm, usePage} from "@inertiajs/vue3";
import {options} from "Modules/Export/resources/js/optionsForDownload";
import ConfirmationModal from "Jetstream/Components/ConfirmationModal.vue";
import DangerButton from "Jetstream/Components/DangerButton.vue";
import Pagination from "Jetstream/Components/Pagination.vue";
import {MinusCircleIcon} from "@heroicons/vue/20/solid";
import {DocumentTextIcon, DocumentArrowDownIcon} from "@heroicons/vue/24/outline";
import EmptyCollection from "Modules/Collections/resources/js/Components/EmptyCollection.vue";
import EmptyImport from "Modules/Imports/resources/js/Components/EmptyImport.vue";
import ExportDataTable from "Modules/Export/resources/js/Components/ExportDataTable.vue";

const props = defineProps({
    languages: Array,
    compilations: Array,
    active: Array,
    hasItems: Boolean,
    activeExport: Object,
    items: Object
});

const translationType = 'translation';
const compilationType = 'compilation';

const activeLanguages = ref([]);
const activeModal = ref(false);
const selectedCompilations = ref(null);
const cancelling = ref(false);

const dataLabel = props.compilations.map(item => {
    return {
        value: item.id,
        label: item.name
    }
});

let data = {
    value: 0,
    label: ''
};

const activeGenerations = ref(data);
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
const exports = ref(null);
const cancelledExports = ref(null);

async function fetchProgress() {
    try {
        const response = await axios.get('/get-progress');
        progress.value = response.data.progress;
    } catch (error) {
        // console.error('Error fetching progress:', error);
    }
}

const generateActive = ref(false);

let progressInterval;
const showProgress = (id) => {
    clearInterval(progressInterval);
    if (localStorage.getItem('selected_queue_translation')) {
        activeGenerations.value = {
            label: localStorage.getItem('selected_queue_translation')
        }
    }

    generateActive.value = true;
    progressInterval = setInterval(() => {
        if (id) {
            axios.get(route('export.showProgress')).then((res) => {
                progress.value = res.data.data.progress;

                if (res.data.data.cancelled) {
                    cancelling.value = true;
                }

                if (progress.value === 100 || res.data.data.finished) {
                    generateActive.value = false;
                    cancelling.value = false;
                    generationDone();
                    notify({
                        group: 'success',
                        title: 'Success!',
                        text: 'The compilation was generated successfully!',
                    }, 4000);
                    clearInterval(progressInterval);
                    progress.value = 0;
                }
                if (localStorage.getItem('selected_queue')) {
                    activeGenerations.value = dataLabel.find(
                        (item) => item.value === parseInt(localStorage.getItem('selected_queue'))
                    );
                } else if (localStorage.getItem('selected_queue_translation')) (
                    activeGenerations.value = {
                        label: localStorage.getItem('selected_queue_translation')
                    }
                )
            });
        } else {
            clearInterval(progressInterval);
        }
    }, 2000);
}

if (props.activeExport &&
    props.activeExport.batch) {
    showProgress(props.activeExport.job_batch_id);
}

const activeQueue = ref(null);
const generate = async () => {
    generateActive.value = true;
    if (!loading.value) {
        if (selectedCompilations.value) {
            loading.value = true;
            axios.post(route('export.generate', form.compilations))
                .then((res) => {
                    activeQueue.value = res.data.id_queue;
                    progress.value = 0;
                    activeGenerations.value = dataLabel.find(
                        (item) => item.value === selectedCompilations.value
                    );
                    localStorage.setItem('id_queue', res.data.id_queue);
                    localStorage.setItem('selected_queue', selectedCompilations.value);
                    showProgress(activeQueue.value);
            });
        }
    }
};

const changePreset = (value) => {
    localStorage.setItem('selected-compilations', value)
    selectedCompilations.value = value;
}

const showModal = (id, value, name) => {
    form.name = name;
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
    axios.post(route('export.delete', form.id)).then((res) => {
        activeGenerations.value = null;
        loading.value = false;
        confirmingExportDeletion.value = false;
        form.id = null;
        exports.value = res.data;

        search();
        fetchCancelledExports();

        notify({
            group: "success",
            title: "Success",
            text: "Export file deleted!"
        }, 4000)
    });
}
const translation = () => {
    axios.post(route('export.translation', form.id), {
        value: form.value,
        languages: form.languages,
    }).then((res) => {
        search();
        fetchCancelledExports();
        loading.value = false;
        activeModal.value = false;
        activeQueue.value = res.data.id_queue;
        localStorage.setItem('id_queue', res.data.id_queue);
        localStorage.setItem('selected_queue_translation', form.name);
        progress.value = 0;
        showProgress(activeQueue.value);
    });

    progress.value = 0;
    localStorage.setItem('progress', 0);
    generateActive.value = true;

    activeGenerations.value = {
        value: 0,
        label: form.name
    }
}

const download = () => {
    const config = {
        responseType: "blob",
        headers: {
            'Content-Type': 'application/json; charset=UTF-8'
        }
    };

    axios.post(route('export.download', form.id), {format: selectedDownloadFormat.value}, config)
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            if (selectedDownloadFormat.value === '.xml') {
                link.setAttribute('download', `${activeDownloadName.value}.xml`);
            } else if (selectedDownloadFormat.value === '.json') {
                link.setAttribute('download', `${activeDownloadName.value}.json`);
            } else if (selectedDownloadFormat.value === '.csv') {
                link.setAttribute('download', `${activeDownloadName.value}.csv`);
            } else if (selectedDownloadFormat.value === '.xlsx') {
                link.setAttribute('download', `${activeDownloadName.value}.xlsx`);
            } else if (selectedDownloadFormat.value === '.xls') {
                link.setAttribute('download', `${activeDownloadName.value}.xls`);
            }

            document.body.appendChild(link);
            link.click();
            activeDownloadModal.value = false;
        })
        .catch(error => {
            // console.log(error);
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

    axios.get(route('export.items.index', item), {}).then((response) => {
        const item = response.data.data;

        activeViewJson.value = item;
        activeViewModal.value = true;
    });
}

const activeDownloadModal = ref(false);
const activeDownloadName = ref(null);
const showDownloadModal = (id, name) => {
    form.id = id;
    activeDownloadModal.value = true;
    activeDownloadName.value = name;
}

const closeDownloadModal = () => {
    selectedDownloadFormat.value = null;
    activeDownloadModal.value = false;
    activeDownloadName.value = null;
}

const closeModal = () => {
    form.id = null;
    form.value = null;
    activeModal.value = false;
}

const confirmingExportDeletion = ref(false);
const selectedDownloadFormat = ref(null);

const search = (event) => {
    axios
        .post(route('export.search'), {query: searchQuery.value})
        .then((response) => {
            exports.value = response.data.data;
        })
        .catch((error) => {
            // console.error(error);
        });
}

const fetchCancelledExports = (event) => {
    axios
        .post(route('export.cancelled'))
        .then((response) => {
            cancelledExports.value = response.data.data;
        })
        .catch((error) => {
            // console.error(error);
        });
}

const cancelQueue = () => {
    const id = localStorage.getItem('id_queue');
    axios.get(`/export/cancel/${id}`).then((res) => {
        if (translationType !== res.data.data.exportType) {
            generationDone();
            clearInterval(progressInterval);
        }
    })
}

const generationDone = (data) => {
    loading.value = false;
    activeGenerations.value = null;
    searchQuery.value = '';
    search();
    fetchCancelledExports();
    localStorage.removeItem('selected_queue_translation');
    localStorage.removeItem('id_queue');
    localStorage.removeItem('selected_queue');
    generateActive.value = false;
    progress.value = 0;
}

search();
fetchCancelledExports();
</script>

<template>
    <AppLayout title="Export">
        <template #header>
            <div class="flex">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $t('Export') }}
                </h2>
            </div>
        </template>
        <div class="max-w-7xl mx-auto my-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                <EmptyCollection class="mx-auto px-6 py-6" v-if="!$page.props.auth.user.current_collection" />

                <EmptyImport class="mx-auto px-6 py-6" v-else-if="!hasItems" />

                <div v-else class="mx-auto px-6 py-6">
                    <div class="flex border-b border-gray-200 pb-8 items-center">
                        <label class="mr-2 font-medium dark:text-white">{{$t('Compilation')}}:</label>
                        <SelectMenu @update:modelValue="changePreset" v-model="form.compilations" :options="dataLabel" id="employees" class="w-60" />
                        <PrimaryButton v-if="!generateActive && selectedCompilations" class="ml-2 gap-x-1.5" @click="generate">
                            {{ $t('Generate') }}
                        </PrimaryButton>
                    </div>
                    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                        <div class="border-b border-gray-200 pb-8 mb-8">
                            <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">{{$t('Currently running compilations')}}</h2>
                            <div class="flex justify-between mt-6" v-if="generateActive">
                                <div class="my-3 font-medium text-sm items-center flex">
                                    <DocumentArrowDownIcon class="mr-1 w-5 inline-flex" />
                                    {{activeGenerations.label}}
                                    {{'...'}}
                                </div>
                                <div class="flex">
                                    <div aria-label="Loading..." role="status" class="flex items-center space-x-2">
                                        <span class="text-xs font-medium text-gray-500">
                                            {{ cancelling ? $t('Cancelling') : $t('Loading') }}
                                            {{ '...' }}
                                            {{ progress ? `${progress} %` : '' }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <SecondaryButton @click="cancelQueue" class="ml-3">
                                            {{$t('Cancel')}}
                                        </SecondaryButton>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center mt-6 text-gray-700">{{$t('Nothing is being generated now')}}</div>
                        </div>
                        <div class="flex justify-between">
                            <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">{{$t('History of created compilations')}}</h2>
                            <div>
                                <div class="relative mt-2 flex items-center">
                                    <input type="text" @keyup="search" v-model="searchQuery" name="search" :placeholder="`${$t('Search')}...`" id="search" class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-500 sm:text-sm sm:leading-6" />
                                </div>
                            </div>
                        </div>
                        <div v-if="exports" class="border-b border-gray-200 mb-8 pb-8">
                            <ul role="list" class="divide-y divide-gray-100 mt-5">
                                <li v-for="item in exports.data" class="flex justify-between items-center gap-x-6 py-5">
                                    <div class="flex gap-x-4">
                                        <div class="min-w-0 flex-auto">
                                            <div class="flex items-center text-sm font-semibold leading-6 text-gray-900">
                                                <DocumentTextIcon class="mr-1 w-5" />
                                                {{ item.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden sm:flex sm:flex-col sm:items-end">
                                        <p class="text-sm leading-6 text-gray-900">
                                            <PrimaryButton @click="showViewModal(item)" class="ml-2 gap-x-1.5">
                                                {{ $t('View') }}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                    <path d="M6 3a3 3 0 00-3 3v1.5a.75.75 0 001.5 0V6A1.5 1.5 0 016 4.5h1.5a.75.75 0 000-1.5H6zM16.5 3a.75.75 0 000 1.5H18A1.5 1.5 0 0119.5 6v1.5a.75.75 0 001.5 0V6a3 3 0 00-3-3h-1.5zM12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5zM4.5 16.5a.75.75 0 00-1.5 0V18a3 3 0 003 3h1.5a.75.75 0 000-1.5H6A1.5 1.5 0 014.5 18v-1.5zM21 16.5a.75.75 0 00-1.5 0V18a1.5 1.5 0 01-1.5 1.5h-1.5a.75.75 0 000 1.5H18a3 3 0 003-3v-1.5z" />
                                                </svg>
                                            </PrimaryButton>

                                            <PrimaryButton :disabled="generateActive" @click="showModal(item.id, item.data, item.name)" class="ml-2 gap-x-1.5">
                                                {{$t('Translation')}}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                    <path fill-rule="evenodd" d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z" clip-rule="evenodd" />
                                                </svg>
                                            </PrimaryButton>

                                            <PrimaryButton @click="showDownloadModal(item.id, item.name)" class="ml-2 gap-x-1.5">
                                                {{ $t('Download') }}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4" id="download"><path fill="white" d="M21,14a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V15a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V15A1,1,0,0,0,21,14Zm-9.71,1.71a1,1,0,0,0,.33.21.94.94,0,0,0,.76,0,1,1,0,0,0,.33-.21l4-4a1,1,0,0,0-1.42-1.42L13,12.59V3a1,1,0,0,0-2,0v9.59l-2.29-2.3a1,1,0,1,0-1.42,1.42Z"></path></svg>
                                            </PrimaryButton>

                                            <DangerButton @click="showModalDelete(item.id)" class="ml-2 gap-x-1.5">
                                                {{ $t('Delete') }}
                                                <MinusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                            </DangerButton>
                                        </p>
                                    </div>
                                </li>
                            </ul>

                            <Pagination :links="exports.links" />
                        </div>
                        <div v-else class="mt-6 text-center text-gray-700">{{$t('Not found')}}</div>

                        <div class="flex justify-between">
                            <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">{{$t('History of cancelled and failed compilations')}}</h2>
                        </div>
                        <div v-if="cancelledExports">
                            <ul role="list" class="divide-y divide-gray-100 mt-5">
                                <li v-for="item in cancelledExports.data" class="flex justify-between items-center gap-x-6 py-5">
                                    <div class="flex gap-x-4">
                                        <div class="min-w-0 flex-auto">
                                            <div class="flex items-center text-sm font-semibold leading-6 text-gray-900">
                                                <DocumentTextIcon class="mr-1 w-5" />
                                                {{ item.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden sm:flex sm:flex-col sm:items-end">
                                        <p class="text-sm leading-6 text-gray-900">
                                            <DangerButton @click="showModalDelete(item.id)" class="ml-2 gap-x-1.5">
                                                {{ $t('Delete') }}
                                                <MinusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                            </DangerButton>
                                        </p>
                                    </div>
                                </li>
                            </ul>

                            <Pagination :links="cancelledExports.links" />
                        </div>
                        <div v-else class="mt-6 text-center text-gray-700">{{$t('Not found')}}</div>
                    </div>
                </div>
            </div>
        </div>

        <DialogModal :show="activeModal" @close="closeModal">
            <template #title>
                {{$t('Translation')}}
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
                    {{ $t('Translate') }}
                </PrimaryButton>
                <SecondaryButton class="ml-3" @click="closeModal">
                    {{$t('Close')}}
                </SecondaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="activeDownloadModal" @close="closeDownloadModal">
            <template #title>
                {{$t('Download')}}
            </template>

            <template #content>
                <div class="mt-5">
                    <InputLabel value="Select format"></InputLabel>
                    <SelectMenuForDownload @update:modelValue="changeDownloadFormat" v-model="selectedDownloadFormat" class="mt-3"  :options="options"/>
                </div>
            </template>

            <template #footer>
                <PrimaryButton @click="download">
                    {{$t('Download')}}
                </PrimaryButton>
                <SecondaryButton class="ml-3" @click="closeDownloadModal">
                    {{$t('Close')}}
                </SecondaryButton>
            </template>
        </DialogModal>

        <ApiModal :show="activeViewModal" @close="closeViewModal">
            <template #title>
                {{$t('View')}}
            </template>

            <template #content>
                <ExportDataTable :items="activeViewJson" :count="countViewPages" :idPage="form.id" :headers="$page.props.auth.user.current_collection.headers" />
            </template>

            <template #footer>
                <SecondaryButton @click="activeViewModal = false">
                    {{$t('Close')}}
                </SecondaryButton>
            </template>
        </ApiModal>

        <ConfirmationModal :show="confirmingExportDeletion" @close="confirmingExportDeletion = false">
            <template #title>
                {{ $t('Delete Export') }}
            </template>

            <template #content>
                {{$t('Are you sure you want to delete this export?')}}
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingExportDeletion = false">
                    {{$t('Cancel')}}
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="deleteExport"
                >
                    {{ $t('Delete Export') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
