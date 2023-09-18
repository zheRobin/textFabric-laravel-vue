<script setup>
const props = defineProps({
    header: Object,
});


</script>

<template>
    <div class="w-full">
        <div class="block truncate" :title="header.name">
            {{ header.name }}
        </div>
    </div>
</template>
