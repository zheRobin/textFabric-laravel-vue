<script setup>
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import {subscriptionContactLink} from "Modules/Subscriptions/resources/js/subscriptions";
import {daysLeft} from "Modules/Subscriptions/resources/js/subscriptions";

const props = defineProps({
    'planSubscription': Object,
})

const activeTrial = props.planSubscription.active_trial;
const activeInvoice = props.planSubscription.active_invoice;
const isActive = props.planSubscription.is_active;
const expired = !isActive;
</script>

<template>
    <div :class="{ 'bg-yellow-200': isActive, 'bg-red-100': expired }" class="flex gap-x-6 px-2 py-2.5 justify-center">
        <a class="text-xs text-yellow-900 leading-6 lg:text-sm" :href="subscriptionContactLink">
            <div v-if="isActive">
                <div v-if="activeTrial && !activeInvoice">
                    <span v-if="daysLeft(planSubscription.trial_ends_at) <= 1">
                        Your trial period ends <span class="font-semibold">{{ daysLeft(planSubscription.trial_ends_at) === 0 ? 'today': 'tomorrow' }}</span><span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to switch to a subscription that suits you the best</span> <span aria-hidden="true">&rarr;</span>
                    </span>
                    <span v-else>
                        You're currently on a free trial with <span class="font-semibold">{{ daysLeft(planSubscription.trial_ends_at) }}</span> days left<span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to switch to a subscription that suits you the best</span> <span aria-hidden="true">&rarr;</span>
                    </span>
                </div>
                <div v-else>
                    <div v-if="daysLeft(planSubscription.ends_at) <= 30">
                        <span v-if="daysLeft(planSubscription.ends_at) <= 1">
                            Your current subscription plan ends <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) === 0 ? 'today': 'tomorrow' }}</span><span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to renew</span> <span aria-hidden="true">&rarr;</span>
                        </span>
                        <span v-else>
                            Your current subscription plan ends in <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) }}</span> days<span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to renew</span> <span aria-hidden="true">&rarr;</span>
                        </span>
                    </div>
                </div>
            </div>
            <div v-else>
                Your subscription has <span class="font-semibold">expired</span><span class="hidden md:inline">. Please <span class="font-semibold">contact us </span>to renew</span> <span aria-hidden="true">&rarr;</span>
            </div>
        </a>
    </div>
</template>
