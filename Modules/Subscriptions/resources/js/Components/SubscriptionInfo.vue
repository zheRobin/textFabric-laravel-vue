<script setup>
import {CheckIcon} from "@heroicons/vue/20/solid";
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";
import { toLocaleDate } from "Modules/Subscriptions/resources/js/subscriptions";

defineProps({
    'planSubscription': Object,
})
</script>

<template>
    <div>
        <InputLabel>Status</InputLabel>
        <PrimaryBadge v-if="planSubscription.is_active" class="mt-1">Active</PrimaryBadge>
        <DangerBadge v-else>Expired</DangerBadge>

        <InputLabel class="mt-5">Plan Type</InputLabel>
        <p class="flex-1 mt-1 text-base uppercase font-semibold">
            <span v-if="planSubscription.active_trial && !planSubscription.active_invoice" class="text-gray-400 capitalize">(Trial)</span> <span class="text-tf-blue-800">Pro</span>
        </p>

        <InputLabel class="mt-5">Plan Features</InputLabel>
        <ul role="list" class="mt-1 space-y-3 text-sm leading-6 text-gray-600">
            <li v-for="(feature, key) in planSubscription.plan.features" class="flex gap-x-3">
                <CheckIcon class="h-6 w-5 flex-none text-tf-blue-600" aria-hidden="true" />
                {{ feature.description }}
            </li>
        </ul>

        <InputLabel class="mt-5">{{ planSubscription.is_active ? 'Ends at' : 'Ended at' }}</InputLabel>
        <time class="flex mt-q mt-1 text-gray-800 italic text-sm" :datetime="planSubscription.valid_until">
            {{ toLocaleDate(planSubscription.valid_until, 'en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) }}
        </time>
    </div>
</template>
