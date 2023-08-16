<script setup>
import DialogModal from "Jetstream/Components/DialogModal.vue";
import TextInput from "Jetstream/Components/TextInput.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {PencilSquareIcon} from "@heroicons/vue/20/solid";
import {ref, watch} from "vue";

const props = defineProps({
    name: String,
});

const name = ref(props.name);

const emit = defineEmits(['rename']);

watch(() => props.name, (value) => {
    name.value = value;
});

const renaming = ref(false);

const startRenaming = () => {
    renaming.value = true;
}

const closeModal = () => {
    renaming.value = false;
}

const rename = () => {
    emit('rename', name.value);
    closeModal();
}
</script>

<template>
    <span @click="startRenaming">
        <slot />
    </span>

    <DialogModal :show="renaming" @close="closeModal">
        <template #title>
            <div class="flex items-center">
                <PencilSquareIcon class="mr-2 w-5 text-tf-blue-500" aria-hidden="true"/>
                <span>Rename</span>
            </div>
        </template>

        <template #content>
            <div class="mt-4">
                <TextInput
                    ref="passwordInput"
                    v-model="name"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Name"
                    @keyup.enter="rename"
                />
            </div>
        </template>

        <template #footer>
            <SecondaryButton class="" @click="closeModal">
                {{$t('Cancel')}}
            </SecondaryButton>

            <PrimaryButton
                class="ml-3"
                @click="rename"
            >
                {{$t('Update')}}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
