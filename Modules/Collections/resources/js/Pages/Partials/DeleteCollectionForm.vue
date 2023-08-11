<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from 'Jetstream/Components/ActionSection.vue';
import ConfirmationModal from 'Jetstream/Components/ConfirmationModal.vue';
import DangerButton from 'Jetstream/Components/DangerButton.vue';
import SecondaryButton from 'Jetstream/Components/SecondaryButton.vue';

const props = defineProps({
    collection: Object,
});

const confirmingCollectionDeletion = ref(false);
const form = useForm({});

const confirmCollectionDeletion = () => {
    confirmingCollectionDeletion.value = true;
};

const deleteCollection = () => {
    form.delete(route('collections.destroy', props.collection.id), {
        errorBag: 'deleteCollection',
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            {{$t('Delete Collection')}}
        </template>

        <template #description>
            {{$t('Permanently delete this collection.')}}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                {{$t('Collection will be deleted permanently.')}}
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmCollectionDeletion">
                    {{$t('Delete Collection')}}
                </DangerButton>
            </div>

            <!-- Delete Collection Confirmation Modal -->
            <ConfirmationModal :show="confirmingCollectionDeletion" @close="confirmingCollectionDeletion = false">
                <template #title>
                    {{$t('Delete Collection')}}
                </template>

                <template #content>
                    {{$t('Are you sure you want to delete this collection?')}}
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingCollectionDeletion = false">
                        {{ $t('Cancel') }}
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteCollection"
                    >
                        {{$t('Delete Collection')}}
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </template>
    </ActionSection>
</template>
