<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-input
            v-for="l in $locales"
            :key="`name-${l}`"
            v-model="formState[`name_${l}`]"
            :errors="formError[`name_${l}`]" :label="`${$t('Name')} (${$t(`Locale ${l}`)})`"
        />
        <form-select v-model="formState.country_id" :errors="formError.country_id" :label="$t('Country')" :options="countries" />
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
    country_id: '',
});
const countries = ref([]);

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/other/bank/submit_form');
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
        const data = await axios.post('/other/bank/build_form', { id: props.model?.id ?? '' });

        if (data.model) {
            defaultData.value = {...data.model};
        }

        if (data.country) {
            countries.value = data.country.map((item) => {
                return {
                    value: String(item.id),
                    text: item.name,
                };
            });
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
