<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="Setting"
            :search-created-at="false"
            :search-filters="[
                { label: $i18n.t('Name'), type: 'text', name: 'f-like-setting' },
                { label: $i18n.t('Type'), type: 'select', name: 'f-setting_type', options: $helper.getEnumOptions('Setting.setting_type') },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $t('Setting'), column: 'setting', export_column: 'setting_name' },
                { label: $t('Type'), column: 'setting_type' },
                { label: $i18n.t('Updated at'), column: 'updated_at' },
            ]"
        >
            <template #default="{records}">
                <tr v-for="(record, index) in records" :key="record.id">
                    <td>{{ index + 1 }}</td>
                    <td class="flex justify-start items-center space-x-2">
                        <auto-button
                            small
                            :full="false"
                            @click="onFormOpen(record)"
                        >
                            <font-awesome-icon icon="fas fa-pencil" />
                        </auto-button>
                    </td>
                    <td>{{ record.setting_name }}</td>
                    <td>{{ record.setting_type }}</td>
                    <td>{{ $helper.dateTimeFormat(record.updated_at) }}</td>
                </tr>
            </template>
        </auto-datatable>
        <auto-modal
            v-if="formOpened"
            small
            @close="onFormClose"
        >
            <model-form
                :model="model"
                @close="onFormClose"
                @success="onFormClose"
            />
        </auto-modal>
    </div>
</template>

<script setup>
import AutoDatatable from '#/shared/components/auto-datatable.vue';
import { useI18n } from 'vue-i18n';
import AutoButton from "#/shared/components/button/auto-button.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { ref, inject } from "vue";
import AutoModal from "#/shared/components/auto-modal.vue";
import ModelForm from "./model-form.vue";

const $i18n = useI18n();
const $helper = inject('$helper');

const dt = ref();
const model = ref(null);
const formOpened = ref(false);
const onFormOpen = (m = null) => {
    model.value = m;
    formOpened.value = true;
};
const onFormClose = () => {
    model.value = null;
    formOpened.value = false;
    dt.value.fetchData();
};
</script>
