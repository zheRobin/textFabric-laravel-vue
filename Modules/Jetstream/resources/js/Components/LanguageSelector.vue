<template>
    <div class="xl:ml-3 relative">
        <Dropdown align="right" width="48">
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                    >
                        {{ locale === 'en' ? '🇬🇧' : '🇩🇪' }}
                        <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                          <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                          />
                        </svg>
                    </button>
                </span>
            </template>

            <template #content>
                <DropdownLink v-if="locale !== 'en'" as='button' @click="getLocalizedURL('en')">
                    🇬🇧 {{$t('English')}}
                </DropdownLink>
                <DropdownLink v-else as='button' @click="getLocalizedURL('de')">
                    🇩🇪 {{$t('German')}}
                </DropdownLink>
            </template>
        </Dropdown>
    </div>
</template>

<script>
import Dropdown from 'Jetstream/Components/DropdownForLanguage.vue';
import DropdownLink from 'Jetstream/Components/DropdownLink.vue';
import axios from "axios";
export default {
    data() {
        return {
            locale: localStorage.getItem('locale') || 'en',
        };
    },
    mounted() {
        this.supportedLocales = this.getSupportedLocales();
    },
    methods: {
        getSupportedLocales() {
            // Replace this part with your actual logic to fetch the supported locales
            return {
                en: { native: "English" },
                de: { native: "Germany" },
                // Add more locales as needed
            };
        },
        getLocalizedURL(localeCode) {
            // Replace this part with your actual logic to generate the localized URL
            axios.post('/change-language', { locale: localeCode }).then((res) => {
                localStorage.setItem('locale', res.data.locale);
                if(res.data.locale === 'en'){
                    const url = window.location.pathname;

                    let newUrl = url.replace("de", "");
                    if(url.includes('de/')){
                        newUrl = url.replace("de/", "");
                    }

                    // console.log(window.location);
                    window.location.pathname = newUrl;
                }else{
                    window.location.pathname = 'de' + window.location.pathname
                }

            });
        },
    },
    components: {
        Dropdown,
        DropdownLink,
    },
};
</script>
