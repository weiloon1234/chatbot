<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-select
            v-if="parseInt(model.status) === 0"
            v-model="formState.status" :errors="formError.status" :label="$t('Status')" :options="$helper.getEnumOptions('Deposit.status')"
        />
        <form-input v-model="formState.user_identity" :errors="formError.name" :label="$t('User')" readonly disabled />
        <form-input v-model="formState.country_name" :errors="formError.country_name" :label="$t('Bank')" readonly disabled />
        <form-input v-model="formState.bank_name" :errors="formError.bank_name" :label="$t('Country')" readonly disabled />
        <form-input v-model="formState.bank_account_holder_name" :errors="formError.bank_account_holder_name" :label="$t('Bank account holder name')" readonly disabled />
        <form-input v-model="formState.bank_account_number" :errors="formError.bank_account_number" :label="$t('Bank account number')" readonly disabled />
        <form-input v-model="formState.credit_type_explained" :errors="formError.credit_type_explained" :label="$t('Credit')" readonly disabled />
        <form-input v-model="formState.conversion_rate" :errors="formError.conversion_rate" :label="$t('Conversion rate')" readonly disabled />
        <form-input v-model="formState.credit_amount" :errors="formError.credit_amount" :label="$t('Amount')" readonly disabled />
        <form-input v-model="formState.currency_amount" :errors="formError.currency_amount" :label="$t('Currency amount')" readonly disabled />
        <form-input v-model="formState.deposit_method_explained" :errors="formError.deposit_method_explained" :label="$t('Method')" readonly disabled />
        <form-file v-model="formState.dummy" :label="$t('Receipts')" :preview-file="receipts" readonly disabled />
        <form-select
            v-if="parseInt(model.status) !== 0"
            v-model="formState.status" :errors="formError.status" :label="$t('Status')" :options="$helper.getEnumOptions('Deposit.status')" readonly disabled
        />
        <template
            v-if="parseInt(model.status) === 0"
        >
            <!-- Footer buttons - using reusable modal-footer class -->
            <div class="modal-footer grid grid-cols-2 gap-4">
                <auto-button
                    type="plain"
                    :busy="formBusy"
                    @click="emit('close')"
                >
                    {{ $t('Cancel') }}
                </auto-button>
                <auto-button
                    :busy="formBusy"
                    @click="onSubmitForm"
                >
                    {{ $t('Submit') }}
                </auto-button>
            </div>
        </template>
    </div>
</template>

<script setup>
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { computed, inject, onMounted, ref } from "vue";
import FormInput from "#/shared/components/form/form-input.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";
import axios from "axios";
import FormSelect from "#/shared/components/form/form-select.vue";
import FormFile from "#/shared/components/form/form-file.vue";

const props = defineProps({
    model: {
        type: Object,
        required: false,
        default: () => null,
    },
});
const emit = defineEmits(['success', 'close']);

const $helper = inject('$helper');
const $locale = inject('$locale');

const defaultData = ref({
    name: '',
    ext: '',
    currency_prefix: '',
    conversion_rate: '',
    status: '',
});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/user/deposit/submit_form');
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

const receipts = computed(() => {
    if (defaultData.value.receipts) {
        return defaultData.value.receipts.map((receipt) => {
            return receipt.file_path;
        });
    }
    return null;
});

onMounted(async () => {
    try {
        const data = await axios.post('/user/deposit/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {
                ...data.model,
                user_identity: data.model.user?.identity,
                bank_name: data.model.bank?.[`name_${$locale}`],
                country_name: data.model.country?.name,
                conversion_rate: $helper.fundFormat(data.model.conversion_rate),
                credit_amount: $helper.fundFormat(data.model.credit_amount),
                currency_amount: $helper.explainCurrency(data.model.country, data.model.currency_amount),
            }
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
