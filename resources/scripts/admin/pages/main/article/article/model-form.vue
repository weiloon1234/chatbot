<template>
    <admin-content-card>
        <div
            v-if="formReady"
            class="w-full flex flex-col space-y-4"
        >
            <form-select
                v-model="formState.article_category_id"
                :errors="formError.article_category_id"
                name="article_category_id"
                :label="$t('Category')"
                :options="categoryOptions"
            />
            <form-input
                v-model="formState.sorting"
                :errors="formError.sorting"
                type="number"
                name="sorting"
                :label="$t('Sorting (descending order)')"
                number
            />
            <form-input
                v-for="l in $locales"
                :key="`subject-${l}`"
                v-model="formState[`subject_${l}`]"
                :name="`subject_${l}`"
                :errors="formError[`subject_${l}`]"
                :label="`${$t('Subject')} (${$t(`Locale ${l}`)})`"
            />
            <form-input
                v-for="l in $locales"
                :key="`description-${l}`"
                v-model="formState[`description_${l}`]"
                :name="`description_${l}`"
                :errors="formError[`description_${l}`]"
                :label="`${$t('Description')} (${$t(`Locale ${l}`)})`"
            />
            <form-editor
                v-for="l in $locales"
                :key="`content-${l}`"
                folder="article"
                v-model="formState[`content_${l}`]"
                :name="`content_${l}`"
                :errors="formError[`content_${l}`]"
                :label="`${$t('Content')} (${$t(`Locale ${l}`)})`"
            />
            <form-file
                v-for="l in $locales"
                :key="`cover-${l}`"
                v-model="formState[`cover_${l}`]"
                :name="`cover_${l}`"
                :errors="formError[`cover_${l}`]" :label="`${$t('Cover image')} (${$t(`Locale ${l}`)})`"
                :preview-file="model?.[`cover_${l}`]"
            />
            <!-- Footer buttons - using reusable modal-footer class -->
            <div class="modal-footer grid grid-cols-2 gap-4">
                <auto-button
                    type="plain"
                    :busy="formBusy"
                    @click="$router.push({ name: 'admin.article.article.list' })"
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
    </admin-content-card>
</template>

<script setup>
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { computed, inject, onMounted, ref } from "vue";
import FormInput from "#/shared/components/form/form-input.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";
import axios from "axios";
import AdminContentCard from "#/admin/components/admin-content-card.vue";
import { useRoute, useRouter } from "vue-router";
import FormEditor from "#/shared/components/form/form-editor.vue";
import FormFile from "#/shared/components/form/form-file.vue";
import FormSelect from "#/shared/components/form/form-select.vue";

const $helper = inject('$helper');
const $route = useRoute();
const $router = useRouter();
const $locale = inject('$locale');

const model = ref(null);
const categories = ref([]);

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(model);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/article/article/submit_form');
        await $helper.alertSuccess({
            message: data.message,
            callback: async () => {
                await $router.push({ name: 'admin.article.article.list' });
            }
        });
    } catch (e) {
        console.error(e);
    }
};

const categoryOptions = computed(() => {
    return categories.value.map((category) => {
        return {
            text: category[`name_${$locale}`],
            value: String(category.id)
        };
    });
});

onMounted(async () => {
    try {
        const data = await axios.post('/article/article/build_form', { id: $route.params.id ?? '' });

        if (data.model) {
            model.value = { ...data.model };
        }

        if (data.categories) {
            categories.value = data.categories;
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
