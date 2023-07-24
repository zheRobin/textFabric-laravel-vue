<script setup>
import FormSection from "Jetstream/Components/FormSection.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";
import InputError from "Jetstream/Components/InputError.vue";
import ActionMessage from "Jetstream/Components/ActionMessage.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {ref} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";

const form = useForm({
    logo: Object,
})

const currentLogo = ref(usePage().props.mainLogoPath);

const logoInput = ref(null);
const logoPreview = ref(null);

const updateLogo = () => {
    if (logoInput.value) {
        form.logo = logoInput.value.files[0];
    }

    form.post(route('app.settings.logo'), {
        errorBag: 'updateAppLogo',
        preserveScroll: true,
        onSuccess: () => clearLogoFileInput(),
    });
}

const updateLogoPreview = () => {
    const logo = logoInput.value.files[0];

    if (! logo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        logoPreview.value = e.target.result;
    };

    reader.readAsDataURL(logo);
}

const selectNewLogo = () => {
    logoInput.value.click();
}

const clearLogoFileInput = () => {
    if (logoInput.value?.value) {
        logoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateLogo">
        <template #title>
            Main App Logo
        </template>

        <template #description>
            Update the main logo.
        </template>

        <template #form>
            <!-- App Logo -->
            <div v-if="true" class="col-span-6 sm:col-span-4">
                <!-- App Logo File Input -->
                <input
                    ref="logoInput"
                    type="file"
                    class="hidden"
                    @change="updateLogoPreview"
                >

                <InputLabel for="logo" value="Logo" />

                <!-- Current Logo -->
                <div v-show="! logoPreview && currentLogo" class="mt-2 h-20 w-20 flex items-center">
                    <img :src="currentLogo" alt="Logo" class="flex object-cover">
                </div>

                <!-- New Logo Preview -->
                <div v-show="logoPreview" class="mt-2">
                    <span
                        class="block w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + logoPreview + '\'); background-size: contain'"
                    />
                </div>

                <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewLogo">
                    Select A New Logo
                </SecondaryButton>

                <InputError :message="form.errors.logo" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
