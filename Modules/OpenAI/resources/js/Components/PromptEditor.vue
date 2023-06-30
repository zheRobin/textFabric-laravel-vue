<script setup>
import {Mentionable} from "vue-mention";
import {ref, watch} from "vue";
import Dropdown from "Jetstream/Components/Dropdown.vue";
import DropdownLink from "Jetstream/Components/DropdownLink.vue";
import {ChevronUpDownIcon} from "@heroicons/vue/20/solid";

const props = defineProps({
    modelValue: String,
    text: String,
    attributes: Array,
    title: String,
});

const emit = defineEmits(['update:modelValue']);

const promptText = ref(props.modelValue);

watch(() => props.modelValue, (value) => {
    promptText.value = value;
})

const attributesMentions = () => {
    const mentions = [];

    props.attributes.forEach((el) => {
        mentions.push({value: el.name, label: el.name});
    })

    return mentions;
}

const addAttribute = (attribute) => {
    promptText.value = `${promptText.value}@${attribute} `;

    emit('update:modelValue', promptText.value);
}
</script>

<template>
    <div>
        <div class="flex items-center mb-2">
            <label class="text-sm font-medium flex-1"> {{ title }} </label>

            <Dropdown width="30">
                <template #trigger>
                <span class="inline-flex rounded-md">
                    <button type="button" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <span>
                            attributes
                        </span>
                        <ChevronUpDownIcon class="-mr-0.5 h-4 w-4" />
                    </button>
                </span>
                </template>
                <template #content>
                    <div class="w-30 h-36 overflow-scroll">
                        <DropdownLink v-for="(attribute, index) in attributes"  as="button" @click="addAttribute(attribute.name)">
                            {{ attribute.name }}
                        </DropdownLink>
                    </div>

                </template>
            </Dropdown>
        </div>

        <Mentionable
            :keys="['@']"
            :items="attributesMentions()"
            offset="6"
            insert-space
        >
            <textarea @change="$emit('update:modelValue', $event.target.value)" v-model="promptText" placeholder="Enter text and then type @ to trigger the attribute" rows="8" name="comment" id="comment" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-500 sm:text-sm sm:leading-6" />

            <template #no-result>
                <div class="font-medium text-sm p-2">
                    No result
                </div>
            </template>

            <template #item-@="{ item }">
                <div class="user cursor-pointer">
                    <span class="px-4 py-2 text-left text-sm leading-5 text-gray-700">
                        {{ item.value }}
                    </span>
<!--                    <span class="text-gray-600 text-sm font-bold">-->
<!--                        ({{ item.label }})-->
<!--                    </span>-->
                </div>
            </template>
        </Mentionable>
    </div>
</template>

<style>
.mention-item {
    padding: 4px 3px;
    /*border-radius: 4px;*/
}

.mention-selected {
    background: rgb(241, 245, 249);
}
</style>
