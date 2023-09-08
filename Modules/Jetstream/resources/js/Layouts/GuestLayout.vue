<script setup>
import {computed, ref} from 'vue';
import {Head, Link, router, usePage} from '@inertiajs/vue3';
import Banner from 'Jetstream/Components/Banner.vue';
import NavLink from 'Jetstream/Components/NavLink.vue';
import ResponsiveNavLink from 'Jetstream/Components/ResponsiveNavLink.vue';
import NotificationBanner from 'Jetstream/Components/NotificationBanner.vue';
import ApplicationLogo from 'Jetstream/Components/ApplicationLogo.vue';
import LanguageSelector from "Jetstream/Components/LanguageSelector.vue";
import DemoBanner from "Jetstream/Components/DemoBanner.vue";

defineProps({
    title: String,
    locale: String
});

const locale = localStorage.getItem('locale') || 'en';

const showingNavigationDropdown = ref(false);

const currentTeam = computed(() => usePage().props.auth.user.current_team);
const currentCollection = computed( () => usePage().props.auth.user.current_collection);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const switchToCollection = (collection) => {
    localStorage.removeItem('selected-preset');
    router.put(route('current-collection.update', collection.id), {}, {
        preserveState: false,
    })
}

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <NotificationBanner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <DemoBanner />

            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('demo.import.index')" :class="route().current('demo.import.index') ? 'dark:text-white' : ''" :active="route().current('demo.import.index')">
                                    {{$t('Import')}}
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('demo.editor.index')" :class="route().current('demo.editor.index') ? 'dark:text-white' : ''" :active="route().current('demo.editor.index')">
                                    {{$t('Editor')}}
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('demo.compilations.index')" :class="route().current('demo.compilations.index') ? 'dark:text-white' : ''" :active="route().current('demo.compilations.index')">
                                    {{$t('Compilations')}}
                                </NavLink>
                            </div>

<!--                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">-->
<!--                                <NavLink :href="route('export.index')" :class="route().current('export.index') ? 'dark:text-white' : ''" :active="route().current('export.index')">-->
<!--                                    {{$t('Export')}}-->
<!--                                </NavLink>-->
<!--                            </div>-->

                        </div>



                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <div class="">
                                <LanguageSelector />
                            </div>
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('demo.import.index')" :active="route().current('demo.import.index')">
                            {{ $t('Import') }}
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('demo.editor.index')" :active="route().current('demo.editor.index')">
                            {{ $t('Editor') }}
                        </ResponsiveNavLink>
                    </div>


                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('demo.compilations.index')" :active="route().current('demo.compilations.index')">
                            {{ $t('Compilations') }}
                        </ResponsiveNavLink>
                    </div>

                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
