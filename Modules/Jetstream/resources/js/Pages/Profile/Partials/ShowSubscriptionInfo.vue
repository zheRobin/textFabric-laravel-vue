<script setup>
import ActionSection from "Jetstream/Components/ActionSection.vue";
import SubscriptionInfo from "Modules/Subscriptions/resources/js/Components/SubscriptionInfo.vue";

defineProps({
    'planSubscription': Object,
})
</script>

<template>
    <ActionSection>
        <template #title>
            {{$t('Subscription plan')}}
        </template>

        <template #description>
            {{$t('Applicable to all team members.')}}
        </template>

        <template #content>
            <SubscriptionInfo :planSubscription="planSubscription" />
        </template>
    </ActionSection>
</template>
