<script setup>
import ActionSection from "Jetstream/Components/ActionSection.vue";
import {router} from "@inertiajs/vue3";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";

const props = defineProps({
    user: Object,
    team: Object,
});

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            Switch Team
        </template>

        <template #description>
            Switch to this team.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                After switching you will receive access to all related resources.
            </div>
            <div class="mt-5 flex items-center">
                <PrimaryButton @click="switchToTeam(team)" class="px-4" :disabled="user.current_team_id === team.id">
                    {{ user.current_team_id === team.id ? 'Switched' : 'Switch Team' }}
                </PrimaryButton>
            </div>
        </template>
    </ActionSection>
</template>
