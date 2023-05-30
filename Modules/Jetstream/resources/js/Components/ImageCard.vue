<script setup>
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import {notify} from "notiwind";

const props = defineProps({
    item: Object,
    cell: Object
});

const form = useForm({
    cell: props.cell,
    image: null,
});

const imageInput = ref(null);

const imageSource = ref(`storage/${props.cell.path}`);

const selectImage = () => {
    imageInput.value.click();
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
                group: "notification",
                title: "Success",
                text: "The image was updated!"
            }, 4000) // 4s
        }
    })
}
</script>

<template>
    <div class="h-full flex items-center">
        <input
            ref="imageInput"
            :id="`update-image`"
            type="file"
            class="hidden"
            accept="image/*"
            @change="updateImage"
        >

        <img :src="imageSource" class="h-12" @click="selectImage">
    </div>
</template>
