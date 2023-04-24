<script setup>
import {subscriptionContactLink} from "Modules/Subscriptions/resources/js/subscriptions";
import {daysLeft} from "Modules/Subscriptions/resources/js/subscriptions";

const props = defineProps({
    'planSubscription': Object,
});
</script>

<template>
    <div v-if="planSubscription.is_active">
        <div v-if="planSubscription.on_trial" class="flex bg-yellow-200 gap-x-6 px-2 py-2.5 justify-center">
            <a class="text-xs text-yellow-900 leading-6 lg:text-sm" :href="subscriptionContactLink">
                <span v-if="daysLeft(planSubscription.ends_at) <= 1">
                    Your trial period ends <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) === 0 ? 'today': 'tomorrow' }}</span><span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to switch to a subscription that suits you the best</span> <span aria-hidden="true">&rarr;</span>
                </span>
                <span v-else>
                    You're currently on a free trial with <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) }}</span> days left<span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to switch to a subscription that suits you the best</span> <span aria-hidden="true">&rarr;</span>
                </span>
            </a>
        </div>
        <div v-else>
            <div v-if="daysLeft(planSubscription.ends_at) <= 30" class="flex bg-yellow-200 gap-x-6 px-2 py-2.5 justify-center">
                <a class="text-xs text-yellow-900 leading-6 lg:text-sm" :href="subscriptionContactLink">
                    <span v-if="daysLeft(planSubscription.ends_at) <= 1">
                        Your current subscription plan ends <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) === 0 ? 'today': 'tomorrow' }}</span><span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to renew</span> <span aria-hidden="true">&rarr;</span>
                    </span>
                    <span v-else>
                        Your current subscription plan ends in <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) }}</span> days<span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to renew</span> <span aria-hidden="true">&rarr;</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <div v-else class="flex bg-red-100 gap-x-6 px-2 py-2.5 justify-center">
        <a class="text-xs text-yellow-900 leading-6 lg:text-sm" :href="subscriptionContactLink">
            Your subscription has <span class="font-semibold">expired</span><span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to renew</span> <span aria-hidden="true">&rarr;</span>
        </a>
    </div>
</template>
