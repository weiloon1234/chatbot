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
        <div class="form-input-wrapper">
            <input
                v-if="fund"
                v-decimal
                :type="type"
                :name="name"
                :value="modelValue"
                :readonly="disabled || readonly"
                :placeholder="placeholder"
                class="form-input"
                @input="(event) => onInput(event.target.value)"
                @blur="emit('blur')" />
            <input
                v-else-if="integer"
                v-integer
                :type="type"
                :name="name"
                :value="modelValue"
                :readonly="disabled || readonly"
                :placeholder="placeholder"
                class="form-input"
                @input="(event) => onInput(event.target.value)"
                @blur="emit('blur')" />
            <input
                v-else-if="money"
                v-model.lazy="lazyValue"
                v-money3="moneyConfig"
                type="tel"
                :name="name"
                :readonly="disabled || readonly"
                :placeholder="placeholder"
                class="form-input"
                @blur="emit('blur')" />
            <input
                v-else
                :type="type"
                :name="name"
                :value="modelValue"
                :readonly="disabled || readonly"
                :placeholder="placeholder"
                class="form-input"
                @input="(event) => onInput(event.target.value)"
                @blur="emit('blur')" />
        </div>
        <div v-if="searchStatus.isSearching" class="search-loading pl-2">
            <span class="loading-text text-sm font-semibold">{{ $t('Searching') }}...</span>
        </div>
        <div v-if="!searchStatus.isSearching && String(searchStatus.result).length" class="search-result pl-2">
            <div class="results-label text-sm font-semibold">{{ searchStatus.result }}</div>
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
import { computed, ref, useSlots } from "vue";
import FormFieldNote from '#/shared/components/form/form-field-note.vue';
import FormFieldError from '#/shared/components/form/form-field-error.vue';
import FormFieldLabel from '#/shared/components/form/form-field-label.vue';
import {debounce} from "underscore";
import axios from "axios";

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
    integer: {
        required: false,
        type: Boolean,
        default: false,
    },
    fund: {
        required: false,
        type: Boolean,
        default: false,
    },
    money: {
        required: false,
        type: Boolean,
        default: false,
    },
    moneyConfig: {
        required: false,
        type: Object,
        default: () => {
            return {
                masked: false,
                prefix: '',
                suffix: '',
                thousands: '',
                decimal: '.',
                precision: 2,
                disableNegative: true,
                disabled: false,
                min: null,
                max: null,
                allowBlank: false,
                minimumNumberOfCharacters: 0,
                shouldRound: false,
                focusOnRight: false,
            };
        },
    },
    searchEndpoint: {
        required: false,
        type: String,
        default: null
    },
    searchDebounce: {
        required: false,
        type: Number,
        default: 300
    },
    searchResultKey: {
        required: false,
        type: String,
        default: null
    },
    minSearchLength: {
        required: false,
        type: Number,
        default: 3
    },
    enableSearchHints: {
        required: false,
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['blur', 'update:modelValue', 'search-result', 'search-error']);

const $slots = useSlots();

const searchStatus = ref({
    searching: false,
    result: '',
    lastQuery: '',
});
const activeRequest = ref(null);

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
    set: async (v) => {
        emit('update:modelValue', v);
        if (props.enableSearchHints && props.searchEndpoint) {
            searchStatus.value.result = '';
            await performSearch(value);
        }
    }
});
const onInput = async (value) => {
    emit('update:modelValue', value);
    if (props.enableSearchHints && props.searchEndpoint) {
        searchStatus.value.result = '';
        await performSearch(value);
    }
};
const performSearch = debounce(async(query) => {
    if (query.length < props.minSearchLength) {
        return;
    }

    try {
        if (activeRequest.value) {
            activeRequest.value.cancel('New search initiated');
        }

        searchStatus.value = {
            isSearching: true,
            result: '',
            lastQuery: query
        };

        activeRequest.value = axios.CancelToken.source();

        const data = await axios.post(
            props.searchEndpoint,
            { query },
            { cancelToken: activeRequest.value.token }
        );

        searchStatus.value = {
            isSearching: false,
            result: data[props.searchResultKey] ?? '',
            lastQuery: query
        };

        emit('search-result', searchStatus.value.result);
    } catch (error) {
        if (!axios.isCancel(error)) {
            searchStatus.value = {
                isSearching: false,
                result: '',
                lastQuery: '',
            };

            emit('search-error', error);
        }
    }
}, props.searchDebounce);
</script>
