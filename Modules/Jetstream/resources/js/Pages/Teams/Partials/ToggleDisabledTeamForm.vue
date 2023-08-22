<script setup>
import ActionSection from "Jetstream/Components/ActionSection.vue";
import DangerSecondaryButton from "Jetstream/Components/DangerSecondaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import ActionMessage from "Jetstream/Components/ActionMessage.vue";

const props = defineProps({
    team: Object,
});

const form = useForm({
    disabled: null
});

const disableTeam = () => {
    form.disabled = true;
    updateTeam();
}

const enableTeam = () => {
    form.disabled = false;
    updateTeam();
}

const updateTeam = () => {
    form.put(route('teams.toggle-disabled', props.team),{
        errorBag: 'toggleTeam',
        preserveScroll: true,
    })
}
</script>

<template>
    <ActionSection>
        <template #title>
            {{ team.disabled ? $t('Enable Team') : $t('Disable Team') }}
        </template>

        <template #description>
            {{ $t(team.disabled ? 'Enable this team.' : 'Disable this team.') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                {{ $t(team.disabled ? 'Once a team is enabled, all of its resources and data will be enabled also. Access will be granted again.' : 'Once a team is disabled, all of its resources and data will be disabled also. Access will be restricted.') }}
            </div>

            <div class="mt-5 flex items-center">
                <PrimaryButton v-if="team.disabled" @click="enableTeam" class="px-4">
                    {{ $t('Enable Team') }}
                </PrimaryButton>
                <DangerSecondaryButton v-else @click="disableTeam" class="px-4">
                    {{ $t('Disable Team') }}
                </DangerSecondaryButton>
                <ActionMessage :on="form.recentlySuccessful" class="ml-3">
                    {{ $t('Changed.') }}
                </ActionMessage>
            </div>
        </template>
    </ActionSection>
</template>
