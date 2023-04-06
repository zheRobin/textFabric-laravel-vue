<script setup>
import {CheckIcon} from "@heroicons/vue/20/solid";
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";

const planSubscription = computed(() => usePage().props.planSubscription);

const toLocaleDate = (date, locale, options) => {
    return new Date(date).toLocaleDateString('en-US', options);
}
</script>

<template>
    <div>
        <InputLabel>Status</InputLabel>
        <PrimaryBadge v-if="planSubscription.is_active" class="mt-1 ml-5">Active</PrimaryBadge>
        <DangerBadge v-else>Expired</DangerBadge>

        <InputLabel class="mt-5">Plan Type</InputLabel>
        <p class="flex-1 ml-5 mt-1 text-base uppercase font-semibold">
            <span v-if="planSubscription.active_trial" class="text-gray-400 capitalize">(Trial)</span> <span class="text-tf-blue-800">Pro</span>
        </p>

        <InputLabel class="mt-5">Plan Features</InputLabel>
        <ul role="list" class="mt-1 space-y-3 text-sm leading-6 text-gray-600">
            <li v-for="(feature, key) in planSubscription.plan.features" class="flex ml-5 gap-x-3">
                <CheckIcon class="h-6 w-5 flex-none text-tf-blue-600" aria-hidden="true" />
                {{ feature.description }}
            </li>
        </ul>

        <InputLabel class="mt-5">{{ planSubscription.is_active ? 'Ends at' : 'Ended at' }}</InputLabel>
        <time v-if="planSubscription.active_trial" class="flex mt-q mt-1 ml-5 text-gray-800 italic text-sm" :datetime="planSubscription.trial_ends_at">
            {{ toLocaleDate(planSubscription.trial_ends_at, 'en_US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
        </time>

        <time v-if="planSubscription.active_invoice && !planSubscription.active_trial" class="flex mt-q mt-1 ml-5 text-gray-800 italic text-sm" :datetime="planSubscription.ends_at">
            {{ toLocaleDate(planSubscription.ends_at, 'en_US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
        </time>

<!--    TODO: add ended date; refactor to use start/end depend on active trial/invoice and not active_trial/active_invoice    -->
    </div>
</template>
