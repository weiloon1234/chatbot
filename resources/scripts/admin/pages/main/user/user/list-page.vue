<template>
    <div class="w-full class flex flex-col space-y-4">
        <div class="w-full flex flex-col space-y-4 lg:grid lg:grid-cols-3 lg:space-y-0 lg:gap-4">
            <admin-content-card>
                <p class="text-theme-sm text-gray-700 capitalize">
                    {{ $t('Total user') }}
                </p>
                <div class="w-full flex justify-end">
                    <span class="text-4xl text-gray-700 capitalize font-black">
                        {{ $helper.fundFormat(statistics.total_user, 0) }}
                    </span>
                </div>
            </admin-content-card>
            <admin-content-card
                @click="openTransactions(null, 1, $i18n.t('Credit 1'))"
            >
                <p class="text-theme-sm text-gray-700 capitalize">
                    {{ $t('Total credit 1') }}
                </p>
                <div class="w-full flex justify-end">
                    <span class="text-4xl text-gray-700 capitalize font-black">
                        {{ $helper.fundFormat(statistics.total_credit_1) }}
                    </span>
                </div>
            </admin-content-card>
            <admin-content-card
                @click="openTransactions(null, 2, $i18n.t('Credit 2'))"
            >
                <p class="text-theme-sm text-gray-700 capitalize">
                    {{ $t('Total credit 2') }}
                </p>
                <div class="w-full flex justify-end">
                    <span class="text-4xl text-gray-700 capitalize font-black">
                        {{ $helper.fundFormat(statistics.total_credit_2) }}
                    </span>
                </div>
            </admin-content-card>
        </div>
        <auto-datatable
            ref="dt"
            model="User"
            :search-filters="[
                { label: $i18n.t('Introducer'), type: 'text', name: 'f-has-like-any-introducer-username|email|full_contact_number|name' },
                { label: $i18n.t('User'), type: 'text', name: 'f-like-any-username|email|full_contact_number|name' },
                { label: $i18n.t('Country'), type: 'text', name: 'f-has-like-any-country-name|iso2|iso3|phone_code|currency_code' },
            ]"
            :headers="[
                { label: '#', column: 'id' },
                { label: $i18n.t('Actions'), column: 'actions', sortable: false },
                { label: $i18n.t('Introducer'), column: 'introducer_user_id', export_column: 'introducer_identity' },
                { label: $i18n.t('Username'), column: 'username' },
                { label: $i18n.t('Email'), column: 'email' },
                { label: $i18n.t('Name'), column: 'name' },
                { label: $i18n.t('Country'), column: 'country_id', export_column: 'country_name' },
                { label: $i18n.t('Contact number'), column: 'full_contact_number' },
                { label: $i18n.t('Credit 1'), column: 'credit_1', sum: true, decimal: 2 },
                { label: $i18n.t('Credit 2'), column: 'credit_2', sum: true, decimal: 2 },
                { label: $i18n.t('Created at'), column: 'created_at' },
            ]"
            @loaded="onTableLoaded"
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
                            type="plain"
                            small
                            :full="false"
                            @click="onLoginUser(record)"
                        >
                            <font-awesome-icon icon="fas fa-key" />
                        </auto-button>
                        <auto-button
                            :type="parseInt(record.ban) === 1 ? 'danger' : 'success'"
                            small
                            :full="false"
                            @click="toggleBanStatus(record)"
                        >
                            {{ parseInt(record.ban) === 1 ? $t('Unban') : $t('Ban') }}
                        </auto-button>
                    </td>
                    <td>{{ record.introducer_identity }}</td>
                    <td>{{ record.username }}</td>
                    <td>{{ record.email }}</td>
                    <td>{{ record.name }}</td>
                    <td>{{ record.country_name }}</td>
                    <td>{{ record.full_contact_number }}</td>
                    <td>
                        <a
                            href="javascript:"
                            class="text-blue-400 font-semibold"
                            @click="openTransactions(record.id, 1, `${record.identity} (${$i18n.t('Credit 1')})`)"
                        >
                            {{ $helper.fundFormat(record.credit_1) }}
                        </a>
                    </td>
                    <td>
                        <a
                            href="javascript:"
                            class="text-blue-400 font-semibold"
                            @click="openTransactions(record.id, 2, `${record.identity} (${$i18n.t('Credit 2')})`)"
                        >
                            {{ $helper.fundFormat(record.credit_2) }}
                        </a>
                    </td>
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
import ModelForm from "./model-form.vue";
import axios from "axios";
import AdminContentCard from "#/admin/components/admin-content-card.vue";
import useUserStore from "#/user/stores/user-store.js";
import TransactionTable from "./transaction-table.vue";

const $i18n = useI18n();
const $helper = inject('$helper');
const $userStore = useUserStore();
const $modalStore = inject('$modalStore');

const statistics = ref({});
const dt = ref();

const openTransactions = (userId = null, cid = null, identity = null) => {
    $modalStore.open(
        TransactionTable,
        { userId, cid },
        { title: identity || 'Transaction History' }
    );
};

const onFormOpen = (m = null) => {
    $modalStore.open(
        ModelForm,
        { model: m },
        { title: m ? 'Edit User' : 'Create User' },
        () => dt.value.fetchData() // Close callback
    );
};

const onTableLoaded = async () => {
    try {
        const data = await axios.post('/user/user/load_statistics');
        statistics.value = data.statistics;
    } catch (e) {
        console.error(e);
    }
};

const toggleBanStatus = async (user) => {
    let message = parseInt(user.ban) === 1 ? $i18n.t('Confirm unban this user x?') : $i18n.t('Confirm ban this user x?');
    message = String(message).replace(/:x/i, user.identity);

    await $helper.alertConfirm({
        message: message,
        callback: async (result) => {
            if (result.isConfirmed) {
                try {
                    await axios.post('/user/user/toggle_ban_status', { id: user.id });
                    await dt.value.fetchData();
                } catch (e) {
                    console.error(e);
                }
            }
        }
    });
};

const onLoginUser = async (user) => {
    const newWindow = window.open('', '_blank');

    await $helper.alertConfirm({
        message: $i18n.t('Confirm login as this user x?').toString().replace(/:x/i, user.identity),
        callback: async (result) => {
            if (result.isConfirmed) {
                try {
                    const data = await axios.post('/user/user/login_account', { id: user.id });
                    $userStore.setToken(data.token);

                    // Update the already opened window
                    newWindow.location.href = $helper.getCurrentDomain() + '/';
                } catch (e) {
                    newWindow.close();
                    console.error(e);
                }
            } else {
                newWindow.close();
            }
        }
    });
};
</script>
