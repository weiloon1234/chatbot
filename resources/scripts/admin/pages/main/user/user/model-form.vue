<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <template
            v-if="!model?.id || (model.id && model.introducer_user_id)"
        >
            <form-input
                v-model="formState.introducer_username"
                :errors="formError.introducer_username"
                :label="$t('Introducer')"
                :disabled="!!(model?.id && model.introducer_user_id)"
                :readonly="!!(model?.id && model.introducer_user_id)"
                search-endpoint="/user/user/check_user"
                search-result-key="name"
                :enable-search-hints="true"
            />
        </template>
        <form-input v-model="formState.username" :errors="formError.username" :label="$t('Username')" />
        <form-input v-model="formState.email" :errors="formError.email" :label="$t('Email')" />
        <form-input v-model="formState.name" :errors="formError.name" :label="$t('Name')" />
        <form-select v-model="formState.country_id" :errors="formError.country_id" :label="$t('Country')" :options="$settingStore.countryForOptions" />
        <form-contact v-model:select-value="formState.contact_country_id" v-model:input-value="formState.contact_number" :errors="formError" :label="$t('Contact number')" />
        <template
            v-if="model === null"
        >
            <div class="flex flex-col space-y-2 lg:grid lg:grid-cols-2 lg:gap-2 lg:space-y-0">
                <form-password v-model="formState.password" :errors="formError.password" :label="$t('Password')" />
                <form-password v-model="formState.password_confirmation" :errors="formError.password_confirmation" :label="$t('Password confirmation')" />
            </div>
            <div class="flex flex-col space-y-2 lg:grid lg:grid-cols-2 lg:gap-2 lg:space-y-0">
                <form-pin v-model="formState.password2" :errors="formError.password2" :label="$t('Password2')" />
                <form-pin v-model="formState.password2_confirmation" :errors="formError.password2_confirmation" :label="$t('Password2 confirmation')" />
            </div>
        </template>
        <template
            v-else
        >
            <div class="flex flex-col space-y-2 lg:grid lg:grid-cols-2 lg:gap-2 lg:space-y-0">
                <form-password v-model="formState.new_password" :errors="formError.new_password" :label="$t('New password')" />
                <form-password v-model="formState.new_password_confirmation" :errors="formError.new_password_confirmation" :label="$t('New password confirmation')" />
            </div>
            <div class="flex flex-col space-y-2 lg:grid lg:grid-cols-2 lg:gap-2 lg:space-y-0">
                <form-pin v-model="formState.new_password2" :errors="formError.new_password2" :label="$t('New password2')" />
                <form-pin v-model="formState.new_password2_confirmation" :errors="formError.new_password2_confirmation" :label="$t('New password2 confirmation')" />
            </div>
        </template>
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
import FormContact from "#/shared/components/form/form-contact.vue";
import FormPassword from "#/shared/components/form/form-password.vue";
import FormPin from "#/shared/components/form/form-pin.vue";

const props = defineProps({
    model: {
        type: Object,
        required: false,
        default: () => null,
    },
});
const emit = defineEmits(['success', 'close']);

const $helper = inject('$helper');
const $settingStore = inject('$settingStore');

const defaultData = ref({
    country_id: '',
});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/user/user/submit_form');
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
        const data = await axios.post('/user/user/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {...data.model};
            defaultData.value.introducer_username = data.model.introducer?.username;
        }

        await $settingStore.fetchSettings();
    } catch (e) {
        console.error(e);
    }
});
</script>
