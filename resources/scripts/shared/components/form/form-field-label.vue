<template>
    <label class="form-input-label">
        {{ label }}
    </label>
</template>

<script setup>
defineProps({
    label: {
        required: false,
        type: [String, Number],
        default: null,
    },
});
</script>
