<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-input disabled readonly :model-value="formState.username" :label="$t('Username')" />
        <form-password v-model="formState.new_password" :errors="formError.new_password" :label="$t('New password')" />
        <form-password v-model="formState.new_password_confirmation" :errors="formError.new_password_confirmation" :label="$t('New password confirmation')" />
        <form-password v-model="formState.current_password" :errors="formError.current_password" :label="$t('Current password')" />
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
    </div>
</template>

<script setup>
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { computed, inject, onMounted, ref } from "vue";
import FormInput from "#/shared/components/form/form-input.vue";
import FormPassword from "#/shared/components/form/form-password.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";

const emit = defineEmits(['success', 'close']);

const $accountStore = inject('$accountStore');
const $admin = computed(() => $accountStore.account);
const $helper = inject('$helper');

const defaultData = ref({
    username: '',
    new_password: '',
    new_password_confirmation: '',
    current_password: '',
});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/security_submit');
        await $helper.alertSuccess({
            message: data.message,
            callback: async () => {
                emit('success');
                await $accountStore.fetchAccount();
            }
        });
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    defaultData.value = {
        username: $admin.value.username,
    };
});
</script>
