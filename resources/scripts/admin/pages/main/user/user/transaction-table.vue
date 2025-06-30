<template>
    <auto-datatable
        model="UserCreditTransaction"
        :search-filters="[
            !userId ? { label: $i18n.t('User'), type: 'text', name: 'f-like-any-username|email|full_contact_number|name' } : null,
        ].filter(Boolean)"
        :hidden-data="[
            userId ? {value: userId, name: 'f-user_id'} : null,
            cid ? {value: cid, name: 'f-user_id'} : null
        ].filter(Boolean)"
        :headers="[
            { label: '#', column: 'id' },
            ...(!userId ? [
                { label: $i18n.t('Username'), column: 'user_id', export_column: 'user_identity' }
            ] : []),
            { label: $i18n.t('Description'), column: 'transaction_type_explained' },
            { label: $i18n.t('Amount'), column: 'amount' },
            { label: $i18n.t('Created at'), column: 'created_at' },
        ]"
    >
        <template #default="{records}">
            <tr v-for="(record, index) in records" :key="record.id">
                <td>{{ index + 1 }}</td>
                <td
                    v-if="!userId"
                >
                    {{ record.user_identity }}
                </td>
                <td>{{ record.transaction_type_explained }}</td>
                <td>{{ $helper.fundFormat(record.amount) }}</td>
                <td>{{ $helper.dateTimeFormat(record.created_at) }}</td>
            </tr>
        </template>
    </auto-datatable>
</template>
<script setup>
import AutoDatatable from "#/shared/components/auto-datatable.vue";
import { useI18n } from "vue-i18n";

defineProps({
    userId: {
        type: [Number, String],
        required: false,
    },
    cid: {
        type: [Number, String],
        required: false,
    },
});
const $i18n = useI18n();

</script>
