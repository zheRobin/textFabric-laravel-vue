<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from "Jetstream/Layouts/AuthLayout.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import InputError from "Jetstream/Components/InputError.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Forgot Password">
        <template #logo>
            <img class="h-12 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company" />
        </template>

        <template #header>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">{{ $t('Forgot your password?') }}</h2>
            <p class="mt-2 text-gray-600">
                {{$t('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.')}}
            </p>
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" :value="$t('Email')" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-2 w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="w-full">
                    {{ $t('Email Password Reset Link') }}
                </PrimaryButton>
            </div>
        </form>

        <template #second-screen>
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1505904267569-f02eaeb45a4c?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1908&q=80" alt="" />
        </template>
    </AuthLayout>
</template>
