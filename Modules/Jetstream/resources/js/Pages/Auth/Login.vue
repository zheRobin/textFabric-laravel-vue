<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthLayout from "Jetstream/Layouts/AuthLayout.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import Checkbox from "Jetstream/Components/Checkbox.vue";
import InputError from "Jetstream/Components/InputError.vue";
import TextInput from "Jetstream/Components/TextInput.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout title="Sign in">
        <template #header>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Sign in to your account</h2>
            <p class="mt-2 text-gray-600">
                Or
                {{ ' ' }}
                <Link :href="route('register')" class="font-medium text-tf-blue-500 hover:text-tf-blue-400">
                    create one
                </Link>
            </p>
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email address" />
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
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <Checkbox
                        id="remember-me"
                        v-model:checked="form.remember"
                        name="remember"
                        class="h-4 w-4 rounded border-gray-300 text-tf-blue-600 focus:ring-tf-blue-600"
                    />
                    <InputLabel for="remember-me" value="Remember me" class="ml-2 font-normal leading-5 text-gray-90" />
                </div>

                <div class="text-sm">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="font-medium text-tf-blue-500 hover:text-tf-blue-400">
                        Forgot your password?
                    </Link>
                </div>
            </div>

            <div>
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="w-full">
                    Sign in
                </PrimaryButton>
            </div>
        </form>

        <template #second-screen>
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1505904267569-f02eaeb45a4c?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1908&q=80" alt="" />
        </template>
    </AuthLayout>
</template>
