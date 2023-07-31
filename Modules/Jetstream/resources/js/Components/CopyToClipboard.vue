<script setup>
import {ref} from "vue";

const props = defineProps({
    content: String,
});

const copied = ref(false);

const copy = () => {
    copied.value = true;
    navigator.clipboard.writeText(props.content)
        .then(() => {
            copied.value = true;
            setTimeout(() => {
                copied.value = false;
            }, 1000)
        })
        .catch((err) => {
            // TODO: handle error
        });
}

</script>

<template>
    <button class="custom-tooltip" :data-tooltip="copied ? $t('Copied!') : $t('Click to copy')" @click="copy">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
        </svg>
    </button>
</template>

<style>
.custom-tooltip {
    position: relative;
}

.custom-tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
}

.custom-tooltip:hover::after {
    opacity: 1;
}
</style>
