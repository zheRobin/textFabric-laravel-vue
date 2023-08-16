<script setup>
import AppLayout from 'Jetstream/Layouts/AppLayout.vue';
import DeleteUserForm from 'Jetstream/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from 'Jetstream/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from 'Jetstream/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from 'Jetstream/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from 'Jetstream/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from 'Jetstream/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import ShowSubscriptionInfo from "Jetstream/Pages/Profile/Partials/ShowSubscriptionInfo.vue";

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t('Profile') }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.planSubscription">
                    <ShowSubscriptionInfo :planSubscription="$page.props.planSubscription" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />

                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />

                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
