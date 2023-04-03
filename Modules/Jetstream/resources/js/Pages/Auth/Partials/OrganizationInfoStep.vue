<script setup>
import InputLabel from "Jetstream/Components/InputLabel.vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import InputError from "Jetstream/Components/InputError.vue";
import DropdownInput from "Jetstream/Components/DropdownInput.vue";

const props = defineProps({
    form: Object
});
</script>

<template>
    <div class="space-y-4">
        <div class="space-y-1">
            <InputLabel for="company" value="Company" />
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
            <InputLabel for="employees" value="Employees" />
            <DropdownInput class="mt-2" :options="['1-10', '11-20', '21-99', '100-350', '351-1.000', '1.001-1.999', '2.000+']" v-model="form.employees"/>
            <InputError class="mt-2" :message="form.errors.employees" />
        </div>

        <div class="space-y-1">
            <InputLabel for="phone_number" value="Phone Number" />
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
