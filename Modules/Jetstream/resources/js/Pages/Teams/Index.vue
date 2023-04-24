<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import TeamsList from "Jetstream/Pages/Teams/Partials/TeamsList.vue";

defineProps({
    teams: Object,
    filters: Object,
    subscriptionPlans: Array,
})
</script>

<template>
    <AppLayout title="Teams">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Teams
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <TeamsList :teams="teams" :subscriptionPlans="subscriptionPlans" :filters="filters"/>
            </div>
        </div>
    </AppLayout>
</template>
