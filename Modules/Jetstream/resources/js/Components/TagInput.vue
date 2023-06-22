<script setup>
import {ref, watch} from "vue";
import { onClickOutside } from '@vueuse/core';
import TextInput from "Jetstream/Components/TextInput.vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: [],
    },
});

const emit = defineEmits(["update:modelValue"]);

const open = ref(false);
const textInput = ref(null);
const tags = ref(props.modelValue);

watch(() => props.modelValue, (value) => {
    tags.value = value;
});

const outsideTarget = ref(null);
onClickOutside(outsideTarget, () => clearSearch());

const search = (value) => {
    if (value.includes(',')) {
        value.split(',').forEach((el) => {
            if (el) {
                addTag(el);
            }
        })
    }

    toggleSearch();
}

const addTag = (tag) => {
    tag = tag.trim();

    if (tag && !hasTag(tag)) {
        tags.value.push(tag);
    }

    clearSearch();
    emit('update:modelValue', tags);
    // TODO: add focus after add button clicked
}

const hasTag = (tag) => {
    const foundedTag = tags.value.find(e => {
        return e.toLowerCase() === tag.toLowerCase();
    });

    return foundedTag !== undefined;
}

const clearSearch = () => {
    textInput.value = null;

    toggleSearch();
}

const toggleSearch = () => {
    open.value = !!textInput.value;
}

const removeTag = (index) => {
    tags.value.splice(index,1);
    emit('update:modelValue', tags);
}
</script>

<template>
    <div class="mb-10">
        <div ref="outsideTarget" @keydown.esc="clearSearch">
            <div class="relative" @keydown.enter.prevent="addTag(textInput)">
                <TextInput type="text"
                           class="w-full"
                           placeholder="Enter some items"
                           :ref="textInput"
                           v-model="textInput"
                           @input="search($event.target.value)"/>

                <div :class="{block: open, hidden: !open}">
                    <div class="absolute z-40 left-0 mt-2 w-full">
                        <div class="py-1 text-sm bg-white rounded shadow-lg border border-gray-300">
                            <a @click.prevent="addTag(textInput)" class="block py-1 px-5 cursor-pointer hover:bg-tf-blue-600 hover:text-white">
                                Add item "<span class="font-semibold" > {{ textInput }} </span>"
                            </a>
                        </div>
                    </div>
                </div>
                <!-- selections -->
                <template v-for="(tag, index) in tags">
                    <div class="bg-gray-200 inline-flex items-center text-sm rounded mt-2 mr-1">
                        <span class="ml-2 mr-1 leading-relaxed truncate max-w-xs">{{ tag }}</span>
                        <button @click.prevent="removeTag(index)" class="w-6 h-8 inline-block align-middle text-gray-500 hover:text-gray-600 focus:outline-none"> <!--@click.prevent="removeTag(index)"-->
                            <svg class="w-6 h-6 fill-current mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M15.78 14.36a1 1 0 0 1-1.42 1.42l-2.82-2.83-2.83 2.83a1 1 0 1 1-1.42-1.42l2.83-2.82L7.3 8.7a1 1 0 0 1 1.42-1.42l2.83 2.83 2.82-2.83a1 1 0 0 1 1.42 1.42l-2.83 2.83 2.83 2.82z"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
