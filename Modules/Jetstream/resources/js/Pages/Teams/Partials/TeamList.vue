<script setup>
import { CalendarIcon, UsersIcon } from '@heroicons/vue/20/solid';
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";
import { teamRoleAdmin } from "Jetstream/teams.js";
import { toLocaleDate } from "Modules/Subscriptions/resources/js/subscriptions";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    teams: Array,
})

const getAdminsFromUsers = (users) => {
    return users.filter( (user) => user.membership.role === teamRoleAdmin );
}
</script>

<template>
    <!-- Find input -->
    <div class="flex justify-end mb-5">
        <div class="mt-2 flex rounded-md shadow-sm">
            <div class="relative flex flex-grow items-stretch focus-within:z-10">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <UsersIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </div>
                <input type="email" name="email" id="email" class="block w-full rounded-none rounded-l-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="John Smith" />
            </div>
            <button type="button" class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <MagnifyingGlassIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
            </button>
        </div>
    </div>

    <div class="overflow-hidden bg-white shadow sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            <li v-for="team in teams" :key="team.id">
                <Link :href="route('teams.show', team)" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="truncate text-sm font-medium text-tf-blue-500">{{ team.name }}</p>
                            <div v-if="team.plan_subscription" class="ml-2 flex flex-shrink-0">
                                <PrimaryBadge v-if="team.plan_subscription.is_active">Subscribed</PrimaryBadge>
                                <DangerBadge v-else>Expired</DangerBadge>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <div class="flex-col">
                                    <p class="text-sm flex font-medium text-gray-900">
                                        {{ team.owner.email }}
                                    </p>
                                    <p class="truncate flex text-sm text-gray-500">
                                        Owner
                                    </p>
                                </div>

                                <div class="flex-col sm:ml-5" v-for="member in getAdminsFromUsers(team.users)">
                                    <p class="text-sm flex font-medium text-gray-900">
                                        {{ member.email }}
                                    </p>
                                    <p class="truncate flex text-sm text-gray-500 capitalize">
                                        {{ teamRoleAdmin }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="team.plan_subscription" class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <CalendarIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                                <p>
                                    {{ team.plan_subscription.is_active ? 'Ends at' : 'Ended at' }}
                                    {{ ' ' }}
                                    <time :datetime="team.plan_subscription.valid_until">
                                        {{ toLocaleDate(team.plan_subscription.valid_until, 'en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
                                    </time>
                                </p>
                            </div>
                        </div>
                    </div>
                </Link>
            </li>
        </ul>

<!--        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">-->
<!--            <div class="flex flex-1 justify-between sm:hidden">-->
<!--                <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>-->
<!--                <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>-->
<!--            </div>-->
<!--            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">-->
<!--                <div>-->
<!--                    <p class="text-sm text-gray-700">-->
<!--                        Showing-->
<!--                        {{ ' ' }}-->
<!--                        <span class="font-medium">1</span>-->
<!--                        {{ ' ' }}-->
<!--                        to-->
<!--                        {{ ' ' }}-->
<!--                        <span class="font-medium">10</span>-->
<!--                        {{ ' ' }}-->
<!--                        of-->
<!--                        {{ ' ' }}-->
<!--                        <span class="font-medium">97</span>-->
<!--                        {{ ' ' }}-->
<!--                        results-->
<!--                    </p>-->
<!--                </div>-->
<!--                <div>-->
<!--                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">-->
<!--                        <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">-->
<!--                            <span class="sr-only">Previous</span>-->
<!--                            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />-->
<!--                        </a>-->
<!--                        &lt;!&ndash; Current: "z-10 bg-tf-blue-500 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-500", Default: "text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0" &ndash;&gt;-->
<!--                        <a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-tf-blue-500 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-500">1</a>-->
<!--                        <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">2</a>-->
<!--                        <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex">3</a>-->
<!--                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>-->
<!--                        <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex">8</a>-->
<!--                        <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">9</a>-->
<!--                        <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">10</a>-->
<!--                        <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">-->
<!--                            <span class="sr-only">Next</span>-->
<!--                            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />-->
<!--                        </a>-->
<!--                    </nav>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>


<!--    <div class="bg-white overflow-hidden shadow sm:rounded-tl-md sm:rounded-tr-md">-->
<!--        <div class="px-4 sm:px-6 lg:px-8">-->
<!--            <div class="mt-3 flow-root">-->
<!--                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">-->
<!--                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">-->
<!--                        <table class="min-w-full divide-y divide-gray-200">-->
<!--                            <thead>-->
<!--                            <tr>-->
<!--                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-medium leading-6 text-gray-900 sm:pl-0">Name</th>-->
<!--                                <th scope="col" class="px-3 py-3.5 text-left text-left text-sm font-medium leading-6 text-gray-900">Members</th>-->
<!--                                <th scope="col" class="px-3 py-3.5 text-left text-left text-sm font-medium leading-6 text-gray-900">Status</th>-->
<!--                                <th scope="col" class="px-3 py-3.5 text-left text-left text-sm font-medium leading-6 text-gray-900">Subscription</th>-->

<!--                            </tr>-->
<!--                            </thead>-->
<!--                            <tbody class="divide-y divide-gray-200 bg-white">-->
<!--                            <tr v-for="person in people" :key="person.email">-->
<!--                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-0">-->
<!--                                    <div class="text-gray-900">-->
<!--                                        <Link href="#" class="font-medium text-gray-800 hover:text-gray-600">-->
<!--                                            SolidBrain-->
<!--                                        </Link>-->
<!--                                    </div>-->

<!--                                </td>-->
<!--                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">-->
<!--                                    <div class="py-3">-->
<!--                                        <div class="font-medium text-gray-900">{{ person.name }}</div>-->
<!--                                        <div class="text-gray-500">{{ person.email }}</div>-->
<!--                                    </div>-->
<!--                                    <div class="py-3">-->
<!--                                        <div class="font-medium text-gray-900">{{ person.name }}</div>-->
<!--                                        <div class="text-gray-500">{{ person.email }}</div>-->
<!--                                    </div>-->
<!--                                </td>-->
<!--                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">-->
<!--                                    <PrimaryBadge> Active </PrimaryBadge>-->
<!--                                </td>-->
<!--                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">-->
<!--                                    <DangerBadge> Expired </DangerBadge>-->
<!--                                </td>-->
<!--                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">-->
<!--                                    <PrimaryButton>Switch</PrimaryButton>-->
<!--                                </td>-->
<!--                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">-->
<!--                                    <DangerSecondaryButton>Disable</DangerSecondaryButton>-->
<!--                                </td>-->
<!--                            </tr>-->
<!--                            </tbody>-->
<!--                        </table>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="flex items-center px-4 py-3 bg-gray-50 justify-center sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">-->
<!--        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">-->
<!--            <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">-->
<!--                <span class="sr-only">Previous</span>-->
<!--                <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />-->
<!--            </a>-->
<!--            &lt;!&ndash; Current: "z-10 bg-tf-blue-500 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-500", Default: "text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0" &ndash;&gt;-->
<!--            <a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-tf-blue-500 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-500">1</a>-->
<!--            <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">2</a>-->
<!--            <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex">3</a>-->
<!--            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>-->
<!--            <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex">8</a>-->
<!--            <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">9</a>-->
<!--            <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">10</a>-->
<!--            <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">-->
<!--                <span class="sr-only">Next</span>-->
<!--                <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />-->
<!--            </a>-->
<!--        </nav>-->
<!--    </div>-->
</template>
