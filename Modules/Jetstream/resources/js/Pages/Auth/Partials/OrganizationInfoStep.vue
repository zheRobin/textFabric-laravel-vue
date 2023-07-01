<script setup>
import InputLabel from "Jetstream/Components/InputLabel.vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import InputError from "Jetstream/Components/InputError.vue";
import {companyOptions} from "Jetstream/user.js";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";

const props = defineProps({
    form: Object
});
</script>

<template>
    <div class="space-y-4">
        <div class="space-y-1">
            <InputLabel for="company" :value="$t('Company')" />
            <TextInput
                id="company"
                autofocus
                v-model="form.company"
                type="text"
                class="mt-2 w-full"
                required
                autocomplete="organization"
            />
            <InputError class="mt-2" :message="form.errors.company" />
        </div>

        <div class="space-y-1">
            <InputLabel for="employees" :value="$t('Employees')" />
            <SelectMenu v-model="form.employees" :options="companyOptions" id="employees" class="mt-2" />
            <InputError class="mt-2" :message="form.errors.employees" />
        </div>

        <div class="space-y-1">
            <InputLabel for="phone_number" :value="$t('Phone Number')" />
            <TextInput
                id="phone_number"
                v-model="form.phone_number"
                type="text"
                class="mt-2 w-full"
                required
                autocomplete="tel"
            />
            <InputError class="mt-2" :message="form.errors.phone_number" />
        </div>
    </div>
</template>
