<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-input
            v-if="isRead"
            v-model="formState.user_identity"
            :errors="formError.user_identity"
            :label="$t('User')"
            :disabled="isRead"
            :readonly="isRead"
        />
        <form-input
            v-else
            v-model="formState.username"
            :errors="formError.username"
            :label="$t('Username')"
            search-endpoint="/user/user/check_user"
            search-result-key="name"
            :enable-search-hints="true"
            :disabled="isRead"
            :readonly="isRead"
        />
        <form-select v-model="formState.transaction_type" :errors="formError.transaction_type" :label="$t('Operation')" :options="$helper.getEnumOptions('AdminAdjustUserCredit.transaction_type')" :disabled="isRead" />
        <form-select v-model="formState.credit_type" :errors="formError.credit_type" :label="$t('Credit')" :options="$helper.getEnumOptions('AdminAdjustUserCredit.credit_type')" :disabled="isRead" />
        <form-input v-model="formState.amount" :errors="formError.amount" :label="$t('Amount')" fund :disabled="isRead" />
        <form-textarea v-model="formState.remark" :errors="formError.remark" :label="$t('Remark')" :disabled="isRead" />
        <teleport to="#modal-footer">
            <div v-if="!isRead" class="grid grid-cols-2 gap-4">
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
import { computed, inject, onMounted, ref } from "vue";
import useAutoForm from "#/shared/composables/use-auto-form.js";
import AutoButton from "#/shared/components/button/auto-button.vue";
import FormInput from "#/shared/components/form/form-input.vue";
import FormSelect from "#/shared/components/form/form-select.vue";
import FormTextarea from "#/shared/components/form/form-textarea.vue";

const props = defineProps({
    model: {
        type: Object,
        required: false,
        default: () => null,
    },
});
const emit = defineEmits(['success', 'close']);

const $helper = inject('$helper');

const defaultData = ref({});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const isRead = computed(() => {
    return props.model !== null && props.model.id > 0;
});

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/user/credit/submit_form');
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
        if (props.model) {
            defaultData.value = {
                ...props.model,
                amount: $helper.fundFormat(props.model.amount),
            };
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
