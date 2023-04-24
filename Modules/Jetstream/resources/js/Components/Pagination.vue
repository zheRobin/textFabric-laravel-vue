<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/20/solid";

const props = defineProps({
    'links': Array,
});
</script>

<template>
    <div v-if="links.length > 3" class="flex items-center justify-center border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <Link :href="links[0].url ?? '#'" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <span>Previous</span>
            </Link>
            <Link :href="links[links.length-1].url ?? '#'" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <span>Next</span>
            </Link>
        </div>
        <div class="hidden sm:flex sm:items-center sm:justify-between">
            <div>
                <nav class="first:rounded isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <template v-for="(link, key) in links">
                        <Link v-if="link.active" :key="`link-${key}`" :href="link.url ?? '#'" class="relative z-10 inline-flex items-center bg-tf-blue-500 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tf-blue-500">
                            {{ link.label }}
                        </Link>
                        <Link v-else-if="link.url || key === 0 || key === links.length - 1" :key="`link-${key}`" :href="link.url ?? '#'" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0" :class="{'rounded-l-md': key === 0, 'rounded-r-md': key === links.length - 1}">
                            <ChevronLeftIcon v-if="key === 0" class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            <ChevronRightIcon v-else-if="key === links.length - 1" class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            <span v-else v-html="link.label"></span>
                        </Link>
                        <span v-else class="relative inline-flex items-center px-4 py-2 text-sm font-semibold bg-gray-50 text-gray-900 ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0">
                            {{ link.label }}
                        </span>
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>
