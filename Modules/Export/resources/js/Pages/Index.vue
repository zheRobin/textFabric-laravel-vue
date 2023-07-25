<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";
import DialogModal from "Jetstream/Components/DialogModal.vue";
import ApiModal from "Jetstream/Components/ApiModal.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import CollectionDataTable from "Modules/Export/resources/js/Components/CollectionDataTable.vue";
import axios from "axios";
import {notify} from "notiwind";
import { ref } from "vue"
import {useForm} from "@inertiajs/vue3";
import {options} from "Modules/Export/resources/js/optionsForDownload";
import Select from "Modules/Export/resources/js/Components/Select.vue";

const props = defineProps({
    languages: Array,
    complications: Array,
    exports: Array
});

console.log(props.exports, 'exports')
const activeLanguages = ref([]);
const activeModal = ref(false);
const selectedCompilations = ref(null);
const activeGenerations = ref(null);
const loading = ref(false);

const form = useForm({
    id: null,
    compilations: null,
    languages: [],
    value: null
})
const generate = () => {
    if(!loading.value){
        if(selectedCompilations.value){
            loading.value = true;
            activeGenerations.value = data.find(item => item.value === selectedCompilations.value);
            form.post(route('export.generate'), {
                errorBag: 'errors',
                preserveScroll: true,
                onSuccess: () => {
                    activeGenerations.value = null;
                    loading.value = false;
                    notify({
                        group: "success",
                        title: "Success",
                        text: "Export file created!"
                    }, 4000)
                }
            });
        }
    }
}

const changePreset = (value) => {
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

const deleteExport = (id) => {
    form.id = id;

    form.post(route('export.delete'), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: () => {
            activeGenerations.value = null;
            loading.value = false;
            notify({
                group: "success",
                title: "Success",
                text: "Export file deleted!"
            }, 4000)
        }
    });
    console.log(id)
}

const translation = () => {
    console.log('translate')
    form.post(route('export.translation'), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: (res) => {
            console.log(res.props.exports[0].value, 'res')
            activeGenerations.value = null;
            loading.value = false;
            notify({
                group: "success",
                title: "Success",
                text: "The translation was successful"
            }, 4000)
            activeViewJson.value = res.props.exports[0].value;
        }

    });
}

const download = () => {
    axios.post(route('export.download'), {id: form.id, format: selectedDownloadFormat.value})
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            if(selectedDownloadFormat.value === '.xml'){
                link.setAttribute('download', 'data.xml');
            }else if(selectedDownloadFormat.value === '.json'){
                link.setAttribute('download', 'data.json');
            }

            document.body.appendChild(link);
            link.click();
        })
}

const changeDownloadFormat = (value) => {
    selectedDownloadFormat.value = value;
    console.log(value);
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
const activeViewJson = ref(null);
const showViewModal = (item) => {
    form.value = null;
    axios.get(`/export/${item.id}`).then((res) => {
        activeViewJson.value = res;

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
const data = props.complications.map(item => {
    return {
        value: item.id,
        label: item.name
    }
});

const selectedDownloadFormat = ref(null);

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
                <div class="mx-auto px-6 py-6">
                    <div class="flex border-b border-gray-200 pb-8 items-center">
                        <label class="mr-2 mt-1 font-medium dark:text-white">{{$t('Compilation')}}:</label>
                        <SelectMenu @update:modelValue="changePreset" v-model="form.compilations" :options="data" id="employees" class="w-60" />
                        <PrimaryButton class="ml-2 gap-x-1.5" @click="generate">
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
                                        <svg class="h-6 w-6 animate-spin stroke-gray-500" viewBox="0 0 256 256">
                                            <line x1="128" y1="32" x2="128" y2="64" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"></line>
                                            <line
                                                x1="195.9"
                                                y1="60.1"
                                                x2="173.3"
                                                y2="82.7"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="24"></line>
                                            <line x1="224" y1="128" x2="192" y2="128" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"></line>
                                            <line
                                                x1="195.9"
                                                y1="195.9"
                                                x2="173.3"
                                                y2="173.3"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="24"></line>
                                            <line x1="128" y1="224" x2="128" y2="192" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"></line>
                                            <line
                                                x1="60.1"
                                                y1="195.9"
                                                x2="82.7"
                                                y2="173.3"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="24"></line>
                                            <line x1="32" y1="128" x2="64" y2="128" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"></line>
                                            <line
                                                x1="60.1"
                                                y1="60.1"
                                                x2="82.7"
                                                y2="82.7"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="24"></line>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-500">Loading...</span>
                                    </div>
                                    <div>
                                        <SecondaryButton class="ml-3 mt-2">
                                            Cancel
                                        </SecondaryButton>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="mt-6 text-center">Nothing is being generated now</div>

                        </div>
                        <div class="flex justify-between">
                            <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">History of created compilations</h2>
                            <div>
                                <div class="relative mt-2 flex items-center">
                                    <input type="text" name="search" placeholder="Search..." id="search" class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                    <div class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5">
                                        <kbd class="inline-flex items-center rounded border border-gray-200 px-1 font-sans text-xs text-gray-400">⌘K</kbd>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul role="list" class="divide-y divide-gray-100 mt-5">
                            <li v-for="item in props.exports" class="flex justify-between gap-x-6 py-5">
                                <div class="flex gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ item.name }}</p>
                                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">1 text presets / 2 images presets / 12 languages</p>
                                    </div>
                                </div>
                                <div class="hidden sm:flex sm:flex-col sm:items-end">
                                    <p class="text-sm leading-6 text-gray-900">
                                        <PrimaryButton @click="showViewModal(item)" class="ml-2 gap-x-1.5">
                                            View
                                        </PrimaryButton>
                                        <PrimaryButton @click="showModal(item.id, item.value)" class="ml-2 gap-x-1.5">
                                            Translation
                                        </PrimaryButton>
                                        <PrimaryButton @click="showDownloadModal(item.id)" class="ml-2 gap-x-1.5">
                                            Download
                                        </PrimaryButton>
                                        <SecondaryButton @click="deleteExport(item.id)" class="ml-2 gap-x-1.5">
                                            Delete
                                        </SecondaryButton>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
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
                <div class="mt-5 h-40">
                    <InputLabel value="Select format"></InputLabel>
                    <SelectMenu @update:modelValue="changeDownloadFormat" v-model="selectedDownloadFormat" class="mt-3"  :options="options"/>
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
                <CollectionDataTable :items="activeViewJson" :headers="$page.props.auth.user.current_collection.headers" class="" />
            </template>

            <template #footer>
                <SecondaryButton @click="activeViewModal = false">
                    Cancel
                </SecondaryButton>
            </template>
        </ApiModal>
    </AppLayout>
</template>
