<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="AuditTrail"
            :search-filters="[
                { label: $i18n.t('Admin'), type: 'text', name: 'f-has-like-admin-username' },
                { label: $i18n.t('Data'), type: 'text', name: 'f-like-model_class' },
                { label: $i18n.t('Operation'), type: 'text', name: 'f-like-operation' },
                { label: $i18n.t('Description'), type: 'text', name: 'f-like-description' },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('Admin'), column: 'admin_username' },
                { label: $i18n.t('Data'), column: 'model_class' },
                { label: $i18n.t('Description'), column: 'description' },
                { label: $i18n.t('Operation'), column: 'operation' },
                { label: $i18n.t('Created at'), column: 'created_at' },
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
                            <font-awesome-icon icon="fas fa-eye" />
                        </auto-button>
                    </td>
                    <td>{{ record.admin_username }}</td>
                    <td>{{ record.model_class }}</td>
                    <td>{{ record.description }}</td>
                    <td>{{ record.operation }}</td>
                    <td>{{ $helper.dateTimeFormat(record.created_at) }}</td>
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
