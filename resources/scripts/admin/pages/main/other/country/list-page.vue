<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="Country"
            :search-filters="[
                { label: $i18n.t('ISO 2'), type: 'text', name: 'f-like-iso2' },
                { label: $i18n.t('Name'), type: 'text', name: 'f-like-name' },
                { label: $i18n.t('Phone code'), type: 'text', name: 'f-like-phone_code' },
                { label: $i18n.t('Currency code'), type: 'text', name: 'f-like-currency_code' },
                { label: $i18n.t('Status'), type: 'select', name: 'f-status', options: $helper.getEnumOptions('Country.status') },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('ISO 2'), column: 'ext' },
                { label: $i18n.t('Name'), column: 'name' },
                { label: $i18n.t('Phone code'), column: 'phone_code' },
                { label: $i18n.t('Currency code'), column: 'currency_code' },
                { label: $i18n.t('Rate to base'), column: 'rate_to_base' },
                { label: $i18n.t('Status'), column: 'status_explained' },
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
                    <td>{{ record.iso2 }}</td>
                    <td>{{ record.name }}</td>
                    <td>{{ record.phone_code }}</td>
                    <td>{{ record.currency_code }}</td>
                    <td>{{ $helper.fundFormat(record.rate_to_base, 5) }}</td>
                    <td>{{ record.status_explained }}</td>
                    <td>{{ $helper.dateTimeFormat(record.updated_at) }}</td>
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
