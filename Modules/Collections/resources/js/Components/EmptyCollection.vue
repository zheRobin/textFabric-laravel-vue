<script setup>
import { Link } from '@inertiajs/vue3';
import { DocumentPlusIcon } from '@heroicons/vue/20/solid';
import EmptyState from "Jetstream/Components/EmptyState.vue";

</script>

<template>
    <EmptyState :title="$t('collection.empty.title')"
                :description="$t('collection.empty.description')"
    >
        <template #icon>
            <DocumentPlusIcon />
        </template>
        <template #action>
            <Link class="inline-flex justify-center rounded-md bg-tf-blue-500 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-tf-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-600"
                  :href="route('collections.create')">
                {{ $t('New Collection') }}
            </Link>
        </template>
    </EmptyState>
</template>
