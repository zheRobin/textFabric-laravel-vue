<script setup>
import {usePage} from "@inertiajs/vue3";

const planSubscription = usePage().props.auth.user.current_team.plan_subscription;

const activeTrial = planSubscription.active_trial;
const activeInvoice = planSubscription.active_invoice && !activeTrial;
const expired = !activeInvoice && !activeTrial;

const contactLink = 'https://www.textfabrik.io/kontakt/';

const daysLeft = (date) => {
    const total = Date.parse(date) - Date.parse(new Date().toISOString());

    return  Math.floor(total/(1000*60*60*24));
}
</script>

<template>
    <div :class="{ 'bg-yellow-200': activeTrial || activeInvoice, 'bg-rose-200': expired }" class="flex gap-x-6 px-2 py-2.5 justify-center">
        <a v-if="activeTrial" class="text-xs text-gray-600 leading-6 lg:text-sm" :href="contactLink">
            <span v-if="daysLeft(planSubscription.trial_ends_at) <= 1">
                Your trial period ends <span class="font-semibold">{{ daysLeft(planSubscription.trial_ends_at) === 0 ? 'today': 'tomorrow' }}</span>. <span class="hidden md:inline">Please <span class="font-semibold">contact us </span>to switch to a subscription that suits you the best </span><span aria-hidden="true">&rarr;</span>
            </span>
            <span v-else>
                You're currently on a free trial with <span class="font-semibold">{{ daysLeft(planSubscription.trial_ends_at) }}</span> days left. <span class="hidden md:inline">Please <span class="font-semibold">contact us </span>to switch to a subscription that suits you the best </span><span aria-hidden="true">&rarr;</span>
            </span>
        </a>

        <a v-if="activeInvoice && daysLeft(planSubscription.ends_at) <= 30" class="text-xs text-gray-600 leading-6 lg:text-sm" :href="contactLink">
            <span v-if="daysLeft(planSubscription.ends_at) <= 1">
                Your current subscription plan ends <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) === 0 ? 'today': 'tomorrow' }}</span>. <span class="hidden md:inline">Please <span class="font-semibold">contact us </span>to renew </span><span aria-hidden="true">&rarr;</span>
            </span>
            <span v-else>
                Your current subscription plan ends in <span class="font-semibold">{{ daysLeft(planSubscription.ends_at) }}</span> days. <span class="hidden md:inline">Please <span class="font-semibold">contact us </span>to renew </span><span aria-hidden="true">&rarr;</span>
            </span>
        </a>

        <a v-if="!activeTrial && !activeInvoice" class="text-xs text-gray-600 leading-6 lg:text-sm" :href="contactLink">
            Your subscription has <span class="font-semibold">expired</span>. Please <span class="font-semibold">contact us</span> to renew.<span aria-hidden="true">&rarr;</span>
        </a>
    </div>
</template>
