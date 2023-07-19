<template>
    <YouTube
        :src="url"
        @ready="onReady"
        ref="youtube"
    />
</template>

<script>
import { defineComponent, ref } from 'vue';
import YouTube from 'vue3-youtube';

export default defineComponent({
    components: { YouTube },
    props: {
        url: {
            type: String,
            required: true
        }
    },
    methods: {
        onReady() {
            this.$refs.youtube.playVideo();
        },
    },
});
</script>
