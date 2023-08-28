<script setup>
import {Link, useForm, usePage} from "@inertiajs/vue3";
import AuthLayout from "Jetstream/Layouts/AuthLayout.vue";
import {useScriptTag} from "@vueuse/core";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import PersonalInfoStep from "Jetstream/Pages/Auth/Partials/PersonalInfoStep.vue";
import OrganizationInfoStep from "Jetstream/Pages/Auth/Partials/OrganizationInfoStep.vue";
import {reactive, ref, shallowRef} from "vue";
import UserInfoStep from "Jetstream/Pages/Auth/Partials/UserInfoStep.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";
import StepBullets from "Jetstream/Components/StepBullets.vue";
import ApplicationLogo from "Jetstream/Components/ApplicationLogo.vue";

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
    // TODO: refactor (remove from form and make auto detection of current lang)
    locale: localStorage.getItem('locale')
});

const recaptchaSiteKey = usePage().props.googleRecaptchaSiteKey;

useScriptTag(`https://www.google.com/recaptcha/api.js?render=${recaptchaSiteKey}`);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onError: () => firstErrorStep(),
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

const steps = reactive([
    {
        component: shallowRef(PersonalInfoStep),
        props: {
            first_name: form.first_name,
            last_name: form.last_name,
            position: form.position,
        }
    },
    {
        component: shallowRef(OrganizationInfoStep),
        props: {
            company: form.company,
            employees: form.employees,
            phone_number: form.phone_number,
        }
    },
    {
        component: shallowRef(UserInfoStep),
        props: {
            email: form.email,
            password: form.password,
        }
    }
]);

let currentIndex = ref(0);
let currentComponent = shallowRef(PersonalInfoStep);

let totalSteps = ref(steps.length);

const nextStep = () => {
    if(currentIndex.value === 1 && !form.employees){
        form.errors.employees = 'The employees field is required.';
    }else{
        if (currentIndex.value + 1 === totalSteps.value) {
            reCaptcha();
        } else {
            switchStep(currentIndex.value + 1);
        }
    }
}

const prevStep = () => {
    switchStep(currentIndex.value - 1);
}

const switchStep = (index) => {
    if (index <= (totalSteps.value - 1) && index >= 0) {
        currentIndex.value = index;
        currentComponent.value = steps[index].component;
    }
}

const firstErrorStep = () => {
    steps.some(step => {
        const stepHasError = Object.keys(form.errors).some(key => Object.keys(step.props).includes(key));
        if (stepHasError) {
            currentComponent.value = step.component;
            currentIndex.value = steps.indexOf(step);
            return stepHasError;
        }
    });
}
</script>

<template>
    <AuthLayout title="Sign up">
<!--        <template #logo>-->
<!--            <Link :href="route('dashboard')">-->
<!--                <ApplicationLogo class="block h-9 w-auto" />-->
<!--            </Link>-->
<!--        </template>-->

        <template #header>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">{{ $t('Create your account') }}</h2>
            <p class="mt-2 text-gray-600">
                {{ $t('Or') }}
                {{ ' ' }}
                <Link :href="route('login')" class="font-medium text-tf-blue-500 hover:text-tf-blue-400">
                    {{$t('sign in')}}
                </Link>
            </p>
        </template>
        <form @submit.prevent="nextStep" class="space-y-4">
            <KeepAlive>
                <component :is="currentComponent" :form="form" />
            </KeepAlive>
            <div class="mt-8 flex items-center justify-between flex-wrap-reverse gap-4">
                <div class="flex justify-start">
                    <div class="flex mr-3" v-if="currentIndex > 0">
                        <SecondaryButton type="button" class="uppercase pr-4 pl-4" @click="prevStep">{{$t('Back')}}</SecondaryButton>
                    </div>

                    <div class="flex">
                        <PrimaryButton v-if="currentIndex + 1 < totalSteps" class="uppercase pr-4 pl-4">{{ $t("Next") }}</PrimaryButton>
                        <PrimaryButton v-else class="uppercase pr-4 pl-4">{{ $t('Sign up') }}</PrimaryButton>
                    </div>
                </div>
                <StepBullets :currentStep="currentIndex + 1" :totalSteps="totalSteps"  class="flex pr-2"/>
            </div>

            <div>
                <Link v-if="currentIndex < 1" class="" :href="route('demo.compilations.index')">
                    <SecondaryButton class="w-full ring-2 ring-tf-blue-500 bg-white text-tf-blue-700 hover:bg-gray-50">
                        {{ $t('Try without login') }}
                    </SecondaryButton>
                </Link>
            </div>
        </form>

        <template #second-screen>
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1505904267569-f02eaeb45a4c?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1908&q=80" alt="" />
        </template>
    </AuthLayout>
</template>
