<script setup>
import InputLabel from "Jetstream/Components/InputLabel.vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import InputError from "Jetstream/Components/InputError.vue";

const props = defineProps({
    form: Object
});
</script>

<template>
    <div class="space-y-4">
        <div class="space-y-1">
            <InputLabel for="email" value="Email" />
            <TextInput
                id="email"
                autofocus
                v-model="form.email"
                type="email"
                class="mt-2 w-full"
                required
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
                class="mt-2 w-full"
                required
                autocomplete="new-password"
            />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <InputError class="mt-2" :message="form.errors.google_recaptcha" />
    </div>
</template>
