<template>
    <div
        v-if="isTheComponentReady"
        class="form-input-container date-picker"
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
        <vue-date-picker
            v-model="lazyValue"
            :disabled="disabled"
            :readonly="readonly"
            :format="customFormat"
            :model-value="lazyValue"
            :enable-time-picker="false"
            :clearable="true"
            :flow="flow"
            teleport
            auto-apply
            class="form-input-wrapper"
            @blur="emit('blur', lazyValue)">
            <template #trigger>
                <span class="absolute top-1/2 left-4 z-10 -translate-y-1/2 cursor-pointer">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 32 32"
                        aria-hidden="true"
                        width="20"
                        height="20"
                        class="dp__input_icons fill-gray-500"
                        role="img"
                        aria-label="Calendar icon">
                        <path
                            d="M29.333 8c0-2.208-1.792-4-4-4h-18.667c-2.208 0-4 1.792-4 4v18.667c0 2.208 1.792 4 4 4h18.667c2.208 0 4-1.792 4-4v-18.667zM26.667 8v18.667c0 0.736-0.597 1.333-1.333 1.333 0 0-18.667 0-18.667 0-0.736 0-1.333-0.597-1.333-1.333 0 0 0-18.667 0-18.667 0-0.736 0.597-1.333 1.333-1.333 0 0 18.667 0 18.667 0 0.736 0 1.333 0.597 1.333 1.333z"></path>
                        <path d="M20 2.667v5.333c0 0.736 0.597 1.333 1.333 1.333s1.333-0.597 1.333-1.333v-5.333c0-0.736-0.597-1.333-1.333-1.333s-1.333 0.597-1.333 1.333z"></path>
                        <path d="M9.333 2.667v5.333c0 0.736 0.597 1.333 1.333 1.333s1.333-0.597 1.333-1.333v-5.333c0-0.736-0.597-1.333-1.333-1.333s-1.333 0.597-1.333 1.333z"></path>
                        <path d="M4 14.667h24c0.736 0 1.333-0.597 1.333-1.333s-0.597-1.333-1.333-1.333h-24c-0.736 0-1.333 0.597-1.333 1.333s0.597 1.333 1.333 1.333z"></path>
                    </svg>
                </span>
                <input type="text" :name="name" :value="forInput" :readonly="disabled || readonly" :placeholder="placeholder" class="form-input" @blur="emit('blur')" />
                <span v-if="modelValue && modelValue.length" class="absolute top-1/2 right-4 z-10 -translate-y-1/2 cursor-pointer" @click="onClear">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                        <path
                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                    </svg>
                </span>
            </template>
        </vue-date-picker>
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
import { computed, nextTick, onMounted, ref, useSlots } from 'vue';
import FormFieldNote from '#/shared/components/form/form-field-note.vue';
import FormFieldError from '#/shared/components/form/form-field-error.vue';
import FormFieldLabel from '#/shared/components/form/form-field-label.vue';
import moment from 'moment';

const props = defineProps({
    modelValue: {
        required: false,
        type: [String, Number],
        default: '',
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

const emit = defineEmits(['blur', 'update:modelValue']);

const $slots = useSlots();

const isTheComponentReady = ref(false);

const customFormat = ref('yyyy-MM-dd'); // date-fns format
const flow = ref(['year', 'month', 'calendar']);

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
    get: () => {
        return props.modelValue
            ? moment(props.modelValue, moment.ISO_8601, true).format('YYYY-MM-DD') // Convert Laravel ISO 8601 to YYYY-MM-DD
            : null;
    },
    set: (v) => {
        if (v) {
            emit('update:modelValue', moment(v, 'YYYY-MM-DD').format('YYYY-MM-DD')); // Only send YYYY-MM-DD
        } else {
            emit('update:modelValue', null);
        }
    },
});
const forInput = computed(() => {
    if (!props.modelValue) return null; // Prevent errors

    return moment(props.modelValue, moment.ISO_8601, true).isValid()
        ? moment(props.modelValue).format('YYYY-MM-DD') // Display YYYY-MM-DD format
        : null;
});

const onClear = () => {
    lazyValue.value = null;
};

onMounted(async () => {
    if (props.modelValue) {
        lazyValue.value = moment(props.modelValue, moment.ISO_8601, true).format('YYYY-MM-DD'); // Convert Laravel ISO 8601 to YYYY-MM-DD
    }

    await nextTick(() => {
        isTheComponentReady.value = true;
    });
});
</script>
