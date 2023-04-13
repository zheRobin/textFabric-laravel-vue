<script setup>
import FormSection from "Jetstream/Components/FormSection.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import DropdownInput from "Jetstream/Components/DropdownInput.vue";
import Checkbox from "Jetstream/Components/Checkbox.vue";
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";
import { toLocaleDate } from "Modules/Subscriptions/resources/js/subscriptions";
import TextInput from "Jetstream/Components/TextInput.vue";

const props = defineProps({
    'planSubscription': Object,
})
</script>

<template>
    <FormSection>
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

            <!-- Plan -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="plan" value="Plan Type" />
                <DropdownInput
                    :options="['Basic', 'Pro', 'Enterprise']"
                />
<!--                <InputError :message="form.errors.employees" class="mt-2" />-->
            </div>

            <!-- Plan -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="ends_at" value="Invoice ends at" />
                <TextInput class="w-full mt-2" type="date"/>
                <!--                <InputError :message="form.errors.employees" class="mt-2" />-->
            </div>

            <!-- Is trial -->
            <div class="flex items-center col-span-6 sm:col-span-4">
                <Checkbox
                    id="on-trial"
                    name="trial"
                    class="h-4 w-4 rounded border-gray-300 text-tf-blue-600 focus:ring-tf-blue-600"
                />
                <InputLabel for="on-trial" value="On trial" class="ml-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel class="">{{ planSubscription.is_active ? 'Ends at' : 'Ended at' }}</InputLabel>
                <time class="flex mt-q mt-1 ml-5 text-gray-800 italic text-sm" :datetime="planSubscription.valid_until">
                    {{ toLocaleDate(planSubscription.valid_until, 'en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
                </time>
            </div>


        </template>
    </FormSection>
</template>
