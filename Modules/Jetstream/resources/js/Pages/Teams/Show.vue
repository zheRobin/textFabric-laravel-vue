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

defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
});
</script>

<template>
    <AppLayout title="Team Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Team Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <template v-if="team.disabled && $page.props.auth.user.is_common_user">
                    <ActionPanel class="mb-10">
                        <template #header>
                            <p class="flex"><ExclamationTriangleIcon class="w-5 mr-1 text-red-900" />Team is banned</p>
                        </template>

                        <template #body>
                            <p>Please <a :href="subscriptionContactLink" class="font-medium text-tf-blue-500 hover:text-tf-blue-400"> contact us</a> to find out the reason or switch to another team.</p>
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
