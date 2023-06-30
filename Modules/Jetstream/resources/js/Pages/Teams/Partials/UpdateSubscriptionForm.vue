<script setup>
import FormSection from "Jetstream/Components/FormSection.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";
import { toLocaleDate } from "Modules/Subscriptions/resources/js/subscriptions";
import TextInput from "Jetstream/Components/TextInput.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import Toggle from "Jetstream/Components/Toggle.vue";
import InputError from "Jetstream/Components/InputError.vue";
import ActionMessage from "Jetstream/Components/ActionMessage.vue";
import {reactive} from "vue";

const props = defineProps({
    'planSubscription': Object,
    'plans': Array,
});

const formatDateTime = (dateTime) => {
    if (dateTime) {
        const [date, time] = new Date(Date.parse(dateTime)).toISOString().split('T');

        return `${date}T${time.slice(0, 5)}`;
    }

    return null;
}

const form = useForm(reactive({
    ends_at: formatDateTime(props.planSubscription.ends_at),
    on_trial: props.planSubscription.on_trial,
    plan_id: props.planSubscription.plan_id,
}));

const planOptions = () => {
    const plans = [];

    props.plans.forEach((el) => {
        plans.push({value: el.id,label: el.name});
    })

    return plans;
}

const updatePlanSubscription = () => {
    form.put(route('plan-subscriptions.update', props.planSubscription.id), {
        errorBag: 'updatePlanSubscription',
        preserveScroll: true,
    });
}
</script>

<template>
    <FormSection @submitted="updatePlanSubscription">
        <template #title>
            Subscription Info
        </template>

        <template #description>
            Manage team subscription.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel>Status</InputLabel>
                <PrimaryBadge v-if="planSubscription.is_active" class="mt-1">Active</PrimaryBadge>
                <DangerBadge v-else>Expired</DangerBadge>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <SelectMenu v-model="form.plan_id" :options="planOptions()" class="mt-1" label="Plan type"/>
                <InputError :message="form.errors.plan_id" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="ends_at" value="Ends at" />
                <TextInput v-model="form.ends_at" class="w-full mt-2" type="datetime-local"/>
                <InputError :message="form.errors.ends_at" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <Toggle v-model="form.on_trial">
                    <template #label>
                        <span class="font-medium text-gray-900">On Trial</span>
                    </template>
                </Toggle>
                <InputError :message="form.errors.on_trial" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel class="">{{ planSubscription.is_active ? 'Ends at' : 'Ended at' }}</InputLabel>
                <time class="flex mt-q mt-1 text-gray-800 italic text-sm" :datetime="planSubscription.ends_at">
                    {{ toLocaleDate(planSubscription.ends_at, 'en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
                </time>
            </div>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Updated.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Update
            </PrimaryButton>
        </template>
    </FormSection>
</template>
