<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from "Jetstream/Layouts/AuthLayout.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import InputError from "Jetstream/Components/InputError.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout title="Reset Password">
        <template #logo>
            <img class="h-12 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company" />
        </template>

        <template #header>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Reset your password</h2>
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-2"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="space-y-1">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-2"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="space-y-1">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-2"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div>
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="w-full">
                    Reset password
                </PrimaryButton>
            </div>
        </form>

        <template #second-screen>
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1505904267569-f02eaeb45a4c?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1908&q=80" alt="" />
        </template>
    </AuthLayout>
</template>
