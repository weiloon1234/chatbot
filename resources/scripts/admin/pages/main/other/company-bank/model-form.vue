<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >

        <form-select v-model="formState.bank_id" :errors="formError.bank_id" :label="$t('Bank')" :options="banks" />
        <form-input v-model="formState.account_name" :errors="formError.account_name" :label="$t('Bank account holder name')" />
        <form-input v-model="formState.account_number" :errors="formError.account_number" :label="$t('Bank account number')" />
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
const $locale = inject('$locale');

const defaultData = ref({});
const banks = ref([]);

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/other/company_bank/submit_form');
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
        const data = await axios.post('/other/company_bank/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {...data.model};
        }

        if (data.banks) {
            banks.value = data.banks.map((item) => {
                return {
                    value: String(item.id),
                    text: `[${item.country?.name}] ${item[`name_${$locale}`]}`,
                };
            });
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
