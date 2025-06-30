<template>
    <div class="w-full">
        <auto-datatable
            ref="dt"
            model="Page"
            :search-created-at="false"
            :search-filters="[
                { label: $i18n.t('Title'), type: 'text', name: 'f-like-title' },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('Title'), column: 'title' },
                { label: $i18n.t('Updated at'), column: 'updated_at' },
            ]"
        >
            <template #tools="{busy}">
                <auto-button
                    :busy="busy"
                    :full="false"
                    class="flex justify-center items-center space-x-2"
                    @click="$router.push({name: 'admin.other.page.form'})"
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
                            @click="$router.push({name: 'admin.other.page.form', params: { id: record.id }})"
                        >
                            <font-awesome-icon icon="fas fa-pencil" />
                        </auto-button>
                        <auto-button
                            v-if="parseInt(record.is_system) === 0"
                            type="danger"
                            small
                            :full="false"
                            @click="onDelete(record)"
                        >
                            <font-awesome-icon icon="fas fa-trash" />
                        </auto-button>
                    </td>
                    <td>{{ record.title }}</td>
                    <td>{{ $helper.dateTimeFormat(record.updated_at) }}</td>
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
import axios from "axios";

const $i18n = useI18n();
const $helper = inject('$helper');

const dt = ref();
const onDelete = async (record) => {
    await $helper.alertConfirm({
        callback: async (result) => {
            if (result.isConfirmed) {
                try {
                    const data = await axios.post('/other/page/delete', { id: record.id });
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
