<script setup>
import { useForm } from '@inertiajs/vue3';
import ActionMessage from 'Jetstream/Components/ActionMessage.vue';
import FormSection from 'Jetstream/Components/FormSection.vue';
import InputError from 'Jetstream/Components/InputError.vue';
import InputLabel from 'Jetstream/Components/InputLabel.vue';
import PrimaryButton from 'Jetstream/Components/PrimaryButton.vue';
import TextInput from 'Jetstream/Components/TextInput.vue';

const props = defineProps({
    collection: Object,
    permissions: Object,
});

const form = useForm({
    name: props.collection.name,
});

const updateCollection = () => {
    form.put(route('collections.update', props.collection), {
        errorBag: 'updateCollection',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateCollection">
        <template #title>
            Collection Name
        </template>

        <template #description>
            The collection's name.
        </template>

        <template #form>
            <!-- Collection Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Collection Name" />

                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.name" class="mt-2" />
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
