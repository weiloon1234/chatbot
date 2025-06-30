<template>
    <div
        class="form-input-container form-checkbox"
        :class="{
            'has-error': hasError,
            'disabled': disabled || readonly,
        }">
        <div class="form-input-wrapper">
            <div class="form-field-control flex items-center justify-start space-x-2">
                <div
                    class="flex h-6 w-6 cursor-pointer flex-col items-center justify-center rounded-lg border"
                    :class="{
                        'border-gray-300 bg-transparent': !String(modelValue).length || String(modelValue) !== String(checkValue),
                        'bg-brand-500 border-brand-500': String(modelValue).length && String(modelValue) === String(checkValue),
                    }"
                    @click="emit('update:modelValue', String(modelValue) === String(checkValue) ? uncheckValue : checkValue)">
                    <svg
                        v-if="String(modelValue).length && String(modelValue) === String(checkValue)"
                        width="14"
                        height="14"
                        viewBox="0 0 14 14"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div
                    class="flex h-6 cursor-pointer flex-col items-center justify-center"
                    @click="emit('update:modelValue', String(modelValue) === String(checkValue) ? uncheckValue : checkValue)">
                    <template v-if="hasLabelSlot">
                        <slot name="label" />
                    </template>
                    <template v-else-if="!hideLabel && (label || name)">
                        <form-field-label :label="label ?? name" class="cursor-pointer capitalize" />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';
import FormFieldLabel from '#/shared/components/form/form-field-label.vue';

const props = defineProps({
    modelValue: {
        required: false,
        type: [String, Number],
        default: '',
    },
    type: {
        required: false,
        type: [String, Number],
        default: 'text',
    },
    checkValue: {
        required: false,
        type: String,
        default: '1',
    },
    uncheckValue: {
        required: false,
        type: String,
        default: '0',
    },
    hideLabel: {
        required: false,
        type: Boolean,
        default: false,
    },
    label: {
        required: false,
        type: [String, Number],
        default: null,
    },
    name: {
        required: false,
        type: [String, Number],
        default: null,
    },
    placeholder: {
        required: false,
        type: [String, Number],
        default: null,
    },
    errors: {
        required: false,
        type: [Array],
        default: () => null,
    },
    disabled: {
        required: false,
        type: Boolean,
        default: false,
    },
    readonly: {
        required: false,
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['blur', 'update:modelValue']);

const $slots = useSlots();

const hasError = computed(() => {
    return props.errors?.length > 0;
});
const hasLabelSlot = computed(() => {
    return !!$slots.label;
});
</script>
