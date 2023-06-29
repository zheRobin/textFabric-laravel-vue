<script setup>
import PermissionsModal from "./PermissionsModal.vue";
import {ref} from "vue";

const props = defineProps({
    data: Object,
});

const open = ref(false);
const openModal = () => {
    // Assuming `open` is a boolean variable indicating whether the modal is open or not
    open.value = !open.value;
}
</script>

<template>
    <template v-if="open">
        <PermissionsModal :open="!open"/>
    </template>
    <div class="mx-auto px-6 py-6">
        <div class="grid grid-cols-3">
            <div class="col-end-2">
                <div class="font-normal text-3xl">Manage API Tokens</div>
                <p class="mt-5 text-xs w-3/4">You may delete any of existing tokens if they are no longer needed.</p>
            </div>
            <div class="col-start-2 col-end-4">
                <div class="bg-white shadow sm:rounded pt-5 pl-5 pr-5">
                    <div class="flex justify-between pb-5" v-for="item in props.data" :key="item.id">
                        <div class="font-medium">{{ item.name }}</div>
                        <div class="flex">
                            <div><button  class="text-slate-300 underline" @click="openModal">Permissions</button></div>
                            <div><button class="ml-2 text-rose-500">Delete</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
