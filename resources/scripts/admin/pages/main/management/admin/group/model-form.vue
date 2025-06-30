<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-input v-model="formState.group_name" :errors="formError.group_name" :label="$t('Name')" />
        <div class="lg:grid lg:grid-cols-2 lg:gap-2">
            <form-checkbox
                v-for="(permission, index) in permissions"
                :key="`permission-${index}`"
                v-model="formState[`permissions[${permission}]`]"
                :name="`permissions[${permission}]`"
                check-value="yes"
                uncheck-value="no"
                :label="$t(permission)"
            />
        </div>
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
import FormCheckbox from "#/shared/components/form/form-checkbox.vue";

const props = defineProps({
    model: {
        type: Object,
        required: false,
        default: () => null,
    },
});
const emit = defineEmits(['success', 'close']);

const $accountStore = inject('$accountStore');
const $helper = inject('$helper');

const defaultData = ref({
    group_name: '',
    permissions: {},
});
const permissions = ref([]);

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/management/admin_group/submit_form');
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

onMounted(async () => {
    try {
        const data = await axios.post('/management/admin_group/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {
                id: props.model?.id ?? '',
                group_name: data.model.group_name,
                permissions: {},
            };

            data.model.permissions.forEach((permission) => {
                defaultData.value[`permissions[${permission.permission_tag}]`] = 'yes';
            });
        }

        if (data.permissions) {
            permissions.value = data.permissions;
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
