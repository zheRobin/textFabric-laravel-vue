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
            {{ $t('actionSection.' + (team.disabled ? 'enableTitle' : 'disableTitle')) }}
        </template>

        <template #description>
            {{ $t('actionSection.' + (team.disabled ? 'enableDescription' : 'disableDescription')) }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                {{ $t('actionSection.' + (team.disabled ? 'enableContent' : 'disableContent')) }}
            </div>

            <div class="mt-5 flex items-center">
                <PrimaryButton v-if="team.disabled" @click="enableTeam" class="px-4">
                    {{ $t('actionSection.enableButton') }}
                </PrimaryButton>
                <DangerSecondaryButton v-else @click="disableTeam" class="px-4">
                    {{ $t('actionSection.disableButton') }}
                </DangerSecondaryButton>
                <ActionMessage :on="form.recentlySuccessful" class="ml-3">
                    {{ $t('actionSection.changedMessage') }}
                </ActionMessage>
            </div>
        </template>
    </ActionSection>
</template>
