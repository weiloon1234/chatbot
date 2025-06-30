<template>
    <div class="form-field-note-container flex flex-col space-y-1">
        <template v-if="notes && Array.isArray(notes)">
            <div v-for="(n, i) in notes" :key="`note-${i}`" class="form-field-note">
                {{ n }}
            </div>
        </template>
        <div v-else-if="notes && !Array.isArray(notes)" class="form-field-note">
            {{ notes }}
        </div>
        <slot />
    </div>
</template>

<script setup>
defineProps({
    notes: {
        required: false,
        type: [String, Array],
        default: null,
    },
});
</script>
