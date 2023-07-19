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
import LanguageSelector from "Jetstream/Components/LanguageSelector.vue";
const locale = localStorage.getItem('locale') || 'en';
const activeLocale = ref(locale.toUpperCase());
const activeBlock = ref(null);
const props = defineProps({
    languages: Array
})

const form = useForm({
    firstBlock: {
        title: "Welcome to your Jetstream application!",
        value: "Laravel Jetstream provides a beautiful, robust starting point for your next Laravel application. Laravel is designed to help you build your application using a development environment that is simple, powerful, and enjoyable. We believe you should love expressing your creativity through programming, so we have spent time carefully crafting the Laravel ecosystem to be a breath of fresh air. We hope you love it.",
        link: {
            value: ""
        }
    },
    secondBlock: {
        title: "Documentation",
        value: "Laravel has wonderful documentation covering every aspect of the framework. Whether you're new to the framework or have previous experience, we recommend reading all of the documentation from beginning to end.",
        link: {
            name:"Explore the documentation",
            value: "https://laravel.com/docs"
        }
    },
    thirdBlock:{
        title: "Laracasts",
        value: "Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.",
        link: {
            name: "Start watching Laracasts",
            value: "https://laracasts.com"
        }
    },
    fourthBlock: {
        title: "Tailwind",
        value: "Laravel Jetstream is built with Tailwind, an amazing utility-first CSS framework that doesn't get in your way. You'll be amazed how easily you can build and maintain fresh, modern designs with this wonderful framework at your fingertips.",
        link: {
            value: "https://tailwindcss.com/"
        }
    },
    fifthBlock: {
        title: "Authentication",
        value: "Authentication and registration views are included with Laravel Jetstream, as well as support for user email verification and resetting forgotten passwords. So, you're free to get started with what matters most: building your application.",
        link: {
            value: ""
        }
    }
})
const displayingModal = ref(false);
const handleSubmit = () => {
    console.log(activeLocale);
    axios.post(route('dashboard.update'), {data: activeBlock.value, lang: activeLocale.value})
}

const editTitle = (active) => {
    console.log(active)
    activeBlock.value = form[active];
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
                        <div>{{form.firstBlock.title}}</div>
                    </h1>

                    <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{form.firstBlock.value}}
                    </p>
                </div>
                <div>
                    <PencilSquareIcon @click="editTitle('firstBlock')" class="-mr-0.5 mt-8 w-4 ml-4 cursor-pointer	" aria-hidden="true" />
                </div>
            </div>


        </div>

        <div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            <a :href="form.secondBlock.link.value">{{ form.secondBlock.title }}</a>
                        </h2>
                    </div>
                    <div>
                        <PencilSquareIcon @click="editTitle('secondBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{ form.secondBlock.value }}
                </p>

                <p class="mt-4 text-sm">
                    <a :href="form.secondBlock.link.value" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                        {{ form.secondBlock.link.name }}

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
                            <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            <a :href="form.thirdBlock.link.value">{{ form.thirdBlock.title }}</a>
                        </h2>
                    </div>
                    <div>
                        <PencilSquareIcon @click="editTitle('thirdBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{form.thirdBlock.value}}
                </p>

                <p class="mt-4 text-sm">
                    <a :href="form.thirdBlock.link.value" class="inline-flex items-center font-semibold text-tf-blue-700 dark:text-tf-blue-300">
                        {{ form.thirdBlock.link.name }}

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-tf-blue-500 dark:fill-tf-blue-200">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            <a :href="form.fourthBlock.link.value">{{ form.fourthBlock.title }}</a>
                        </h2>
                    </div>
                    <div>
                        <PencilSquareIcon @click="editTitle('fourthBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{form.fourthBlock.value}}
                </p>
            </div>

            <div>
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                            {{ form.fifthBlock.title }}
                        </h2>
                    </div>

                    <div>
                        <PencilSquareIcon @click="editTitle('fifthBlock')" class="-mr-0.5 w-4 ml-4 cursor-pointer" aria-hidden="true" />
                    </div>
                </div>

                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                    {{form.fifthBlock.value}}
                </p>
            </div>
        </div>
    </div>


    <!-- Token Value Modal -->
    <DialogModal :show="displayingModal" @close="displayingModal = false">
        <template #title>
            <div class="flex justify-between">
                <div>Edit</div>
                <LanguageSelector />
<!--                <select v-model="activeLocale">-->
<!--                    <option v-for="(item, key) in props.languages" :value="key" :selected="key === activeLocale">{{ item }}</option>-->
<!--                </select>-->
            </div>
        </template>

        <template #content>
            <div>
                <div class="col-span-6 sm:col-span-4">

                    <InputLabel for="title" value="Title" />
                    <TextInput
                        id="name"
                        v-model="activeBlock.title"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                    />
                    <InputLabel for="value" value="Value" />
                    <textarea v-model="activeBlock.value" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-500 sm:text-sm sm:leading-6 h-40"></textarea>
                    <!--                    <InputError :message="form.firstBlock.errors.name" class="mt-2" />-->
                    <div v-if="activeBlock.link.value">
                        <InputLabel for="title" value="Name link" />
                        <TextInput
                            id="link"
                            v-model="activeBlock.link.name"
                            type="text"
                            class="mt-1 block w-full"
                            autofocus
                        />

                        <InputLabel for="link" value="Link" />
                        <TextInput
                            id="link"
                            v-model="activeBlock.link.value"
                            type="text"
                            class="mt-1 block w-full"
                            autofocus
                        />
                    </div>
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
</template>
