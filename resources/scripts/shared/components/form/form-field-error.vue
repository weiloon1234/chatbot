<template>
    <div class="form-field-error-container flex flex-col space-y-1">
        <template v-if="errors && Array.isArray(errors)">
            <div v-for="(n, i) in errors" :key="`err-${i}`" class="form-field-error">
                {{ n }}
            </div>
        </template>
        <div v-else-if="errors && !Array.isArray(errors)" class="form-field-error">
            {{ errors }}
        </div>
        <slot />
    </div>
</template>

<script setup>
defineProps({
    errors: {
        required: false,
        type: [String, Array],
        default: null,
    },
});
</script>
