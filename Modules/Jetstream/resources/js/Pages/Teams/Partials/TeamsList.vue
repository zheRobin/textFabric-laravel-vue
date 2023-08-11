<script setup>
import { CalendarIcon, ClipboardDocumentListIcon } from '@heroicons/vue/20/solid';
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";
import { teamRoleAdmin } from "Jetstream/teams.js";
import { toLocaleDate } from "Modules/Subscriptions/resources/js/subscriptions";
import {Link, useForm} from "@inertiajs/vue3";
import { EnvelopeIcon, UserIcon } from "@heroicons/vue/24/outline";
import TeamsFilter from "Jetstream/Pages/Teams/Partials/TeamsFilter.vue";
import Pagination from "Jetstream/Components/Pagination.vue";
import SuccessBadge from "Jetstream/Components/SuccessBadge.vue";
import InfoBadge from "Jetstream/Components/InfoBadge.vue";

const props = defineProps({
    teams: Object,
    filters: Object,
    subscriptionPlans: Array,
});

const form = useForm({
    filters: {
        search: '',
        plan: [],
        subscribed: Boolean,
        expired: Boolean,
        trial: Boolean,
        sort: String,
    }
});

const getAdminsFromUsers = (users) => {
    return users.filter( (user) => user.membership.role === teamRoleAdmin );
};

const filterTeams = () => {
    form.get(route('teams.index'));
};

const sort = (sortBy) => {
    form.filters.sort = sortBy;
    filterTeams();
};

const fillFilters = () => {
    Object.entries(props.filters).forEach((filter) => {
       const [key, value] = filter;

       if (form.filters.hasOwnProperty(key)) {
            form.filters[key] = value;
       }
    });
}

const openAIFeatureSlug = 'openai-requests';
const openAIUsage = (team) => {
    const usage = team.plan_subscription.usage.find((el) => el.feature.slug === openAIFeatureSlug);

    if (usage) {
        return usage.used;
    }

    return 0;
}

fillFilters();
</script>

<template>
    <TeamsFilter class="shadow sm:rounded-t mb-1" :filters="form.filters" :subscriptionPlans="subscriptionPlans" @submitted="filterTeams" @sorted="sort"/>

    <div class="overflow-hidden bg-white shadow">
        <ul role="list" class="divide-y divide-gray-200">
            <li v-for="team in teams.data" :key="team.id">
                <Link :href="route('teams.show', team)" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="truncate text-sm font-medium text-tf-blue-500">{{ team.name }}</p>
                            <div v-if="team.plan_subscription" class="ml-2 flex flex-shrink-0">
                                <SuccessBadge class="mr-2">{{ team.plan_subscription.plan.name }}</SuccessBadge>
                                <InfoBadge class="mr-2" v-if="team.plan_subscription.on_trial">Trial</InfoBadge>
                                <PrimaryBadge v-if="team.plan_subscription.is_active">Subscribed</PrimaryBadge>
                                <DangerBadge v-else>Expired</DangerBadge>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <div class="flex-col">
                                    <p class="text-sm flex font-medium text-gray-600">
                                        <EnvelopeIcon class="w-5 mr-1"/>
                                        {{ team.owner.email }}
                                    </p>
                                    <p class="truncate flex text-sm text-gray-500">
                                        <UserIcon class="w-5 mr-1 text-gray-700" />
                                        {{$t('Owner')}}
                                    </p>
                                </div>

                                <div class="flex-col sm:ml-5" v-for="member in getAdminsFromUsers(team.users)">
                                    <p class="text-sm flex font-medium text-gray-600">
                                        <EnvelopeIcon class="w-5 mr-1"/>
                                        {{ member.email }}
                                    </p>
                                    <p class="truncate flex text-sm text-gray-500 capitalize">
                                        <UserIcon class="w-5 mr-1 text-gray-700" />
                                        {{ teamRoleAdmin }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="team.plan_subscription" class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <ClipboardDocumentListIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                                <p>
                                    {{ `OpenAI: ${openAIUsage(team)} requests` }}
                                </p>
                            </div>
                            <div v-if="team.plan_subscription" class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <CalendarIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                                <p>
                                    {{ team.plan_subscription.is_active ? 'Ends at' : 'Ended at' }}
                                    {{ ' ' }}
                                    <time :datetime="team.plan_subscription.ends_at">
                                        {{ toLocaleDate(team.plan_subscription.ends_at, 'en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
                                    </time>
                                </p>
                            </div>
                        </div>
                    </div>
                </Link>
            </li>
            <li v-if="!teams.total" class="px-4 py-4 sm:px-6 text-center">
                <p class="truncate font-medium text-gray-600">{{$t('Empty Data...')}}</p>
            </li>
        </ul>
    </div>

    <div class="border-t border-gray-200 bg-white">
        <Pagination :links="teams.links" class="shadow sm:rounded-b"/>
    </div>
</template>
