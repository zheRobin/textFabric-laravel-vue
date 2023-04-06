<script setup>
import ActionSection from "Jetstream/Components/ActionSection.vue";
import SubscriptionInfo from "Modules/Subscriptions/resources/js/Components/SubscriptionInfo.vue";

</script>

<template>
    <ActionSection>
        <template #title>
            Subscription plan
        </template>

        <template #description>
            Applicable to all team members
        </template>

        <template #content>
            <SubscriptionInfo />
        </template>
    </ActionSection>
</template>
