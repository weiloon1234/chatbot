<template>
    <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center space-y-5">
        <div class="font-semibold text-lg">
            {{ $t('Sign in') }}
        </div>
        <form-input v-model="formState.username" :errors="formError.username" :label="$t('Username')" />
        <form-password v-model="formState.password" :errors="formError.password" :label="$t('Password')" />
        <auto-button
            :busy="formBusy"
            @click="handleSubmit"
        >
            Submit
        </auto-button>
    </div>
</template>
<script setup>
import { inject, ref } from "vue";
import AutoButton from "#/shared/components/button/auto-button.vue";
import FormInput from "#/shared/components/form/form-input.vue";
import FormPassword from "#/shared/components/form/form-password.vue";
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";

const $role = inject('$role');
const $router = useRouter();
const $helper = inject('$helper');
const $accountStore = inject('$accountStore');
const $i18n = useI18n();
const defaultData = ref({
    username: "",
    password: "",
});
const { formState, formBusy, formError, submitForm } = useAutoForm(defaultData.value);

const handleSubmit = async () => {
    try {
        const data = await submitForm('/login');

        if (data.token) {
            await $accountStore.loginAccount(data.token);
            await $helper.alertSuccess({
                message: $i18n.t('Sign in successfully'),
                callback: async () => {
                    await $router.replace({name: `${$role}.home`});
                }
            });
        }
    } catch (e) {
        console.error(e);
    }
};
</script>
