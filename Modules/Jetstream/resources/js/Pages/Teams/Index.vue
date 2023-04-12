<script setup>
import AppLayout from "Jetstream/Layouts/AppLayout.vue";
import TeamList from "Jetstream/Pages/Teams/Partials/TeamList.vue";

defineProps({
    teams: Array,
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
                <TeamList :teams="teams" />
            </div>
        </div>
    </AppLayout>
</template>
