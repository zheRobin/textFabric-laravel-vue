<script setup>
import {computed, ref} from "vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import ConfirmationModal from "Modules/Imports/resources/js/Components/ConfirmationModal.vue";
import DangerButton from "Jetstream/Components/DangerButton.vue";
import {trans} from "laravel-vue-i18n";
import {PlusCircleIcon} from "@heroicons/vue/20/solid";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";

const props = defineProps({
    hasItems: Boolean,
});

const fileInput = ref(null);

const confirmingAppending = ref(false);

const form = useForm({
    upload: null,
    append: false,
});

const uploadingError = ref(null);

const supportedExtensions = computed(() => {
    return ['.xls', '.xlsx', '.csv', '.json', '.xml'].join(', ');
});

const canUpload = computed(() => {
    return !!(form.upload && !uploadingError.value);
});

const uploadInfo = computed(() => {
    if (Array.isArray(form.upload)) {
        return form.upload.length === 1
            ? form.upload[0].name
            : `${form.upload.length} files selected`
    } else {
        return form.upload.name;
    }
});

const handleUpload = () => {
    const imageFiles = [];
    const dataFiles = [];

    // TODO: move to separate validation function
    Array.from(fileInput.value.files).forEach(function (file) {
        file.type.includes('image')
            ? imageFiles.push(file)
            : dataFiles.push(file)
    });

    if (imageFiles.length && dataFiles.length) {
        uploadingError.value = trans('import_form_1');
        clearFileInput();
        return;
    }

    if (dataFiles.length > 1) {
        uploadingError.value = trans('import_form_2');
        clearFileInput();
        return;
    }

    form.upload = imageFiles.length
        ? imageFiles
        : dataFiles.find(() => true);

    uploadingError.value = false;
}

const confirmUploading = () => {
    if (!Array.isArray(form.upload) &&
        typeof form.upload === 'object' &&
        props.hasItems) {
        confirmingAppending.value = true;
    } else {
        upload();
    }
}

const upload = (append = false) => {
    form.append = append;
    confirmingAppending.value = false;

    if (Array.isArray(form.upload)) {
        form.post(route('import.images'), {
            errorBag: 'importFile',
            preserveScroll: true,
            preserveState: false,
        });
    } else {
        form.post(route('import.file'), {
            errorBag: 'importFile',
            preserveScroll: true,
        });
    }
}

const clearFileInput = () => {
    if (fileInput.value?.value) {
        fileInput.value.value = null;
    }
}

const closeModal = () => {
    confirmingAppending.value = false
}
</script>

<template>
    <div>
        <label for="upload-file" class="block h-36 sm:h-32 relative overflow-hidden rounded border-2 border-gray-300 border-dashed" :class="{'border-red-400': uploadingError}">
            <input
                ref="fileInput"
                id="upload-file"
                type="file"
                class="absolute top-0 left-0 right-0 bottom-0 w-full block"
                :accept="`${supportedExtensions}`"
                multiple
                @change="handleUpload"
            />

            <span :class="`absolute top-0 left-0 right-0 bottom-0 w-full flex-col block bg-white text-gray-800 pointer-events-none flex justify-center items-center`">
                <div class="text-center">
                    <!-- TODO: pass formats as parameters to translation method -->
                    <strong>{{ hasItems ? $t('Browse additional file to append or to replace. We support .xls, .xlsx, .csv, .json, .xml.') : $t('Browse file to upload. We support .xls, .xlsx, .csv, .json, .xml.') }}</strong>
                    <small v-if="canUpload" :class="`text-gray-600 block`">
                        {{ uploadInfo }}
                    </small>
                    <!-- TODO: fix validation message for images -->
                    <span v-if="uploadingError || form.errors.upload" class="text-sm text-red-900 block">{{ uploadingError || form.errors.upload }}</span>
                    <div v-if="canUpload" class="block mt-2 pointer-events-auto">
                        <PrimaryButton @click="confirmUploading" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">{{ $t('Upload') }}</PrimaryButton>
                    </div>

                </div>
                <div>
                   <PrimaryButton v-if="!canUpload" class="ml-2 mt-4 gap-x-1.5">
                        {{ $t('Add file') }}
                        <PlusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                    </PrimaryButton>
                </div>
            </span>
        </label>

        <ConfirmationModal :show="confirmingAppending" @close="confirmingAppending.value = false">
            <template #title>
                {{ $t('Confirm upload') }}
            </template>

            <template #content>
                {{ $t('Do you want to replace or append data?') }}
            </template>

            <template #footer>
                <SecondaryButton class="ml-3" @click="closeModal">
                    {{$t('Cancel')}}
                </SecondaryButton>
                <div class="text-right">
                    <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="upload(true)"
                    >
                        {{ $t('Append') }}
                    </PrimaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="upload()"
                    >
                        {{ $t('Replace') }}
                    </DangerButton>
                </div>
            </template>
        </ConfirmationModal>
    </div>
</template>
