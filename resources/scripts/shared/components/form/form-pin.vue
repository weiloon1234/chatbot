<template>
    <div
        class="form-input-container pin-input"
        :class="{
            'has-error': hasError,
            'disabled': disabled || readonly,
        }">
        <template v-if="hasLabelSlot">
            <slot name="label" />
        </template>
        <template v-else-if="!hideLabel && (label || name)">
            <form-field-label :label="label ?? name" />
        </template>
        <div class="form-input-wrapper">
            <v-otp-input
                v-model="lazyValue"
                input-classes="otp-input"
                input-type="number"
                :num-inputs="6"
                :should-auto-focus="false"
                :should-focus-order="true"
                :placeholder="['*', '*', '*', '*', '*', '*']"
                :is-disabled="disabled || readonly"
                @on-change="onInput"
                @on-complete="onComplete" />
        </div>
        <template v-if="hasNotesSlot">
            <slot name="notes" />
        </template>
        <template v-else>
            <form-field-note v-if="notes" :notes="notes">
                <template #notes />
            </form-field-note>
        </template>
        <form-field-error v-if="hasError" :errors="errors">
            <template #errors />
        </form-field-error>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';
import FormFieldNote from '#/shared/components/form/form-field-note.vue';
import FormFieldError from '#/shared/components/form/form-field-error.vue';
import FormFieldLabel from '#/shared/components/form/form-field-label.vue';
import VOtpInput from 'vue3-otp-input';

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
    notes: {
        required: false,
        type: [String, Array],
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

const emit = defineEmits(['blur', 'update:modelValue', 'complete']);

const $slots = useSlots();

const hasError = computed(() => {
    return props.errors?.length > 0;
});
const hasNotesSlot = computed(() => {
    return !!$slots.notes;
});
const hasLabelSlot = computed(() => {
    return !!$slots.label;
});
const lazyValue = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});
const onInput = (value) => {
    emit('update:modelValue', value);
};
const onComplete = (v) => {
    console.log(v);
    emit('complete', v);
};
</script>
