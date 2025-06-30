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
        <div class="form-input-wrapper contact-wrapper">
            <combobox v-slot="{ open }" :model-value="selectValue" as="div" class="contact-select-container">
                <div class="contact-select relative">
                    <combobox-input class="contact-select" :display-value="(_) => displayText" :placeholder="placeholder ?? name ?? '-'" @change="searchQuery = $event.target.value" />
                    <combobox-button class="absolute inset-y-0 right-0 flex items-center pr-2">
                        <span
                            class="pointer-events-none absolute top-1/2 right-0 z-10 -translate-y-1/2 text-gray-700 dark:text-gray-400"
                            :class="{
                                'rotate-180': open,
                            }">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </combobox-button>
                    <transition-root leave="transition ease-in duration-100" leave-from="opacity-100" leave-to="opacity-0" @after-leave="searchQuery = ''">
                        <combobox-options class="max-h-select absolute top-full left-0 z-40 flex w-full flex-col overflow-y-auto rounded-lg bg-white shadow-sm">
                            <div v-if="possibleOptions.length === 0 && searchQuery !== ''" class="relative cursor-default px-4 py-2 text-gray-700 select-none">-</div>
                            <combobox-option v-for="option in possibleOptions" :key="option.id" v-slot="{ active }" as="template" :value="option">
                                <li
                                    class="hover:bg-primary/5 flex w-full cursor-pointer items-center justify-start border-gray-200 py-2 pl-4 dark:border-gray-800"
                                    :class="{
                                        'bg-brand-500/5': active,
                                    }">
                                    <span class="block">
                                        {{ option.phone_code }}
                                    </span>
                                </li>
                            </combobox-option>
                        </combobox-options>
                    </transition-root>
                </div>
            </combobox>
            <input
                v-integer
                type="number"
                :name="name"
                :value="inputValue"
                :readonly="disabled || readonly"
                :placeholder="placeholder"
                class="form-control"
                @input="(event) => onInput(event.target.value)"
                @blur="emit('blur:input', inputValue)" />
        </div>
        <template v-if="hasNotesSlot">
            <slot name="notes" />
        </template>
        <template v-else>
            <form-field-note v-if="notes" :notes="notes">
                <template #notes />
            </form-field-note>
        </template>
        <form-field-error v-if="hasError" :errors="computedErrors">
            <template #errors />
        </form-field-error>
    </div>
</template>

<script setup>
import { computed, inject, onMounted, ref, useSlots, watch } from "vue";
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, TransitionRoot, ComboboxButton } from '@headlessui/vue';
import FormFieldLabel from '#/shared/components/form/form-field-label.vue';
import FormFieldNote from '#/shared/components/form/form-field-note.vue';
import FormFieldError from '#/shared/components/form/form-field-error.vue';

const props = defineProps({
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
    selectValue: {
        required: false,
        type: [String, Number],
        default: '',
    },
    inputValue: {
        required: false,
        type: [String, Number],
        default: '',
    },
    selectName: {
        required: false,
        type: [String, Number],
        default: 'contact_country_id',
    },
    inputName: {
        required: false,
        type: [String, Number],
        default: 'contact_number',
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
        type: [Array, Object],
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

const emit = defineEmits(['blur:select', 'blur:input', 'update:selectValue', 'update:inputValue']);

const $slots = useSlots();
const $settingStore = inject('$settingStore');

const searchQuery = ref('');

const countryOptions = computed(() => {
    return $settingStore.settings.country ?? [];
});
const hasError = computed(() => {
    return computedErrors.value?.length > 0;
});
const hasNotesSlot = computed(() => {
    return !!$slots.notes;
});
const hasLabelSlot = computed(() => {
    return !!$slots.label;
});
const selectedCountry = computed(() => {
    if (!countryOptions.value) return '';
    if (!props.selectValue || props.selectValue === '') return '';
    return countryOptions.value.find((c) => {
        return parseInt(c.id) === parseInt(props.selectValue);
    });
});
const selectedExt = computed(() => {
    return selectedCountry.value.phone_code ?? '';
});
const possibleOptions = computed(() => {
    return countryOptions.value.filter((c) => {
        const q = String(searchQuery.value).toLowerCase().toString();
        if (!q || q === '' || !q.length) return true;
        return (
            String(c.name).toLowerCase().toString().includes(q) ||
            String(c.iso2).toLowerCase().toString().includes(q) ||
            String(c.iso3).toLowerCase().toString().includes(q) ||
            String(c.phone_code).toLowerCase().toString().includes(q)
        );
    });
});
const computedErrors = computed(() => {
    if (Array.isArray(props.errors)) {
        return props.errors.length > 0 ? props.errors : null;
    }

    if (typeof props.errors === 'object' && props.errors !== null) {
        // Filter errors for specific fields only
        const filteredErrors = Object.entries(props.errors)
            .filter(([key, value]) => {
                // Match either inputName or selectName
                const isTargetField = key === props.inputName || key === props.selectName;
                // Ensure the value is an array
                return isTargetField && Array.isArray(value);
            })
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
            .flatMap(([_, value]) => value);

        return filteredErrors.length > 0 ? filteredErrors : null;
    }

    return null;
});
const onInput = (value) => {
    emit('update:inputValue', value);
};
const displayText = computed(() => {
    return selectedExt.value ?? props.placeholder ?? props.name;
});
watch(
    () => props.selectValue,
    (value) => {
        emit('blur:select', value);
    }
);
onMounted(async () => {
    if (!$settingStore.settings) await $settingStore.fetchSettings();

    if ($settingStore.defaultCountry && (!props.selectValue || props.selectValue === '')) {
        emit('update:selectValue', String($settingStore.defaultCountry.id).toString());
    }
});
</script>
