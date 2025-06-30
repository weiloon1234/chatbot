<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-select
            v-if="defaultData.setting_type === 'select'"
            v-model="formState.setting_value" :errors="formError.setting_value" :label="$t('Value')" :options="selectOptions"
        />
        <form-text-area
            v-else-if="defaultData.setting_type === 'textarea'"
            v-model="formState.setting_value" :errors="formError.setting_value" :label="$t('Value')"
        />
        <form-input
            v-else-if="defaultData.setting_type === 'number'"
            v-model="formState.setting_value"
            type="number"
            :errors="formError.setting_value"
            v-integer
            :label="$t('Value')"
        />
        <form-input
            v-else-if="defaultData.setting_type === 'fund'"
            v-model="formState.setting_value"
            type="number"
            :errors="formError.setting_value"
            fund
            :label="$t('Value')"
        />
        <form-input
            v-else-if="defaultData.setting_type === 'email'"
            v-model="formState.setting_value"
            type="email"
            :errors="formError.setting_value"
            :label="$t('Value')"
        />
        <form-editor
            v-else-if="defaultData.setting_type === 'editor'"
            v-model="formState.setting_value"
            :errors="formError.setting_value"
            :label="$t('Value')"
        />
        <form-file
            v-else-if="defaultData.setting_type === 'image'"
            v-model="formState.setting_value"
            :errors="formError.setting_value"
            :preview-file="formState.setting_value"
            accept="image/*"
            folder="setting"
            :label="$t('Value')"
        />
        <form-input
            v-else
            v-model="formState.setting_value"
            :type="defaultData.setting_type"
            :errors="formError.setting_value"
            :label="$t('Value')"
        />
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
import AutoButton from "#/shared/components/button/auto-button.vue";
import axios from "axios";
import FormInput from "#/shared/components/form/form-input.vue";
import FormTextArea from "#/shared/components/form/form-textarea.vue";
import FormSelect from "#/shared/components/form/form-select.vue";
import FormEditor from "#/shared/components/form/form-editor.vue";
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

const defaultData = ref({});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const selectOptions = ref([]);
const onSubmitForm = async () => {
    try {
        const data = await submitForm('/other/setting/submit_form');
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
        const data = await axios.post('/other/setting/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {...data.model};

            if (data.model.setting_type === 'select') {
                selectOptions.value = [];
                Object.keys(data.model.params).forEach(key => {
                    selectOptions.value.push({
                        text: String(data.model.params[key]),
                        value: String(key),
                    });
                });
            }
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
