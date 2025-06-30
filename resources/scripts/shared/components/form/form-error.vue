<template>
    <div v-if="errors && errors.length" class="flex w-full flex-col space-y-1 text-red-500">
        <div v-for="(err, i) in errors" :key="i">
            {{ err }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    errors: {
        required: false,
        type: Array,
        default: () => [],
    },
});
</script>
