<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="Bank"
            :search-filters="[
                { label: $i18n.t('Name'), type: 'text', name: 'f-locale-like-name' },
                { label: $i18n.t('Country'), type: 'text', name: 'f-has-like-country-name' },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('Name'), column: `name_${$locale}` },
                { label: $i18n.t('Country'), column: 'country_name' },
                { label: $i18n.t('Updated at'), column: 'updated_at' },
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
                            <font-awesome-icon icon="fas fa-pencil" />
                        </auto-button>
                        <auto-button
                            type="danger"
                            small
                            :full="false"
                            @click="onDelete(record)"
                        >
                            <font-awesome-icon icon="fas fa-trash" />
                        </auto-button>
                    </td>
                    <td>{{ record[`name_${$locale}`] }}</td>
                    <td>{{ record.country_name }}</td>
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
import axios from "axios";

const $i18n = useI18n();
const $helper = inject('$helper');
const $modalStore = inject('$modalStore');

const dt = ref();

const onFormOpen = (m = null) => {
    $modalStore.open(ModelForm, { model: m }, { 
        title: m ? 'Edit Bank' : 'Create Bank' 
    });
};

const onFormClose = () => {
    $modalStore.close();
    dt.value.fetchData();
};
const onDelete = async (record) => {
    await $helper.alertConfirm({
        callback: async (result) => {
            if (result.isConfirmed) {
                try {
                    const data = await axios.post('/other/bank/delete', { id: record.id });
                    await $helper.alertSuccess({
                        message: data.message,
                        callback: async () => {
                            await dt.value.fetchData();
                        }
                    });
                } catch (e) {
                    console.error(e);
                }
            }
        }
    });
}
</script>
