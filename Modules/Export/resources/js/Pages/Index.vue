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
import {computed, onMounted, onUnmounted, ref} from "vue"
import {router, useForm, usePage} from "@inertiajs/vue3";
import {options} from "Modules/Export/resources/js/optionsForDownload";
import ConfirmationModal from "Jetstream/Components/ConfirmationModal.vue";
import DangerButton from "Jetstream/Components/DangerButton.vue";
import Pagination from "Modules/Export/resources/js/Components/Pagination.vue";
import {MinusCircleIcon} from "@heroicons/vue/20/solid";
import {DocumentArrowDownIcon, DocumentTextIcon} from "@heroicons/vue/24/outline";
import EmptyCollection from "Modules/Collections/resources/js/Components/EmptyCollection.vue";
import EmptyImport from "Modules/Imports/resources/js/Components/EmptyImport.vue";
import ExportDataTable from "Modules/Export/resources/js/Components/ExportDataTable.vue";
import {trans} from "laravel-vue-i18n";
import {hasPlanLimit} from "Modules/Subscriptions/resources/js/subscriptions";

const props = defineProps({
    languages: Array,
    compilations: Array,
    active: Array,
    hasItems: Boolean,
    activeExport: Object,
    activeExportProgress: Number | null,
    items: Object,
    teamRunningCompilations: Array,
});

const translationType = 'translation';
const compilationType = 'compilation';

const activeLanguages = ref([]);
const activeModal = ref(false);
const selectedCompilations = ref(null);
const cancelling = ref(false);
const loadingSuccessfulJobs = ref(false);
const loadingCancelledJobs = ref(false);
const page = usePage();

const dataLabel = props.compilations.map(item => {
    return {
        value: item.id,
        label: item.name,
    }
});

let data = {
    value: 0,
    label: '',
};

const activeGenerations = ref(data);
const loading = ref(false);
const runningCompilations = ref({pending: 0, total: 0});
const anyTeamHasRunningCompilations = computed(() =>
    runningCompilations.value.pending !== 0 &&
    runningCompilations.value.pending !== runningCompilations.value.total
);

const hasLimit = computed(() => hasPlanLimit(page.props.planSubscription));

const form = useForm({
    id: null,
    compilations: null,
    languages: [],
    value: null,
});

const searchQuery = ref("");
const progress = ref(null);
const exports = ref(null);
const cancelledExports = ref(null);
const generateActive = ref(false);
const jobBatchId = ref(localStorage.getItem('id_queue') || null);

let progressInterval;
const showProgress = (id) => {
    clearInterval(progressInterval);
    if (localStorage.getItem('selected_queue_translation')) {
        activeGenerations.value = {
            label: localStorage.getItem('selected_queue_translation'),
            value: 0,
        }
    }

    generateActive.value = true;
    progressInterval = setInterval(() => {
        if (!id) {
            clearInterval(progressInterval);
        }

        axios.get(route('export.showProgress')).then((res) => {
            progress.value = res.data.data.progress;
            runningCompilations.value = res.data.data.compilations;
            jobBatchId.value = res.data.data.job_batch_id;

            localStorage.setItem('id_queue', res.data.data.job_batch_id);
            cancelling.value = false;

            if (res.data.data.finished) {
                if (res.data.data.cancelled) {
                    cancelling.value = true;

                    notify({
                        group: 'error',
                        title: 'Error!',
                        text: trans('The compilation is cancelled!'),
                    }, 4000);
                } else {
                    if (parseInt(progress.value) === 100 || res.data.data.successful) {
                        notify({
                            group: 'success',
                            title: 'Success!',
                            text: trans('The compilation has finished successfully!'),
                        }, 4000);
                    } else {
                        notify({
                            group: 'error',
                            title: 'Error!',
                            text: trans('The compilation has finished with failure!'),
                        }, 4000);
                    }
                }

                generateActive.value = false;
                generationDone();

                clearInterval(progressInterval);
                progress.value = null;
                router.reload({only: ['teamRunningCompilations']});
            }

            if (res.data.data.name) {
                activeGenerations.value = {
                    label: res.data.data.name,
                    value: progress.value,
                };
            } else if (localStorage.getItem('selected_queue')) {
                activeGenerations.value = dataLabel.find((item) => item.value === parseInt(localStorage.getItem('selected_queue')));
            } else if (localStorage.getItem('selected_queue_translation')) {
                activeGenerations.value = {
                    label: localStorage.getItem('selected_queue_translation'),
                    value: progress.value,
                }
            }
        });
    }, 2000);
}

onUnmounted(() => clearInterval(progressInterval));

if (props.activeExport && props.activeExport.batch) {
    showProgress(props.activeExport.job_batch_id);
}

const activeQueue = ref(null);

const alertCompilationWithNoPreset = () => notify({
    group: 'error',
    title: 'Error!',
    text: trans("This compilation doesn't contain any presets. Please add presets to the selected compilation to start an export."),
}, 4000);

const selectedCompilationHasPreset = () => {
    const selectedCompilation = localStorage.getItem('selected-compilations');

    if (!selectedCompilation) {
        alertCompilationWithNoPreset();
        return false;
    }

    const currentCompilation = props?.compilations.find((compilation) => compilation.id === parseInt(selectedCompilation));

    if (currentCompilation.preset_ids.length === 0) {
        alertCompilationWithNoPreset();
        return false;
    }

    return true;
}

const generate = async () => {
    if (!selectedCompilationHasPreset()) {
        return;
    }

    // check requests limit of the subscription plan
    if (!hasLimit.value.openai || !hasLimit.value.api) {
        notify({
            group: 'error',
            title: 'Error!',
            text: trans('Your team is out of remaining requests for this month. Please adjust your plan or wait until the next month.'),
        }, 4000);
        return;
    }

    generateActive.value = true;
    progress.value = null;

    if (!loading.value && selectedCompilations.value) {
        loading.value = true;

        axios.post(route('export.generate', form.compilations)).then((res) => {
            activeQueue.value = res.data.id_queue;
            progress.value = 0;
            activeGenerations.value = dataLabel.find((item) => item.value === selectedCompilations.value);
            localStorage.setItem('id_queue', activeQueue.value);
            localStorage.setItem('selected_queue', selectedCompilations.value);
            showProgress(activeQueue.value);
        }).catch(error => {
            generateActive.value = false;
            notify({
                group: 'error',
                title: 'Error!',
                text: error.response?.data?.message || trans('Error generating compilation'),
            }, 4000);
        });
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
            text: trans("Export file deleted!")
        }, 4000);
    }).catch(() => {
        notify({
            group: "error",
            title: "Error",
            text: trans("Error deleting export file!")
        }, 4000);
    });
}
const translation = () => {
    if (form.languages.length === 0) {
        return;
    }

    // check requests limit of the subscription plan
    if (!hasLimit.value.deepl || !hasLimit.value.api) {
        notify({
          group: 'error',
          title: 'Error!',
          text: trans('Your team is out of remaining requests for this month. Please adjust your plan or wait until the next month.'),
        }, 4000);
        activeModal.value = false;
        activeLanguages.value = [];
        return;
    }

    localStorage.setItem('progress', 0);
    generateActive.value = true;
    progress.value = null;
    activeLanguages.value = [];

    axios.post(route('export.translation', form.id), {
        value: form.value,
        languages: form.languages,
    }).then((res) => {
        search();
        fetchCancelledExports();
        loading.value = false;
        activeQueue.value = res.data.id_queue;
        localStorage.setItem('id_queue', res.data.id_queue);
        localStorage.setItem('selected_queue_translation', form.name);
        progress.value = 0;
        activeGenerations.value = {label: form.name, value: 0};
        showProgress(activeQueue.value);
    }).catch((error) => {
        generateActive.value = false;
        notify({
            group: 'error',
            title: 'Error!',
            text: error.response.data?.message || trans( 'Error generating translation'),
        }, 4000);
    }).finally(() => {
        activeModal.value = false;
    });
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
        activeViewJson.value = response.data.data;
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
    activeLanguages.value = [];
}

const confirmingExportDeletion = ref(false);
const selectedDownloadFormat = ref(null);

const search = (uri = null, pageNumber = '1') => {
    if (!page.props.auth.user.current_collection_id) {
        return;
    }

    loadingSuccessfulJobs.value = true;

    axios.post(uri || route('export.search'), {query: searchQuery.value, page: pageNumber})
        .then((response) => {
            exports.value = response.data.data;
        })
        .catch((error) => {
            // console.error(error);
        }).finally(() => (loadingSuccessfulJobs.value = false));
}

const fetchCancelledExports = (uri) => {
    if (!page.props.auth.user.current_collection_id) {
        return;
    }

    loadingCancelledJobs.value = true;

    axios.get(uri || route('export.cancelled'))
        .then((response) => {
            cancelledExports.value = response.data.data;
        })
        .catch((error) => {
            // console.error(error);
        }).finally(() => (loadingCancelledJobs.value = false));
}

const cancelQueue = () => {
    const id = localStorage.getItem('id_queue') || jobBatchId.value;

    if (!id) {
        notify({
            group: 'error',
            title: 'Error!',
            text: trans( 'Error cancelling job, missing job-batch ID'),
        }, 4000);
        return;
    }

    axios.get(`/export/cancel/${id}`).then((res) => {
        // generationDone();
        // clearInterval(progressInterval);
    });
}

const generationDone = (data) => {
    loading.value = false;
    activeGenerations.value = null;
    searchQuery.value = '';
    search();
    fetchCancelledExports();
    triggerTeamRunningCompilationsStatus();
    localStorage.removeItem('selected_queue_translation');
    localStorage.removeItem('id_queue');
    localStorage.removeItem('selected_queue');
    generateActive.value = false;
    progress.value = null;
}

const switchToCollection = (collectionId) => {
    localStorage.removeItem('selected-preset');
    router.put(route('current-collection.update', collectionId), {
      redirect: 'export',
    }, {
        preserveState: false,
    });
}

let teamRunningCompilationsInterval;

const teamRunningCompilationsStatus = () => {
    if (props.teamRunningCompilations.length === 0) {
        clearInterval(teamRunningCompilationsInterval);
    } else {
        router.reload({ only: ['teamRunningCompilations'] });
    }
}

const triggerTeamRunningCompilationsStatus = () => {
    teamRunningCompilationsInterval = setInterval(() => teamRunningCompilationsStatus(), 3000);
}

onMounted(() => {
    if (!progressInterval) {
        triggerTeamRunningCompilationsStatus();
    }
});
onUnmounted(() => clearInterval(teamRunningCompilationsInterval));

const paginate = (destination = null) => {
    let uri = null, pageNumber = '1';

    if (destination) {
        let url = new URL(destination);
        uri = url.origin + url.pathname;
        pageNumber = url.searchParams.get('page');
    }

    destination.includes('cancelled')
        ? fetchCancelledExports(destination)
        : search(uri, pageNumber);
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

        <div class="max-w-7xl mx-auto mt-8 mb-8 sm:px-6 lg:px-8">
            <div v-if="generateActive && (anyTeamHasRunningCompilations || progress === 0 || progress === null)" class="flex items-center mb-6 text-sm bg-yellow-200 text-yellow-900 border-l-4 border-yellow-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 m-4 text-yellow-500">
                  <path d="M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/>
                </svg>
                <span v-if="anyTeamHasRunningCompilations">{{ $t('There :isSingular :count job in the queue in front of you before we can start your processing.', {'isSingular': (runningCompilations.pending <= 1 ? $t('is') : $t('are')) , 'count': runningCompilations.pending.toString()}) }}</span>
                <span v-else-if="progress === 0">{{ $t('Waiting for background workers to start processing.') }}</span>
                <span v-else-if="progress === null">{{ $t('Fetching status of this job.') }}</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto mt-8 mb-8 sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                <EmptyCollection class="mx-auto px-6 py-6" v-if="!$page.props.auth.user.current_collection" />

                <EmptyImport class="mx-auto px-6 py-6" v-else-if="!hasItems" />

                <div v-else class="mx-auto px-6 py-6">
                    <div class="max-w-7xl mx-auto pt-10 sm:px-6 lg:px-8">
                        <div class="border-b border-gray-200 pb-8">
                            <div v-if="!generateActive && props.teamRunningCompilations.length !== 0" class="flex items-center mb-6 text-sm bg-yellow-200 text-yellow-900 border-l-4 border-yellow-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-4 h-4 m-4 text-yellow-500">
                                    <path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/>
                                </svg>
                                <span>{{ $t('Team has running compilations') }}</span>
                                <span v-for="teamRunningCompilation in props.teamRunningCompilations" class="flex flex-wrap font-medium text-sm">
                                    <span class="flex items-center ml-3">
                                        <a href="" @click.prevent="switchToCollection(teamRunningCompilation.collection_id)" class="flex items-center hover:underline">
                                            <DocumentArrowDownIcon class="mr-1 w-5 inline-flex" />
                                            <span>{{ teamRunningCompilation?.name }}</span>
                                        </a>
                                        <span v-if="teamRunningCompilation?.batch?.total_jobs" class="mx-2 px-1 bg-amber-500 text-amber-50 rounded shadow">
                                            {{ Math.round((teamRunningCompilation?.batch?.total_jobs - teamRunningCompilation?.batch?.pending_jobs) / teamRunningCompilation?.batch?.total_jobs * 100) }}%
                                        </span>
                                    </span>
                                </span>
                            </div>
                            <div class="flex items-center">
                                <label class="mr-2 font-medium dark:text-white">{{$t('Compilation')}}:</label>
                                <SelectMenu @update:modelValue="changePreset" v-model="form.compilations" :options="dataLabel" id="employees" class="w-60" />
                                <PrimaryButton v-if="!generateActive && selectedCompilations && props.teamRunningCompilations.length === 0" class="ml-2 gap-x-1.5" @click="generate">
                                    {{ $t('Generate') }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                        <div class="border-b border-gray-200 pb-8 mb-8">
                            <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">{{$t('Currently running compilations')}}</h2>
                            <div class="flex justify-between mt-6" v-if="generateActive">
                                <div class="flex items-center text-sm font-semibold leading-6 text-gray-900 my-3">
                                    <DocumentArrowDownIcon class="mr-1 w-5 inline-flex" />
                                    {{activeGenerations?.label || '...'}}
                                </div>
                                <div class="flex">
                                    <div aria-label="Loading..." role="status" class="flex items-center space-x-2">
                                        <span class="text-xs font-medium text-gray-500">
                                            {{ cancelling ? $t('Cancelling') : $t('Loading') }}
                                            {{ '...' }}
                                            {{ progress ? `${progress} %` : '' }}
                                        </span>
                                    </div>
                                    <div v-if="jobBatchId" class="mt-2">
                                        <SecondaryButton @click="cancelQueue" class="ml-3">
                                            {{ $t('Cancel') }}
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
                                    <input type="text" @keyup="() => search(null)" v-model="searchQuery" name="search" :placeholder="`${$t('Search')}...`" id="search" class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-500 sm:text-sm sm:leading-6" />
                                </div>
                            </div>
                        </div>
                        <div v-if="exports?.data?.length" class="border-b border-gray-200 mb-8 pb-8">
                            <ul role="list" class="divide-y divide-gray-100 mt-5">
                                <li v-for="item in exports.data" class="flex justify-between flex-col sm:flex-row gap-x-6 py-5">
                                    <div class="flex gap-x-4">
                                        <div class="min-w-0 flex-auto">
                                            <div class="flex items-center text-sm font-semibold leading-6 text-gray-900 mb-2">
                                                <DocumentTextIcon class="mr-1 w-5" />
                                                {{ item.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <p class="text-sm leading-6 text-gray-900">
                                            <PrimaryButton @click="showViewModal(item)" class="ml-2 mb-2 gap-x-1.5">
                                                {{ $t('View') }}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                    <path d="M6 3a3 3 0 00-3 3v1.5a.75.75 0 001.5 0V6A1.5 1.5 0 016 4.5h1.5a.75.75 0 000-1.5H6zM16.5 3a.75.75 0 000 1.5H18A1.5 1.5 0 0119.5 6v1.5a.75.75 0 001.5 0V6a3 3 0 00-3-3h-1.5zM12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5zM4.5 16.5a.75.75 0 00-1.5 0V18a3 3 0 003 3h1.5a.75.75 0 000-1.5H6A1.5 1.5 0 014.5 18v-1.5zM21 16.5a.75.75 0 00-1.5 0V18a1.5 1.5 0 01-1.5 1.5h-1.5a.75.75 0 000 1.5H18a3 3 0 003-3v-1.5z" />
                                                </svg>
                                            </PrimaryButton>

                                            <PrimaryButton :disabled="generateActive" @click="showModal(item.id, item.data, item.name)" class="ml-2 mb-2 gap-x-1.5">
                                                {{$t('Translation')}}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                    <path fill-rule="evenodd" d="M9 2.25a.75.75 0 01.75.75v1.506a49.38 49.38 0 015.343.371.75.75 0 11-.186 1.489c-.66-.083-1.323-.151-1.99-.206a18.67 18.67 0 01-2.969 6.323c.317.384.65.753.998 1.107a.75.75 0 11-1.07 1.052A18.902 18.902 0 019 13.687a18.823 18.823 0 01-5.656 4.482.75.75 0 11-.688-1.333 17.323 17.323 0 005.396-4.353A18.72 18.72 0 015.89 8.598a.75.75 0 011.388-.568A17.21 17.21 0 009 11.224a17.17 17.17 0 002.391-5.165 48.038 48.038 0 00-8.298.307.75.75 0 01-.186-1.489 49.159 49.159 0 015.343-.371V3A.75.75 0 019 2.25zM15.75 9a.75.75 0 01.68.433l5.25 11.25a.75.75 0 01-1.36.634l-1.198-2.567h-6.744l-1.198 2.567a.75.75 0 01-1.36-.634l5.25-11.25A.75.75 0 0115.75 9zm-2.672 8.25h5.344l-2.672-5.726-2.672 5.726z" clip-rule="evenodd" />
                                                </svg>
                                            </PrimaryButton>

                                            <PrimaryButton @click="showDownloadModal(item.id, item.name)" class="ml-2 mb-2 gap-x-1.5">
                                                {{ $t('Download') }}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4" id="download"><path fill="white" d="M21,14a1,1,0,0,0-1,1v4a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V15a1,1,0,0,0-2,0v4a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V15A1,1,0,0,0,21,14Zm-9.71,1.71a1,1,0,0,0,.33.21.94.94,0,0,0,.76,0,1,1,0,0,0,.33-.21l4-4a1,1,0,0,0-1.42-1.42L13,12.59V3a1,1,0,0,0-2,0v9.59l-2.29-2.3a1,1,0,1,0-1.42,1.42Z"></path></svg>
                                            </PrimaryButton>

                                            <DangerButton @click="showModalDelete(item.id)" class="ml-2 mb-2 gap-x-1.5">
                                                {{ $t('Delete') }}
                                                <MinusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                            </DangerButton>
                                        </p>
                                    </div>
                                </li>
                            </ul>

                            <Transition leave-active-class="transition ease-in duration-400">
                                <Pagination v-if="!loadingSuccessfulJobs" @update:pagination="paginate" :links="exports.links" />
                            </Transition>
                        </div>
                        <div v-else class="border-b border-gray-200 pb-8 mt-6 mb-8 text-center text-gray-700">{{$t('Not found')}}</div>

                        <div class="flex justify-between">
                            <h2 class="mt-3 text-base font-semibold leading-6 text-gray-900">{{$t('History of cancelled and failed compilations')}}</h2>
                        </div>
                        <div v-if="cancelledExports?.data?.length">
                            <ul role="list" class="divide-y divide-gray-100 mt-5">
                                <li v-for="item in cancelledExports.data" class="flex justify-between flex-col sm:flex-row gap-x-6 py-5">
                                    <div class="flex gap-x-4">
                                        <div class="min-w-0 flex-auto">
                                            <div class="flex items-center text-sm font-semibold leading-6 text-gray-900 mb-2">
                                                <DocumentTextIcon class="mr-1 w-5" />
                                                {{ item.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <p class="text-sm leading-6 text-gray-900">
                                            <DangerButton @click="showModalDelete(item.id)" class="ml-2 mb-2 gap-x-1.5">
                                                {{ $t('Delete') }}
                                                <MinusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                                            </DangerButton>
                                        </p>
                                    </div>
                                </li>
                            </ul>

                            <Transition leave-active-class="transition ease-in duration-400">
                                <Pagination v-if="!loadingCancelledJobs" @update:pagination="paginate" :links="cancelledExports.links" />
                            </Transition>
                        </div>
                        <div v-else class="border-b border-gray-200 pb-8 mt-6 mb-8 text-center text-gray-700">{{$t('Not found')}}</div>
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
                    <span @click="setLanguage(language.code)" v-for="language in props.languages" :class="activeLanguages.includes(language.code) ? style.enable : style.disable">
                      {{ language.name }}
                      <button v-if="activeLanguages.includes(language.code)" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-gray-500/20">
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
                    <InputLabel value="{{ $t('Select format') }}"></InputLabel>
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
