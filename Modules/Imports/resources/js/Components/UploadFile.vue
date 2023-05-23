<script setup>
import {computed, ref} from "vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";

const fileInput = ref(null);

const form = useForm({
    upload: null,
});

const uploadingError = ref(null);

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
        uploadingError.value = "You can select only images or one data file.";
        clearFileInput();
        return;
    }

    if (dataFiles.length > 1) {
        uploadingError.value = "You can select only one data file.";
        clearFileInput();
        return;
    }

    form.upload = imageFiles.length
        ? imageFiles
        : dataFiles.find(() => true);

    uploadingError.value = false;
}

const upload = () => {
    if (Array.isArray(form.upload)) {
        form.post(route('import.images'), {
            errorBag: 'importImages',
            preserveScroll: true,
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
</script>

<template>
    <div>
        <label for="upload-file" class="block h-32 relative overflow-hidden rounded border-2 border-gray-300 border-dashed" :class="{'border-red-400': uploadingError}">
            <input
                ref="fileInput"
                id="upload-file"
                type="file"
                class="absolute top-0 left-0 right-0 bottom-0 w-full block"
                multiple
                @change="handleUpload"
            />

            <span :class="`absolute top-0 left-0 right-0 bottom-0 w-full block bg-white text-gray-800 pointer-events-none flex justify-center items-center`">
                <div class="text-center">
                    <strong>Browse file to upload</strong>
                    <small v-if="canUpload" :class="`text-gray-600 block`">
                        {{ uploadInfo }}
                    </small>
                    <span v-if="uploadingError || form.errors.upload" class="text-red-900 block">{{ uploadingError || form.errors.upload }}</span>
                    <div class="block mt-2 pointer-events-auto">
                        <PrimaryButton v-if="canUpload" @click="upload">Upload</PrimaryButton>
                    </div>
                </div>
            </span>
        </label>
    </div>
</template>
