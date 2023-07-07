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
                    {{ $t('Your trial period ends') }}
                    <span class="font-semibold">
                        {{ daysLeft(planSubscription.ends_at) === 0 ? $t('today') : $t('tomorrow') }}
                    </span>
                    <span class="hidden md:inline">
                        {{ '. ' }}
                        {{$t('Please')}}
                        <span class="font-semibold">
                            {{ $t('contact us') }}
                        </span>
                        {{ $t('to switch to a subscription that suits you the best') }}
                    </span>
                    {{ ' ' }}
                    <span aria-hidden="true">&rarr;</span>
                </span>
                <span v-else>
                    {{ $t('You\'re currently on a free trial with') }}
                    <span class="font-semibold">
                        {{ `${daysLeft(planSubscription.ends_at)} ${$t('days left')}` }}
                    </span>
                    <span class="hidden md:inline">
                        {{ '. ' }}
                        {{$t("Please")}}
                        <span class="font-semibold">
                            {{ $t('contact us') }}
                        </span>
                        {{ $t('to switch to a subscription that suits you the best') }}
                    </span>
                    {{ ' ' }}
                    <span aria-hidden="true">&rarr;</span>
                </span>
            </a>
        </div>
        <div v-else>
            <div v-if="daysLeft(planSubscription.ends_at) <= 30" class="flex bg-yellow-200 gap-x-6 px-2 py-2.5 justify-center">
                <a class="text-xs text-yellow-900 leading-6 lg:text-sm" :href="subscriptionContactLink">
                    <span v-if="daysLeft(planSubscription.ends_at) <= 1">
                        {{ $t('Your current subscription plan ends') }}
                        <span class="font-semibold">
                            {{ daysLeft(planSubscription.ends_at) === 0 ? $t('today') : $t('tomorrow') }}
                        </span>
                        <span class="hidden md:inline">
                            {{ '. ' }}
                            {{ $t('Please') }}
                            <span class="font-semibold">
                                {{ $t('contact us') }}
                            </span> {{ $t('to renew') }}
                        </span>
                        {{ ' ' }}
                        <span aria-hidden="true">&rarr;</span>
                    </span>
                    <span v-else>
                        {{ $t('Your current subscription plan ends') }} {{$t('in')}}
                        <span class="font-semibold">
                            {{ `${daysLeft(planSubscription.ends_at)} ${$t('days')}` }}
                        </span>
                        <span class="hidden md:inline">
                            {{ '. ' }}
                            {{$t('Please')}}
                            <span class="font-semibold">
                                {{ $t('contact us') }}
                            </span>
                            {{ $t('to renew') }}
                        </span>
                        {{ ' ' }}
                        <span aria-hidden="true">&rarr;</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <div v-else class="flex bg-red-100 gap-x-6 px-2 py-2.5 justify-center">
        <a class="text-xs text-yellow-900 leading-6 lg:text-sm" :href="subscriptionContactLink">
            <span>
                {{ `${$t('Your subscription has')} ${$t('expired')}` }}
            </span>
            <span class="hidden md:inline">
                {{ '. ' }}
                {{ $t('Please') }}
                <span class="font-semibold">
                    {{ $t('contact us') }}
                </span>
                {{ $t('to renew') }}
            </span>
            {{ ' ' }}
            <span aria-hidden="true">&rarr;</span>
        </a>
    </div>
</template>
