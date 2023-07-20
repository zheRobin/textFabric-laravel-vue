<script setup>
import ApplicationLogo from 'Jetstream/Components/ApplicationLogo.vue';
import DialogModal from 'Jetstream/Components/DialogModal.vue';
import SecondaryButton from 'Jetstream/Components/SecondaryButton.vue';
import InputLabel from 'Jetstream/Components/InputLabel.vue';
import TextInput from 'Jetstream/Components/TextInput.vue';
import {useForm} from "@inertiajs/vue3";
import {ref} from 'vue'
import {PencilSquareIcon} from "@heroicons/vue/20/solid";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import axios from "axios";
import Youtube from "Jetstream/Components/Youtube.vue";
import LanguageSelector from "Jetstream/Components/LanguageSelector.vue";
const locale = localStorage.getItem('locale') || 'en';
const activeLocale = ref(locale);

const props = defineProps({
    data: Array
})
const displayingModal = ref(false);
const displayingYouTubeModal = ref(false);
const activeBlock = ref(null);

const showYouTubeModal = (url) => {
    activeUrlVideo.value = url;
    displayingYouTubeModal.value = true;
}
const handleSubmit = () => {
    console.log(activeLocale);
    axios.post(route('dashboard.update'), activeBlock.value).then(()=>{
        displayingModal.value = false;
    })
}
const activeUrlVideo = ref(null);
const isYouTubeLink = (url) => {
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        if (url.includes('watch?v=') || url.includes('embed/')) {
            return true;
        }
    }
    return false;
}

const editTitle = (active) => {
    console.log(props.data.find(item => item.block_name === active))
    activeBlock.value = props.data.find(item => item.block_name === active);
    displayingModal.value = true;
    console.log('title')
}

</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
            <ApplicationLogo class="block h-12 w-auto" />
            <div class="flex">
                <div>
                    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                        <div>{{props.data[0].title[activeLocale]}}</div>
                    </h1>

                    <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{props.data[0].value[activeLocale]}}
                    </p>
                    <p class="mt-4 text-sm">
                        <div v-if="props.data[0].link_name[activeLocale]">
                            <button v-if="isYouTubeLink(props.data[0].link)" @click="showYouTubeModal(props.data[0].link)" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                                {{props.data[0].link_name[activeLocale]}}

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <a v-else :href="props.data[0].link" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                                {{props.data[0].link_name[activeLocale]}}

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </p>
                </div>
                <div>
                    <PencilSquareIcon v-if="$page.props.auth.user.is_admin" @click="editTitle('firstBlock')" class="-mr-0.5 mt-8 w-4 ml-4 cursor-pointer	" aria-hidden="true" />
                </div>
            </div>


        </div>

        <div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <font-awesome-icon :icon="props.data[1].icon" style="color: #9CA3AF; font-size: 20px;"/>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            {{props.data[1].title[activeLocale]}}
                        </h2>
                    </div>
                    <div>
                        <PencilSquareIcon v-if="$page.props.auth.user.is_admin" @click="editTitle('secondBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{props.data[1].value[activeLocale]}}
                </p>

                <p class="mt-4 text-sm">
                    <div v-if="props.data[1].link_name[activeLocale]">
                        <button v-if="isYouTubeLink(props.data[1].link)" @click="showYouTubeModal(props.data[1].link)" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                            {{props.data[1].link_name[activeLocale]}}

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <a v-else :href="props.data[1].link" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                        {{props.data[1].link_name[activeLocale]}}

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    </div>
                </p>
            </div>

            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <font-awesome-icon :icon="props.data[2].icon" style="color: #9CA3AF; font-size: 20px;"/>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            {{props.data[2].title[activeLocale]}}
                        </h2>
                    </div>
                    <div>
                        <PencilSquareIcon v-if="$page.props.auth.user.is_admin" @click="editTitle('thirdBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{props.data[2].value[activeLocale]}}
                </p>

                <p class="mt-4 text-sm">
                    <div v-if="props.data[2].link_name[activeLocale]">
                        <button v-if="isYouTubeLink(props.data[2].link)" @click="showYouTubeModal(props.data[2].link)" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                            {{props.data[2].link_name[activeLocale]}}

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <a v-else :href="props.data[2].link" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                            {{props.data[2].link_name[activeLocale]}}

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </p>
            </div>

            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <font-awesome-icon :icon="props.data[3].icon" style="color: #9CA3AF; font-size: 20px;"/>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            {{props.data[3].title[activeLocale]}}
                        </h2>
                    </div>
                    <div>
                        <PencilSquareIcon v-if="$page.props.auth.user.is_admin" @click="editTitle('fourthBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{props.data[3].value[activeLocale]}}
                </p>

                <p class="mt-4 text-sm" v-if="props.data[3].link">
                    <div v-if="props.data[3].link_name[activeLocale]">
                        <button v-if="isYouTubeLink(props.data[3].link)" @click="showYouTubeModal(props.data[3].link)" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                            {{props.data[3].link_name[activeLocale]}}

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <a v-else :href="props.data[3].link" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                            {{props.data[3].link_name[activeLocale]}}

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </p>
            </div>

            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <font-awesome-icon :icon="props.data[4].icon" style="color: #9CA3AF; font-size: 20px;"/>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            {{props.data[4].title[activeLocale]}}
                        </h2>
                    </div>

                    <div>
                        <PencilSquareIcon v-if="$page.props.auth.user.is_admin" @click="editTitle('fifthBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{props.data[4].value[activeLocale]}}
                </p>

                <p class="mt-4 text-sm" v-if="props.data[4].link">
                    <div v-if="props.data[4].link_name[activeLocale]">
                        <button v-if="isYouTubeLink(props.data[4].link)" @click="showYouTubeModal(props.data[4].link)" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                            {{props.data[4].link_name[activeLocale]}}

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <a v-else :href="props.data[4].link" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                            {{props.data[4].link_name[activeLocale]}}

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                                <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </p>
            </div>
        </div>
    </div>


    <!-- Token Value Modal -->
    <DialogModal :show="displayingModal" @close="displayingModal = false">
        <template #title>
            <div>Edit</div>
        </template>

        <template #content>
            <div>
                <div class="col-span-6 sm:col-span-4">

                    <InputLabel for="title" value="Title" />
                    <TextInput
                        id="name"
                        v-model="activeBlock.title[activeLocale]"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                    />
                    <InputLabel for="value" value="Value" />
                    <textarea v-model="activeBlock.value[activeLocale]" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-500 sm:text-sm sm:leading-6 h-40"></textarea>
                        <InputLabel for="title" value="Name link" />
                        <TextInput
                            id="link"
                            v-model="activeBlock.link_name[activeLocale]"
                            type="text"
                            class="mt-1 block w-full"
                            autofocus
                        />

                        <InputLabel for="link" value="Link" />
                        <TextInput
                            id="link"
                            v-model="activeBlock.link"
                            type="text"
                            class="mt-1 block w-full"
                            autofocus
                        />
                    <InputLabel for="icon" value="Icon" />
                    <TextInput
                        id="icon"
                        v-model="activeBlock.icon"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                    />
                </div>
            </div>

            <div v-if="$page.props.jetstream.flash.token" class="mt-4 bg-gray-100 dark:bg-gray-900 px-4 py-2 rounded font-mono text-sm text-gray-500 break-all h-auto">
                {{ $page.props.jetstream.flash.token }}
            </div>
        </template>

        <template #footer>
            <PrimaryButton @click="handleSubmit">Save</PrimaryButton>

            <SecondaryButton class="ml-5" @click="displayingModal = false">
                Close
            </SecondaryButton>
        </template>
    </DialogModal>

    <DialogModal :show="displayingYouTubeModal" @close="displayingYouTubeModal = false">
        <template #title>

        </template>

        <template #content>
            <Youtube :url="activeUrlVideo" />
        </template>

        <template #footer>
            <SecondaryButton class="ml-5" @click="displayingYouTubeModal = false">
                Close
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
