<script setup>
import {CheckIcon} from "@heroicons/vue/20/solid";
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";
import { toLocaleDate } from "Modules/Subscriptions/resources/js/subscriptions";

const props = defineProps({
    planSubscription: Object
})

const getUsage = (feature) => {
    return props.planSubscription.usage.find((el) => el.feature_id === feature.id);
}

const getFeatureUsage = (feature) => {
    const usage = getUsage(feature);

    if (getUsage(feature)) {
        return usage.used;
    }

    return false;
}
</script>

<template>
    <div>
        <InputLabel>Status</InputLabel>
        <PrimaryBadge v-if="planSubscription.is_active" class="mt-1">Active</PrimaryBadge>
        <DangerBadge v-else>Expired</DangerBadge>

        <InputLabel class="mt-5">Plan Type</InputLabel>
        <p class="flex-1 mt-1 text-base uppercase font-semibold">
            <span v-if="planSubscription.on_trial" class="text-gray-400 capitalize">(Trial)</span>
            {{ ' ' }}
            <span class="text-tf-blue-800">{{ planSubscription.plan.name }}</span>
        </p>

        <InputLabel class="mt-5">Plan Features</InputLabel>
        <ul role="list" class="mt-1 space-y-3 text-sm leading-6 text-gray-600">
            <li v-for="(feature, key) in planSubscription.plan.features" class="flex gap-x-3">
                <CheckIcon class="h-6 w-5 flex-none text-tf-blue-600" aria-hidden="true" />
                <span>{{ feature.description }}</span>
                {{ ' ' }}
                <span v-if="feature.slug !== 'collection-items-limit' && feature.slug !== 'openai-params'" class="font-semibold text-gray-500">
                    {{ `(used: ${getFeatureUsage(feature) ? getFeatureUsage(feature) : 0})` }}
                </span>
            </li>
        </ul>

        <InputLabel class="mt-5">{{ planSubscription.is_active ? 'Ends at' : 'Ended at' }}</InputLabel>
        <time class="flex mt-q mt-1 text-gray-800 italic text-sm" :datetime="planSubscription.ends_at">
            {{ toLocaleDate(planSubscription.ends_at, 'en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
        </time>
    </div>
</template>
