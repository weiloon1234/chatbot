<template>
    <div
        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6"
    >
        <div
            v-if="hasHeader"
            class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <slot name="title" />
            <slot name="tools" />
        </div>
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <slot />
        </div>
    </div>
</template>
<script setup>
import { computed, useSlots } from "vue";

const $slots = useSlots();
const hasHeader = computed(() => {
    return !!$slots.title || !!$slots.tools;
});
</script>
