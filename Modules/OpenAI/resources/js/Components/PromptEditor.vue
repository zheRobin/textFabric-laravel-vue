<script setup>
import {Mentionable} from "vue-mention";
import {ref, watch} from "vue";
import Dropdown from "Jetstream/Components/Dropdown.vue";
import DropdownLink from "Jetstream/Components/DropdownLink.vue";
import {ChevronUpDownIcon} from "@heroicons/vue/20/solid";
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    modelValue: String,
    text: String,
    attributes: {
        type: Array,
        default: [],
    },
    title: String,
    canEdit: Boolean,
});

const textInput = ref(null);

const emit = defineEmits(['update:modelValue']);

const promptText = ref(props.modelValue ?? '');

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
    const caretPosition = textInput.value.selectionStart;

    const start = promptText.value.slice(0, caretPosition);
    const end = promptText.value.slice(caretPosition, promptText.value.length);

    promptText.value = `${start}@${attribute}${end} `;

    emit('update:modelValue', promptText.value);
}

const update = () => {
    emit('update:modelValue', promptText.value);
}
</script>

<template>
    <div>
        <div class="flex items-center mb-2">
            <label class="text-sm font-medium flex-1"> {{ title }} </label>

            <Dropdown v-if="canEdit">
                <template #trigger>
                <span class="inline-flex rounded-md">
                    <button type="button" class="inline-flex items-center text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <span>
                            {{ $t('Attributes') }}
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
            offset="2"
            insert-space
        >
            <textarea @input="update" ref="textInput" :disabled="!canEdit" v-model="promptText" :style="{resize: canEdit ? 'vertical' : 'none'}" :placeholder="trans('Enter text and then type @ to trigger the attribute')" rows="8" name="comment" id="comment" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-tf-blue-500 sm:text-sm sm:leading-6" />

            <template #no-result>
                <div class="font-medium text-sm p-2">
                    {{ $t('No result') }}
                </div>
            </template>

            <template #item-@="{ item }" class="h-10">
                <div class="user cursor-pointer wtf">
                    <span class="px-4 py-2 text-left text-sm leading-5 text-gray-700">
                        {{ item.value }}
                    </span>
                </div>
            </template>
        </Mentionable>
    </div>
</template>

<style>
.mention-item {
    padding: 4px 3px;
}

.mention-selected {
    background: rgb(241, 245, 249);
}
</style>
