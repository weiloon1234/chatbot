<template>
    <div
        v-if="formReady"
        class="w-full flex flex-col space-y-4"
    >
        <form-input v-model="formState.sorting" :errors="formError.sorting" :label="$t('Sorting (descending order)')" />
        <form-input
            v-for="l in $locales"
            :key="`name-${l}`"
            v-model="formState[`name_${l}`]"
            :errors="formError[`name_${l}`]" :label="`${$t('Name')} (${$t(`Locale ${l}`)})`"
        />
        <form-select v-model="formState.main_display_style" :errors="formError.main_display_style" :label="$t('Main display style')" :options="$helper.getEnumOptions('ArticleCategory.main_display_style')" />
        <form-select v-model="formState.main_display_show_title" :errors="formError.main_display_show_title" :label="$t('Main display show title')" :options="$helper.getEnumOptions('ArticleCategory.main_display_show_title')" />
        <form-select v-model="formState.main_display_show_more" :errors="formError.main_display_show_more" :label="$t('Main display show more')" :options="$helper.getEnumOptions('ArticleCategory.main_display_show_more')" />
        <form-select v-model="formState.list_display_style" :errors="formError.list_display_style" :label="$t('List display style')" :options="$helper.getEnumOptions('ArticleCategory.list_display_style')" />
        <form-select v-model="formState.details_show_article_cover" :errors="formError.details_show_article_cover" :label="$t('Details show cover image')" :options="$helper.getEnumOptions('ArticleCategory.details_show_article_cover')" />
        <form-select v-model="formState.details_show_article_datetime" :errors="formError.details_show_article_datetime" :label="$t('Details show date time')" :options="$helper.getEnumOptions('ArticleCategory.details_show_article_datetime')" />
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

const defaultData = ref({});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/article/category/submit_form');
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
    if (props.model !== null && props.model.id) {
        try {
            const data = await axios.post('/article/category/build_form', { id: props.model?.id ?? '' });

            if (data.model) {
                defaultData.value = {...data.model};
            }
        } catch (e) {
            console.error(e);
        }
    }
});
</script>
