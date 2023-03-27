<script setup>
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useScriptTag} from "@vueuse/core";
import DropdownInput from "../../Components/DropdownInput.vue";

const form = useForm({
    first_name: '',
    last_name: '',
    position: '',
    company: '',
    employees: '',
    email: '',
    phone_number: '',
    password: '',
    google_recaptcha: '',
    terms: false,
});

const recaptchaSiteKey = usePage().props.googleRecaptchaSiteKey;

useScriptTag(`https://www.google.com/recaptcha/api.js?render=${recaptchaSiteKey}`);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const reCaptcha = () => {
    grecaptcha.ready(function() {
        grecaptcha.execute(recaptchaSiteKey, {action: 'register'}).then(function(token) {
            form.google_recaptcha = token;
            submit();
        });
    });
}
</script>

<template>
    <Head>
        <title>Register</title>
    </Head>

    <AuthenticationCard class="sm:pt-10 sm:pb-10">
        <template #logo>
            <AuthenticationCardLogo class="mt-5" />
        </template>

        <form @submit.prevent="reCaptcha">
            <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full"
                    autofocus
                    autocomplete="given-name"
                />
                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="last_name" value="Last Name" />
                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="family-name"
                />
                <InputError class="mt-2" :message="form.errors.last_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="position" value="Position" />
                <TextInput
                    id="position"
                    v-model="form.position"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="organization-title"
                />
                <InputError class="mt-2" :message="form.errors.position" />
            </div>

            <div class="mt-4">
                <InputLabel for="company" value="Company" />
                <TextInput
                    id="company"
                    v-model="form.company"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="organization"
                />
                <InputError class="mt-2" :message="form.errors.company" />
            </div>

            <div class="mt-4">
                <InputLabel for="employees" value="Employees" />
                <DropdownInput :options="['1-10', '11-20', '21-99', '100-350', '351-1.000', '1.001-1.999', '2.000+']" v-model="form.employees"/>
                <InputError class="mt-2" :message="form.errors.employees" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone_number" value="Phone Number" />
                <TextInput
                    id="phone_number"
                    v-model="form.phone_number"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.phone_number" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ml-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <InputError class="mt-2" :message="form.errors.google_recaptcha" />

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Already registered?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
