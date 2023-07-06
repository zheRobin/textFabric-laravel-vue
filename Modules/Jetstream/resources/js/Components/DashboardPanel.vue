<script setup>

</script>

<template>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="mx-auto px-6 py-6">
                    <slot name="header"/>

                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
