<template>
    <admin-content-card>
        <div
            v-if="formReady"
            class="w-full flex flex-col space-y-4"
        >
            <form-input v-model="formState.title" :errors="formError.title" :label="$t('Title')" :readonly="isSystem" :disabled="isSystem" />
            <form-editor
                v-for="l in $locales"
                :key="`content-${l}`"
                folder="page"
                v-model="formState[`content_${l}`]"
                :errors="formError[`content_${l}`]" :label="`${$t('Content')} (${$t(`Locale ${l}`)})`"
            />
            <!-- Footer buttons - using reusable modal-footer class -->
            <div class="modal-footer grid grid-cols-2 gap-4">
                <auto-button
                    type="plain"
                    :busy="formBusy"
                    @click="$router.push({ name: 'admin.other.page.list' })"
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

const $helper = inject('$helper');
const $route = useRoute();
const $router = useRouter();

const model = ref(null);

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(model);

const isSystem = computed(() => {
    return model.value && model.value.id && parseInt(model.value.is_system) === 1;
});

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/other/page/submit_form');
        await $helper.alertSuccess({
            message: data.message,
            callback: async () => {
                await $router.push({ name: 'admin.other.page.list' });
            }
        });
    } catch (e) {
        console.error(e);
    }
};

onMounted(async () => {
    if ($route.query.id) {
        try {
            const data = await axios.post('/other/page/build_form', { id: $route.params.id ?? '' });

            if (data.model) {
                model.value = { ...data.model };
            }
        } catch (e) {
            console.error(e);
        }
    }
});
</script>
