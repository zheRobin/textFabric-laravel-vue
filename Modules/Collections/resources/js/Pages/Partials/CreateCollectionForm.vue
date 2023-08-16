<script setup>
import { useForm } from '@inertiajs/vue3';
import FormSection from 'Jetstream/Components/FormSection.vue';
import InputError from 'Jetstream/Components/InputError.vue';
import InputLabel from 'Jetstream/Components/InputLabel.vue';
import PrimaryButton from 'Jetstream/Components/PrimaryButton.vue';
import TextInput from 'Jetstream/Components/TextInput.vue';

const form = useForm({
    name: '',
});

const createCollection = () => {
    form.post(route('collections.store'), {
        errorBag: 'createCollection',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="createCollection">
        <template #title>
            {{$t('Collection Details')}}
        </template>

        <template #description>
            {{$t('Create a new collection to collaborate with others on projects.')}}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" :value="$t('Collection Name')" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('Create') }}
            </PrimaryButton>
        </template>
    </FormSection>
</template>
