<template>
    <div v-if="countLinks > 3" class="flex items-center justify-center border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $t('Previous') }}</a>
            <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">{{$t('Next')}}</a>
        </div>
        <div class="items-center">
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <button @click="prevPage" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                        <span class="sr-only">{{ $t('Previous') }}</span>
                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    </button>

                    <div v-if="activeLink < 3">
                        <button v-for="(value) in links.slice(1, 3)" @click="changePage(value)" :data-id="value" aria-current="page" :class="value === activeLink ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300'" class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                            {{ value }}
                        </button>
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>
                        <button v-for="(value) in links.slice(-3)" @click="changePage(value)" :data-id="value" aria-current="page" :class="value === activeLink ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300'" class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                            {{ value }}
                        </button>
                    </div>
                    <div v-else-if="activeLink > 3 && activeLink < props.count/3 - 3">
                        <button v-for="(value) in links.slice(0, 2)" @click="changePage(value)" :data-id="value" aria-current="page" :class="value === activeLink ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300'" class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                            {{ value }}
                        </button>
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>
                        <button v-for="(value) in [activeLink-2, activeLink-1,activeLink]" @click="changePage(value)" :data-id="value" aria-current="page" :class="value === activeLink ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300'" class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                            {{ value }}
                        </button>
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>

                        <button v-for="(value) in links.slice(-3)" @click="changePage(value)" :data-id="value" aria-current="page" :class="value === activeLink ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300'" class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                            {{ value }}
                        </button>
                    </div>
                    <div v-else-if="activeLink > props.count/3 - 3 && activeLink<props.count/3">
                        <button v-for="(value) in links.slice(1, 8)" @click="changePage(value)" :data-id="value" aria-current="page" :class="value === activeLink ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300'" class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                            {{ value }}
                        </button>
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>
                        <button v-if="activeLink != props.count/3" v-for="(value) in [activeLink-2, activeLink-1,activeLink]" @click="changePage(value)" :data-id="value" aria-current="page" :class="value === activeLink ? 'bg-indigo-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300'" class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2">
                            {{ value }}
                        </button>
                    </div>
<!--                    <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">2</a>-->
<!--                    <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex">3</a>-->
<!--                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>-->
                    <button @click="nextPage" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                        <span class="sr-only">{{$t('Next')}}</span>
                        <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { ref } from 'vue'

const props = defineProps({
    count: Number
})
const emit = defineEmits(["update:page"]);
const activeLink = ref(0);
const changePage = (id) => {
    activeLink.value = id;
    emit('update:page', id);
}

const prevPage = () => {
    if(activeLink.value >= 1){
        activeLink.value -= 1;
        emit('update:page', activeLink.value);

    }
}
const nextPage = () => {
    if(activeLink.value < (countLinks.value/3) - 1){
        activeLink.value += 1;
        emit('update:page', activeLink.value);
    }
}

const countLinks = ref(props.count);

const links = [];

for(let i=0; i < countLinks.value/3; i++){
    links.push(i);
}
</script>
