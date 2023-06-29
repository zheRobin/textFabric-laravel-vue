<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import CreateApiTokens from "../Components/CreateApiTokens.vue";
import ManageAPITokens from "../Components/ManageAPITokens.vue";

const props = defineProps({
    items: Object,
});

</script>



<template>
    <AppLayout title="Api">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                API
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="bg-grey shadow sm:rounded-lg">
                    <CreateApiTokens/>
                    <hr/>
                    <ManageAPITokens :data="props.items"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
