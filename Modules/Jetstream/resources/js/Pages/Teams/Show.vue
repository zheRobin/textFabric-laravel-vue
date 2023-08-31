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

defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
    subscriptionPlans: Array,
});
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
                <template v-if="$page.props.planSubscription">
                    <ShowSubscriptionInfo :planSubscription="$page.props.planSubscription" />

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

                <UpdateTeamNameForm :team="team" :permissions="permissions" />

                <TeamMemberManager
                    class="mt-10 sm:mt-0"
                    :team="team"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                />



                <template v-if="$page.props.auth.user.is_admin && team.plan_subscription">
                    <SectionBorder />

                    <UpdateSubscriptionForm :planSubscription="team.plan_subscription" :plans="subscriptionPlans" />
                </template>

                <template v-if="permissions.canSwitchTeam">
                    <SectionBorder />

                    <SwitchTeamForm :team="team" :user="$page.props.auth.user" />
                </template>

                <template v-if="permissions.canToggleDisabled">
                    <SectionBorder />

                    <ToggleDisabledTeamForm :team="team" />
                </template>

                <template v-if="permissions.canDeleteTeam && ! team.personal_team">
                    <SectionBorder />

                    <DeleteTeamForm class="mt-10 sm:mt-0" :team="team" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
