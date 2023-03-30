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
            <InputLabel for="first_name" value="First Name" />
            <TextInput
                id="first_name"
                v-model="props.form.first_name"
                type="text"
                class="mt-2"
                required
                autofocus
                autocomplete="given-name"
            />
            <InputError class="mt-2" :message="props.form.errors.first_name" />
        </div>

        <div class="space-y-1">
            <InputLabel for="last_name" value="Last Name" />
            <TextInput
                id="last_name"
                v-model="props.form.last_name"
                type="text"
                class="mt-2"
                required
                autocomplete="family-name"
            />
            <InputError class="mt-2" :message="props.form.errors.last_name" />
        </div>

        <div class="space-y-1">
            <InputLabel for="position" value="Position" />
            <TextInput
                id="position"
                v-model="form.position"
                type="text"
                class="mt-2"
                required
                autocomplete="organization-title"
            />
            <InputError class="mt-2" :message="form.errors.position" />
        </div>
    </div>
</template>
