<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import UpdateAppLogoForm from "Jetstream/Pages/App/Partials/UpdateAppLogoForm.vue";


</script>

<template>
    <AppLayout title="App">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{$t('App Settings')}}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <UpdateAppLogoForm />
            </div>
        </div>
    </AppLayout>
</template>
