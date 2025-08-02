<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-input v-model="formState.username" :errors="formError.username" :label="$t('Username')" />
        <form-input v-model="formState.email" :errors="formError.email" :label="$t('Email')" />
        <form-input v-model="formState.name" :errors="formError.name" :label="$t('Name')" />
        <template
            v-if="!model?.id || (model.id && ![1,2].includes(parseInt(model.type)))"
        >
            <form-select v-model="formState.admin_group_id" :errors="formError.admin_group_id" :label="$t('Group')" :options="groups" />
        </template>
        <form-password v-model="formState.password" :errors="formError.password" :label="$t('Password')" :placeholder="model?.id ? $t('Leave blank if not changing') : ''" />
        <form-password v-model="formState.password_confirmation" :errors="formError.password_confirmation" :label="$t('Password confirmation')" :placeholder="model?.id ? $t('Leave blank if not changing') : ''" />
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
    </div>
</template>

<script setup>
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { inject, onMounted, ref } from "vue";
import FormInput from "#/shared/components/form/form-input.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";
import axios from "axios";
import FormSelect from "#/shared/components/form/form-select.vue";
import FormPassword from "#/shared/components/form/form-password.vue";

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
    group_name: '',
    permissions: {},
});
const groups = ref([]);

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/management/admin/submit_form');
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
        const data = await axios.post('/management/admin/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {
                id: props.model?.id ?? '',
                username: data.model.username,
                email: data.model.email,
                name: data.model.name,
                type: data.type,
                admin_group_id: String(data.model.admin_group_id),
            };
        }

        if (data.groups) {
            groups.value = data.groups.map((item) => {
                return {
                    value: String(item.id),
                    text: item.group_name,
                }
            });
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
