<script setup>
import {Menu, MenuButton, MenuItem, MenuItems, Popover, PopoverButton, PopoverGroup, PopoverPanel} from '@headlessui/vue'
import {MagnifyingGlassIcon} from '@heroicons/vue/24/outline'
import {ChevronDownIcon, UsersIcon} from '@heroicons/vue/20/solid'
import Checkbox from "Jetstream/Components/Checkbox.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import TextInput from "Jetstream/Components/TextInput.vue";

defineEmits(['submitted', 'sorted']);

const props = defineProps({
    filters: Object,
    subscriptionPlans: Array,
});
</script>

<template>
    <div class="bg-white py-4">
        <div class="flex flex-wrap items-center justify-between">
            <div class="px-4">
                <Menu as="div" class="relative inline-block text-left">
                    <MenuButton class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                        {{ $t('Sort') }}
                        <ChevronDownIcon class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                    </MenuButton>

                    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                        <MenuItems class="absolute left-0 z-10 mt-2 w-40 origin-top-left rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <div class="py-1">
                                <MenuItem v-slot="{ active }">
                                    <button @click="$emit('sorted', 'expires_soon')" :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm font-medium text-gray-900 w-full']">
                                        {{$t('Expires soon')}}
                                    </button>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
            <div class="flex items-center">
                <div class="px-4">
                    <PopoverGroup>
                        <Popover as="div" class="relative">
                            <PopoverButton class="flex focus:outline-none items-center group block text-sm font-medium leading-6 text-gray-700 hover:text-gray-900">
                                {{ $t('Subscription') }}
                                <ChevronDownIcon class="mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                            </PopoverButton>

                            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                <PopoverPanel class="absolute right-0 z-10 mt-2 origin-top-right rounded-md bg-white p-4 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="flex items-center">
                                        <Checkbox id="subscribed" v-model:checked="filters.subscribed" />
                                        <InputLabel for="subscribed" value="Subscribed" class="ml-2 whitespace-nowrap pr-6 text-sm font-medium text-gray-900"/>
                                    </div>
                                    <div class="flex items-center">
                                        <Checkbox id="expired" v-model:checked="filters.expired" />
                                        <InputLabel for="expired" value="Expired" class="ml-2 whitespace-nowrap pr-6 text-sm font-medium text-gray-900"/>
                                    </div>
                                    <div class="flex items-center">
                                        <Checkbox id="trial" v-model:checked="filters.trial" />
                                        <InputLabel for="trial" value="Trial" class="ml-2 whitespace-nowrap pr-6 text-sm font-medium text-gray-900"/>
                                    </div>
                                </PopoverPanel>
                            </transition>
                        </Popover>
                    </PopoverGroup>
                </div>
                <div class="px-4">
                    <PopoverGroup>
                        <Popover as="div" class="relative">
                            <PopoverButton class="flex items-center group block text-sm font-medium leading-6 text-gray-700 hover:text-gray-900 focus:outline-none">
                                {{$t('Plan')}}
                                <ChevronDownIcon class="mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                            </PopoverButton>

                            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                <PopoverPanel class="absolute right-0 z-10 mt-2 origin-top-right rounded-md bg-white p-4 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="flex items-center" v-for="plan in subscriptionPlans" :key="plan.id">
                                        <Checkbox :id="`plan-${plan.slug}`" v-model:checked="filters.plan" :value="plan.id" />
                                        <InputLabel :for="`plan-${plan.slug}`" :value="plan.name" class="ml-2 whitespace-nowrap pr-6 text-sm font-medium text-gray-900"/>
                                    </div>
                                </PopoverPanel>
                            </transition>
                        </Popover>
                    </PopoverGroup>
                </div>
                <div class="px-4">
                    <div class="flex rounded-md shadow-sm">
                        <div class="relative flex flex-grow items-stretch focus-within:z-10">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <UsersIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </div>
                            <TextInput v-model="filters.search" class="block w-full rounded-none rounded-l-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-500 sm:text-sm sm:leading-6 focus-visible:outline-none" :placeholder="$t('Search...')"/>
                        </div>
                        <button type="button" @click="$emit('submitted')" class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <MagnifyingGlassIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
