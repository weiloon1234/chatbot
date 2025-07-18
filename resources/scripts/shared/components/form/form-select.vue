<template>
    <div
        class="form-input-container"
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
        <div class="form-input-wrapper select-option">
            <listbox v-slot="{ open }" :multiple="multiple" class="listbox-container">
                <div>
                    <listbox-button class="listbox-button relative cursor-pointer" as="div">
                        <div
                            class="block w-full flex-1 truncate text-left"
                            :class="{
                                'flex items-center justify-start space-x-2': multiple,
                            }">
                            <template v-if="multiple">
                                <template v-if="selectedValues && selectedValues.length">
                                    <div v-for="(s, i) in selectedValues" :key="`multi-select-${name}-${i}`" class="bg-brand-100 flex items-center justify-between space-x-3 rounded-lg px-4">
                                        <span class="flex-1">{{ optionText[s] }}</span>
                                        <span class="text-red-500" @click="onRemove(s)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                <path
                                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </span>
                                    </div>
                                </template>
                                <template v-else>
                                    <span
                                        class="placeholder-text"
                                    >
                                        {{ placeholder ?? $t('Please select') }}
                                    </span>
                                </template>
                            </template>
                            <template v-else>
                                <span v-if="selectedSingleText">
                                    {{ selectedSingleText }}
                                </span>
                                <span
                                    v-else
                                    class="placeholder-text"
                                >
                                    {{ placeholder ?? $t('Please select') }}
                                </span>
                            </template>
                        </div>
                        <span
                            class="pointer-events-none absolute top-1/2 right-0 z-10 -translate-y-1/2 text-gray-700 dark:text-gray-400"
                            :class="{
                                'rotate-180': open,
                            }">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </listbox-button>
                    <transition-root
                        :show="open"
                        enter="transition ease-out duration-100"
                        enter-from="opacity-0"
                        enter-to="opacity-100"
                        leave="transition ease-in duration-75"
                        leave-from="opacity-100"
                        leave-to="opacity-0">
                        <listbox-options
                            v-if="options && Array.isArray(options) && options.length"
                            class="max-h-select absolute top-full left-0 z-40 flex w-full flex-col overflow-y-auto rounded-lg bg-white shadow-sm listbox-option-container"
                        >
                            <listbox-option
                                v-for="(option, index) in options"
                                :key="index"
                                :value="option.value"
                                :disabled="isOptionDisabled(option)"
                                class="hover:bg-primary/5 flex w-full cursor-pointer items-center justify-start border-gray-200 dark:border-gray-800"
                                :class="{
                                    'bg-brand-500/5': isSelected(option),
                                }"
                                @click="onSelect(option)">
                                <span v-if="isSelected(option)" class="flex items-center bg-transparent pl-3 text-blue-600">
                                    <!-- Check Icon -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.704 5.296a.75.75 0 00-1.06 0L8.25 12.69l-3.394-3.394a.75.75 0 00-1.06 1.06l3.924 3.924a.75.75 0 001.06 0l7.924-7.924a.75.75 0 000-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <div class="flex w-full items-center border-l-2 border-transparent p-2 pl-2">
                                    <div class="flex w-full items-center">
                                        {{ option.text }}
                                    </div>
                                </div>
                            </listbox-option>
                        </listbox-options>
                    </transition-root>
                </div>
            </listbox>
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
import { computed, inject, useSlots } from "vue";
import FormFieldNote from '#/shared/components/form/form-field-note.vue';
import FormFieldError from '#/shared/components/form/form-field-error.vue';
import FormFieldLabel from '#/shared/components/form/form-field-label.vue';
import { Listbox, ListboxButton, ListboxOptions, ListboxOption, TransitionRoot } from '@headlessui/vue';

const props = defineProps({
    modelValue: {
        required: false,
        type: [String, Number, Array],
        default: () => null,
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
    options: {
        required: true,
        type: Array,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    maxSelect: {
        type: Number,
        default: Infinity,
    },
    placeholder: {
        required: false,
        type: [String, Number],
        default: () => null,
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
const $helper = inject('$helper');

const hasError = computed(() => props.errors?.length > 0);
const hasNotesSlot = computed(() => !!$slots.notes);
const hasLabelSlot = computed(() => !!$slots.label);

const selectedValues = computed({
    get: () => props.modelValue,
    set: (val) => {
        emit('update:modelValue', val);
    },
});

const optionText = computed(() => {
    const p = {};
    if (props.options && props.options.length) {
        props.options.forEach((option) => {
            p[String(option.value)] = option.text;
        });
    }

    return p ?? {};
});

const isSelected = (option) => {
    if (props.multiple) {
        if (!Array.isArray(selectedValues.value)) return false;
        return selectedValues.value.some(val => $helper.isValuesEqual(val, option.value));
    }
    return $helper.isValuesEqual(selectedValues.value, option.value);
};

const isOptionDisabled = (option) => {
    if (props.multiple) {
        return false;
    } else {
        return option.disabled || props.disabled || props.readonly;
    }
};

const selectedSingleText = computed(() => {
    if (props.multiple) return null;
    if (props.options && props.options.length) {
        const selected = props.options.find(option =>
            $helper.isValuesEqual(props.modelValue, option.value)
        );
        return selected ? selected.text : null;
    }

    return null;
});

const onSelect = (option) => {
    if (props.disabled || props.readonly) {
        return;
    }

    if (props.multiple) {
        handleChange(String(option.value));
    } else {
        if (selectedValues.value === option.value) {
            selectedValues.value = '';
        } else {
            selectedValues.value = option.value;
        }
    }
};

const onRemove = (value) => {
    handleChange(String(value));
};

const handleChange = (value) => {
    if (props.disabled || props.readonly) {
        return;
    }

    if (props.multiple) {
        let temp = [];
        if (Array.isArray(selectedValues.value)) {
            temp = [...selectedValues.value];
        }

        if (temp.includes(value)) {
            temp = temp.filter((v) => v !== value);
        } else {
            if (temp.length < props.maxSelect) {
                temp.push(value);
            }
        }

        selectedValues.value = temp;
    } else {
        selectedValues.value = String(value.value ?? '');
    }
};
</script>
