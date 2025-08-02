<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="UserWithdrawal"
            :search-filters="[
                { label: $i18n.t('User'), type: 'text', name: 'f-has-like-user-username' },
                { label: $i18n.t('Admin'), type: 'text', name: 'f-has-like-admin-username' },
                { label: $i18n.t('Bank'), type: 'text', name: 'f-locale-has-like-bank-name' },
                { label: $i18n.t('Bank account holder name'), name: 'f-like-bank_account_holder_name' },
                { label: $i18n.t('Bank account number'), name: 'f-like-bank_account_number' },
                { label: $i18n.t('Method'), type: 'select', name: 'f-withdraw_method', options: $helper.getEnumOptions('Withdrawal.user_withdraw_method') },
                { label: $i18n.t('Country'), type: 'select', name: 'f-country_id', options: $settingStore.countryForOptions },
                { label: $i18n.t('Status'), type: 'select', name: 'f-status', options: $helper.getEnumOptions('Withdrawal.status') },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('User'), column: 'user_id', export_column: 'user_identity' },
                { label: $i18n.t('Admin'), column: 'admin_id', export_column: 'admin_username' },
                { label: $i18n.t('Country'), column: 'country_id', export_column: 'country_name' },
                { label: $i18n.t('Bank'), column: 'bank_id', export_column: 'bank_name' },
                { label: $i18n.t('Bank account holder name'), column: 'bank_account_holder_name' },
                { label: $i18n.t('Bank account number'), column: 'bank_account_number' },
                { label: $i18n.t('Conversion rate'), column: 'conversion_rate' },
                { label: $i18n.t('Amount'), column: 'credit_amount' },
                { label: $i18n.t('Admin fees'), column: 'admin_fees' },
                { label: $i18n.t('Currency'), column: 'receivable_currency_amount', export_column: 'receivable_currency_amount_explained' },
                { label: $i18n.t('Method'), column: 'withdraw_method', export_column: 'withdraw_method_explained' },
                { label: $i18n.t('Status'), column: 'status', export_column: 'status_explained' },
                { label: $i18n.t('Updated at'), column: 'updated_at' },
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
                            <font-awesome-icon icon="fas fa-pencil" />
                        </auto-button>
                    </td>
                    <td>{{ record.user_identity }}</td>
                    <td>{{ record.admin_username }}</td>
                    <td>{{ record.country_name }}</td>
                    <td>{{ record.bank_name }}</td>
                    <td>{{ record.bank_account_holder_name }}</td>
                    <td>{{ record.bank_account_number }}</td>
                    <td>{{ $helper.fundFormat(record.conversion_rate) }}</td>
                    <td>{{ $helper.fundFormat(record.credit_amount) }}</td>
                    <td>{{ $helper.fundFormat(record.admin_fees) }}</td>
                    <td>{{ record.receivable_currency_amount_explained }}</td>
                    <td>{{ record.withdraw_method_explained }}</td>
                    <td>{{ record.status_explained }}</td>
                    <td>{{ $helper.dateTimeFormat(record.updated_at) }}</td>
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
        title: m ? 'Edit Withdrawal' : 'Create Withdrawal' 
    });
};

const onFormClose = () => {
    $modalStore.close();
    dt.value.fetchData();
};
</script>
