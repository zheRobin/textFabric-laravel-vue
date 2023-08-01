<script setup>
import DropdownLink from "Jetstream/Components/DropdownLink.vue";
import Dropdown from "Jetstream/Components/Dropdown.vue";
import {headers} from "Modules/Imports/resources/js/headers";
import {useForm, usePage} from "@inertiajs/vue3";
import {notify} from "notiwind";

const props = defineProps({
    header: Object,
    canUpdate: Boolean,
});

const form = useForm({
    header: props.header.name,
    type: ''
});

const updateHeaderType = (header) => {
    form.type = header;

    form.put(route('collections.update-header', usePage().props.auth.user.current_collection), {
        errorBag: 'updateCollectionHeader',
        preserveScroll: true,
        onSuccess: () => {
            notify({
                group: "success",
                title: "Success",
                text: "The header type was updated!"
            }, 4000) // 4s
        }
    })
}

</script>

<template>
    <div class="w-full">
        <div class="block truncate"  :title="header.name">
            {{ header.name }}
        </div>

        <Dropdown v-if="canUpdate" align="left" width="30">
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button type="button" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        {{ header.type }}
                        <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                        </svg>
                    </button>
                </span>
            </template>
            <template #content>
                <div class="w-30">
                    <DropdownLink v-for="(header, index) in headers"  as="button" @click="updateHeaderType(header)">
                        {{ header }}
                    </DropdownLink>
                </div>

            </template>
        </Dropdown>

        <span v-else class="inline-flex items-center text-sm leading-4 font-medium text-gray-500">
            {{ header.type }}
        </span>
    </div>
</template>
