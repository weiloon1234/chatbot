<template>
    <div class="safe-height flex h-screen w-full flex-col justify-start">
        <slot name="header" />
        <div
            class="flex-1 overflow-y-scroll"
            :style="{
                'padding-top': `${headerOffsetHeight}px`,
                'padding-bottom': `${footerOffsetHeight}px`,
            }">
            <slot />
        </div>
        <slot name="footer" />
    </div>
</template>

<script setup>
defineProps({
    headerOffsetHeight: {
        required: false,
        type: Number,
        default: 10,
    },
    footerOffsetHeight: {
        required: false,
        type: Number,
        default: 10,
    },
});
</script>
