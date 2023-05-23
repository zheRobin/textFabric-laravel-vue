<script setup>

defineProps({
    source: String
})
</script>

<template>
    <div class="h-12">
        <img :src="source" class="h-12">
    </div>
</template>
