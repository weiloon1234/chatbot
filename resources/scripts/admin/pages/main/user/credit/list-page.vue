<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="AdminAdjustUserCredit"
            :search-filters="[
                { label: $i18n.t('Admin'), type: 'text', name: 'f-has-like-admin-username' },
                { label: $i18n.t('User'), type: 'text', name: 'f-has-like-user-username' },
                { label: $i18n.t('Operation'), type: 'select', name: 'f-transaction_type', options: $helper.getEnumOptions('AdminAdjustUserCredit.transaction_type') },
                { label: $i18n.t('Credit'), type: 'select', name: 'f-credit_type', options: $helper.getEnumOptions('AdminAdjustUserCredit.credit_type') },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('Admin'), column: 'admin_id', export_column: 'admin_username' },
                { label: $i18n.t('User'), column: 'user_identity' },
                { label: $i18n.t('Operation'), column: 'transaction_type', export_column: 'transaction_type_explained' },
                { label: $i18n.t('Credit'), column: 'credit_type', export_column: 'credit_type_explained' },
                { label: $i18n.t('Amount'), column: 'amount' },
                { label: $i18n.t('Remark'), column: 'remark' },
                { label: $i18n.t('Created at'), column: 'created_at' },
            ]"
        >
            <template #tools="{busy}">
                <auto-button
                    :busy="busy"
                    :full="false"
                    class="flex justify-center items-center space-x-2"
                    @click="onFormOpen(null)"
                >
                    <font-awesome-icon icon="fas fa-plus" />
                    <span>
                        {{ $t('Create') }}
                    </span>
                </auto-button>
            </template>
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
                    <td>{{ record.user_identity }}</td>
                    <td>{{ record.transaction_type_explained }}</td>
                    <td>{{ record.credit_type_explained }}</td>
                    <td>{{ $helper.fundFormat(record.amount) }}</td>
                    <td>{{ record.remark }}</td>
                    <td>{{ $helper.dateTimeFormat(record.created_at) }}</td>
                </tr>
            </template>
        </auto-datatable>
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
const $modalStore = inject('$modalStore');

const dt = ref();

const onFormOpen = (m = null) => {
    $modalStore.open(ModelForm, { model: m }, { 
        title: m ? 'Edit Credit Record' : 'Create Credit Record' 
    });
};

const onFormClose = () => {
    $modalStore.close();
    dt.value.fetchData();
};
</script>
