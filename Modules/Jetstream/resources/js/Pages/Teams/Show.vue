<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import DeleteTeamForm from 'Jetstream/Pages/Teams/Partials/DeleteTeamForm.vue';
import SectionBorder from 'Jetstream/Components/SectionBorder.vue';
import TeamMemberManager from 'Jetstream/Pages/Teams/Partials/TeamMemberManager.vue';
import UpdateTeamNameForm from 'Jetstream/Pages/Teams/Partials/UpdateTeamNameForm.vue';
import ToggleDisabledTeamForm from "Jetstream/Pages/Teams/Partials/ToggleDisabledTeamForm.vue";
import ActionPanel from "Jetstream/Components/ActionPanel.vue";
import {subscriptionContactLink} from "Modules/Subscriptions/resources/js/subscriptions";
import {ExclamationTriangleIcon} from "@heroicons/vue/24/outline";
import UpdateSubscriptionForm from "Jetstream/Pages/Teams/Partials/UpdateSubscriptionForm.vue";
import SwitchTeamForm from "Jetstream/Pages/Teams/Partials/SwitchTeamForm.vue";
import ShowSubscriptionInfo from "Jetstream/Pages/Profile/Partials/ShowSubscriptionInfo.vue";
import {usePage} from "@inertiajs/vue3";

defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
    subscriptionPlans: Array,
});

const page = usePage();
</script>

<template>
    <AppLayout title="Team Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{$t('Team Settings')}}
            </h2>
        </template>

        <div>

            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <template v-if="team.plan_subscription">
                    <ShowSubscriptionInfo :planSubscription="team.plan_subscription" :collectionCount="page.props.team.collections.length" />

                    <SectionBorder />
                </template>
                <template v-if="team.disabled">
                    <ActionPanel class="mb-10">
                        <template #header>
                            <p class="flex"><ExclamationTriangleIcon class="w-5 mr-1 text-red-900" />{{$t('Team is banned')}}</p>
                        </template>

                        <template #body>
                            <p>{{$t('Please')}} <a :href="subscriptionContactLink" class="font-medium text-tf-blue-500 hover:text-tf-blue-400"> {{$t('contact us')}}</a> {{$t('to find out the reason or switch to another team.')}}</p>
                        </template>
                    </ActionPanel>
                </template>
                <div class="mt-10 sm:mt-0">
                    <UpdateTeamNameForm :team="team" :permissions="permissions" />
                </div>

                <div class="mt-10 sm:mt-0">
                    <TeamMemberManager
                        :team="team"
                        :available-roles="availableRoles"
                        :user-permissions="permissions"
                    />
                </div>
                <div class="mt-10 sm:mt-0">

                <template v-if="page.props.auth.user.is_admin && team.plan_subscription">
                    <SectionBorder />

                    <UpdateSubscriptionForm :planSubscription="team.plan_subscription" :plans="subscriptionPlans" :collectionCount="page.props.team.collections.length" />
                </template>
                </div>
                <template v-if="permissions.canSwitchTeam">
                    <SectionBorder />

                    <div class="mt-10 sm:mt-0">
                        <SwitchTeamForm :team="team" :user="page.props.auth.user" />
                    </div>
                </template>

                <template v-if="permissions.canToggleDisabled">
                    <div class="mt-10 sm:mt-0">

                    <SectionBorder />

                        <ToggleDisabledTeamForm :team="team" />
                    </div>
                </template>

                <template v-if="permissions.canDeleteTeam && ! team.personal_team">
                    <div class="mt-10 sm:mt-0">

                        <SectionBorder />

                        <DeleteTeamForm class="mt-10 sm:mt-0" :team="team" />
                    </div>

                </template>
            </div>
        </div>
    </AppLayout>
</template>
