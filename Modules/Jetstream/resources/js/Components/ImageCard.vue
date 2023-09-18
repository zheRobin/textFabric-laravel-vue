<script setup>
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import {notify} from "notiwind";
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    item: Object,
    cell: Object,
    canUpload: Boolean,
});

const form = useForm({
    cell: props.cell,
    image: null,
});

const imageInput = ref(null);

const imageSource = ref(`storage/${props.cell.path}`);

const selectImage = () => {
    console.log(props.canUpload);
    if (props.canUpload) {
        imageInput.value.click();
    }
}

const updateImage = () => {
    const image = imageInput.value.files[0];

    if (! image) return;

    form.image = image;

    form.post(route('collection-items.update-image', props.item),{
        errorBag: 'updateCollectionItemImage',
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            notify({
                group: "success",
                title: trans("Success"),
                text: trans("The image was updated!")
            }, 4000) // 4s
        }
    })
}
</script>

<template>
    <div class="h-full flex items-center justify-center">
        <input
            ref="imageInput"
            :id="`update-image`"
            type="file"
            class="hidden"
            accept="image/*"
            @change="updateImage"
        >

        <img :src="imageSource" draggable="false" class="h-12" @click="selectImage">
    </div>
</template>
