<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="AdminGroup"
            :search-filters="[
                { label: $i18n.t('Name'), type: 'text', name: 'f-like-group_name' },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('Name'), column: 'group_name' },
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
                    <td>
                        <auto-button
                            small
                            :full="false"
                            @click="onFormOpen(record)"
                        >
                            <font-awesome-icon icon="fas fa-pencil" />
                        </auto-button>
                    </td>
                    <td>{{ record.group_name }}</td>
                    <td>{{ $helper.dateTimeFormat(record.updated_at) }}</td>
                    <td>{{ $helper.dateTimeFormat(record.created_at) }}</td>
                </tr>
            </template>
        </auto-datatable>
        <auto-modal
            v-if="formOpened"
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
import { ref } from "vue";
import AutoModal from "#/shared/components/auto-modal.vue";
import ModelForm from "./model-form.vue";

const $i18n = useI18n();

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
