<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-select v-model="formState.status" :errors="formError.status" :label="$t('Status')" :options="$helper.getEnumOptions('Country.status')" />
        <form-input v-model="formState.iso2" :errors="formError.iso2" :label="$t('ISO 2')" readonly disabled />
        <form-input v-model="formState.name" :errors="formError.name" :label="$t('Name')" readonly disabled />
        <form-input v-model="formState.phone_code" :errors="formError.phone_code" :label="$t('Phone code')" readonly disabled />
        <form-input v-model="formState.rate_to_base" :errors="formError.rate_to_base" :label="$t('Rate to base')" fund />
        <teleport to="#modal-footer">
            <div class="grid grid-cols-2 gap-4">
                <auto-button
                    :busy="formBusy"
                    @click="onSubmitForm"
                >
                    {{ $t('Submit') }}
                </auto-button>
                <auto-button
                    type="plain"
                    :busy="formBusy"
                    @click="emit('close')"
                >
                    {{ $t('Cancel') }}
                </auto-button>
            </div>
        </teleport>
    </div>
</template>

<script setup>
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { inject, onMounted, ref } from "vue";
import FormInput from "#/shared/components/form/form-input.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";
import axios from "axios";
import FormSelect from "#/shared/components/form/form-select.vue";

const props = defineProps({
    model: {
        type: Object,
        required: false,
        default: () => null,
    },
});
const emit = defineEmits(['success', 'close']);

const $helper = inject('$helper');

const defaultData = ref({
    iso2: '',
    name: '',
    phone_code: '',
    currency_code: '',
    rate_to_base: '',
    status: '',
});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/other/country/submit_form');
        await $helper.alertSuccess({
            message: data.message,
            callback: async () => {
                emit('success');
            }
        });
    } catch (e) {
        console.error(e);
    }
};

onMounted(async () => {
    try {
        const data = await axios.post('/other/country/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {...data.model};
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
