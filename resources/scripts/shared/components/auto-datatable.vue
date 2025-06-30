<template>
    <div
        class="w-full"
    >
        <div
            v-if="tableReady"
            class="w-full overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]"
        >
            <div
                v-if="hasSearchBox"
                class="w-full p-2 border-b border-gray-200"
            >
                <div
                    v-if="searchCreatedAt || searchFilters.length > 0"
                    class="flex flex-col space-y-3 lg:grid lg:grid-cols-2 lg:gap-2 lg:space-y-0"
                >
                    <component
                        v-for="(f, index) in searchFilters"
                        :key="`search-filter-${index}`"
                        :is="formComponents[`form-${f.type}`]"
                        v-bind="f"
                        v-model="searchData[f.name]"
                        :class="{
                            'col-span-2': searchFilters.length % 2 !== 0 && index === searchFilters.length - 1
                        }"
                        :disabled="busy"
                        :readonly="busy"
                    />
                    <component
                        v-for="(f, key) in createdAtSearchFields"
                        :key="`search-created-at-${key}`"
                        :is="formComponents[`form-${f.type}`]"
                        v-bind="f"
                        v-model="searchData[f.name]"
                        :disabled="busy"
                        :readonly="busy"
                    />
                    <auto-button
                        :busy="busy"
                        class="flex justify-center items-center space-x-4"
                        @click="onFilterSubmit"
                    >
                        <div>
                            <svg fill="#ffffff" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 viewBox="0 0 49.999 49.999"
                                 xml:space="preserve"
                                 class="h-4 aspect-square"
                            >
                                <g>
                                    <g>
                                        <path d="M48.681,42.295l-8.925-8.904c-0.045-0.045-0.098-0.078-0.145-0.11c-0.802,1.233-1.761,2.405-2.843,3.487
                                            c-1.081,1.082-2.255,2.041-3.501,2.845c0.044,0.046,0.077,0.1,0.122,0.144l8.907,8.924c1.763,1.76,4.626,1.758,6.383,0
                                            C50.438,46.921,50.439,44.057,48.681,42.295z"/>
                                        <path d="M35.496,6.079C27.388-2.027,14.198-2.027,6.089,6.081c-8.117,8.106-8.118,21.306-0.006,29.415
                                            c8.112,8.105,21.305,8.105,29.413-0.001C43.604,27.387,43.603,14.186,35.496,6.079z M9.905,31.678
                                            C3.902,25.675,3.904,15.902,9.907,9.905c6.003-6.002,15.77-6.002,21.771-0.003c5.999,6,5.997,15.762,0,21.774
                                            C25.676,37.66,15.91,37.682,9.905,31.678z"/>
                                        <path d="M14.18,22.464c-0.441-1.812-2.257-4.326-3.785-3.525c-1.211,0.618-0.87,3.452-0.299,5.128
                                            c2.552,7.621,11.833,9.232,12.798,8.268C23.843,31.387,15.928,29.635,14.18,22.464z"/>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div>{{ $t('Search') }}</div>
                    </auto-button>
                    <auto-button
                        :type="filtered ? 'danger' : 'disabled'"
                        :busy="busy"
                        class="flex justify-center items-center space-x-4"
                        @click="onFilterReset"
                    >
                        <div>
                            <svg class="h-4 aspect-square text-white" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.56189 13.5L4.14285 13.9294L4.5724 14.3486L4.99144 13.9189L4.56189 13.5ZM9.92427 15.9243L15.9243 9.92427L15.0757 9.07574L9.07574 15.0757L9.92427 15.9243ZM9.07574 9.92426L15.0757 15.9243L15.9243 15.0757L9.92426 9.07574L9.07574 9.92426ZM19.9 12.5C19.9 16.5869 16.5869 19.9 12.5 19.9V21.1C17.2496 21.1 21.1 17.2496 21.1 12.5H19.9ZM5.1 12.5C5.1 8.41309 8.41309 5.1 12.5 5.1V3.9C7.75035 3.9 3.9 7.75035 3.9 12.5H5.1ZM12.5 5.1C16.5869 5.1 19.9 8.41309 19.9 12.5H21.1C21.1 7.75035 17.2496 3.9 12.5 3.9V5.1ZM5.15728 13.4258C5.1195 13.1227 5.1 12.8138 5.1 12.5H3.9C3.9 12.8635 3.92259 13.2221 3.9665 13.5742L5.15728 13.4258ZM12.5 19.9C9.9571 19.9 7.71347 18.6179 6.38048 16.6621L5.38888 17.3379C6.93584 19.6076 9.54355 21.1 12.5 21.1V19.9ZM4.99144 13.9189L7.42955 11.4189L6.57045 10.5811L4.13235 13.0811L4.99144 13.9189ZM4.98094 13.0706L2.41905 10.5706L1.58095 11.4294L4.14285 13.9294L4.98094 13.0706Z" fill="#ffffff"/>
                            </svg>
                        </div>
                        <div>{{ $t('Reset') }}</div>
                    </auto-button>
                </div>
            </div>
            <div
                class="w-full flex flex-col space-y-3 p-2"
            >
                <div
                    v-if="hasTools"
                    class="flex justify-start space-x-4 items-center"
                >
                    <slot name="tools" :busy="busy" />
                </div>
                <div class="flex justify-end space-x-2">
                    <auto-button
                        v-if="hasExport"
                        type="plain"
                        small
                        class="flex justify-center space-x-2 items-center"
                        :full="false"
                        @click="onExport"
                    >
                        <font-awesome-icon
                            icon="fas fa-download"
                        />
                        <span>{{ $t('Export') }}</span>
                    </auto-button>
                    <auto-button
                        type="plain"
                        small
                        class="flex justify-center space-x-2 items-center"
                        :full="false"
                        @click="autoRefresh = !autoRefresh"
                    >
                        <span>{{ autoRefreshMessage }}</span>
                        <span
                            class="flex h-4 w-4 cursor-pointer flex-col items-center justify-center rounded-md border"
                            :class="{
                                'border-gray-300 bg-transparent': !autoRefresh,
                                'bg-brand-500 border-brand-500': autoRefresh,
                            }"
                        >
                            <svg
                                v-if="autoRefresh"
                                width="12"
                                height="12"
                                viewBox="0 0 14 14"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </auto-button>
                </div>
            </div>
            <div
                class="custom-table-container"
                :style="{
                    'max-height': `${tableMaxHeight}px`,
                }"
            >
                <table
                    class="custom-table"
                >
                    <thead>
                        <slot name="header-prepend" />
                        <tr>
                            <th
                                v-for="(header, k) in headers"
                                :key="`header-${k}`"
                                :class="{
                                    'sortable': header.sortable
                                }"
                                @click="onSorting(header)"
                            >
                                <div
                                    class="w-full flex justify-between space-x-3 items-center"
                                >
                                    <span>
                                        {{ header.label }}
                                    </span>
                                    <font-awesome-icon
                                        v-if="sorting === header.column"
                                        :icon="['fas', `fa-caret-${String(order).toLowerCase() === 'asc' ? 'up' : 'down'}`]"
                                    />
                                </div>
                            </th>
                        </tr>
                        <slot name="header-append" />
                    </thead>
                    <tbody>
                        <slot name="body-prepend" :records="records" />
                        <template
                            v-if="!records?.length"
                        >
                            <tr>
                                <td :colspan="headers.length" class="text-center">
                                    <div
                                        class="w-full flex justify-center items-center space-x-2"
                                    >
                                        <font-awesome-icon icon="fas fa-close" />
                                        <span>
                                            {{ $t('No data available') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <slot :records="records" />
                        <slot name="body-append" :records="records" />
                    </tbody>
                    <tfoot>
                        <slot name="footer-prepend" :records="records" />
                        <template
                            v-if="hasSums"
                        >
                            <tr>
                                <th
                                    v-for="(header, key) in headers"
                                    :key="`tfoot-header-${key}`"
                                >
                                    {{ header.sum ? $helper.fundFormat(footerSums[header.column], header.decimal ?? 2) : '' }}
                                </th>
                            </tr>
                        </template>
                        <slot name="footer-appends" :records="records" />
                    </tfoot>
                </table>
            </div>
            <div class="w-full flex flex-col space-y-3 p-2">
                <div class="w-full flex justify-between space-x-2">
                    <div class="text-sm text-gray-600">
                        <div class="flex items-center space-x-1">
                            <div class="relative flex justify-center items-center">
                                <select
                                    v-model="itemPerPage"
                                    :disabled="busy"
                                    class="text-black appearance-none leading-tight w-full px-6 py-1 border border-gray-300 rounded-md"
                                    :class="{
                                        'bg-white': !busy,
                                        'bg-gray-200 cursor-not-allowed': busy,
                                    }"
                                >
                                    <option
                                        v-for="i in ippLists"
                                        :key="`ipp-${i}`"
                                        :selected="parseInt(itemPerPage) === i"
                                    >
                                        {{ i }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg
                                        class="fill-current h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                    ><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                                </div>
                            </div>
                            <span>{{ $i18n.t('Records per page') }}</span>
                        </div>
                    </div>
                    <div>
                        <auto-button
                            type="plain"
                            small
                            class="flex justify-center items-center space-x-4"
                            :disabled="busy"
                            @click="fetchData"
                        >
                            <div>
                                <svg
                                    class="h-3 aspect-square" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    :class="{
                                        'animate-spin': busy,
                                    }"
                                >
                                    <path d="M5.39092 5.89092L8.5 9H2.5V3L5.39092 5.89092ZM5.39092 5.89092C7.03504 4.1131 9.38753 3 12 3C16.6326 3 20.4476 6.50005 20.9451 11M18.6091 18.1091L21.5 21V15H15.5L18.6091 18.1091ZM18.6091 18.1091C16.965 19.8869 14.6125 21 12 21C7.36745 21 3.55237 17.5 3.05493 13" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>{{ $t('Refresh') }}</div>
                        </auto-button>
                    </div>
                </div>
                <div class="w-full flex justify-center space-x-2">
                    <div>
                        <auto-button
                            type="primary"
                            :disabled="busy || !canGoBack || currentPage === 1"
                            small
                            @click="goFirstPage"
                        >
                            <font-awesome-icon
                                icon="fas fa-angles-left"
                            />
                        </auto-button>
                    </div>
                    <div>
                        <auto-button
                            type="plain"
                            :disabled="busy || !canGoBack"
                            small
                            @click="goBackPage"
                        >
                            <font-awesome-icon
                                icon="fas fa-angle-left"
                            />
                        </auto-button>
                    </div>
                    <div class="flex justify-start items-center space-x-0.5 text-sm text-gray-600">
                        <input
                            type="number"
                            v-model="currentPage"
                            :disabled="busy"
                            v-integer
                            class="border border-gray-300 rounded-md focus:outline-0 outline-0 px-2 py-0 w-10 text-center"
                            :class="{
                                'bg-gray-200': busy,
                            }"
                        />
                        <div class="flex-1 lowercase">
                            / {{ totalPages }} | {{ fromAndToRecords.from }}-{{ fromAndToRecords.to }} {{ $i18n.t('Of') }} {{ totalRecords }}
                        </div>
                    </div>
                    <div>
                        <auto-button
                            type="plain"
                            :disabled="busy || !canGoNext"
                            small
                            @click="goNextPage"
                        >
                            <font-awesome-icon
                                icon="fas fa-angle-right"
                            />
                        </auto-button>
                    </div>
                    <div>
                        <auto-button
                            type="primary"
                            :disabled="busy || !canGoNext || currentPage === totalPages"
                            small
                            @click="goLastPage"
                        >
                            <font-awesome-icon
                                icon="fas fa-angles-right"
                            />
                        </auto-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    inject,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    useSlots,
    watch
} from "vue";
import { useI18n } from "vue-i18n";
import axios from "axios";
import Cookies from "js-cookie";
import AutoButton from "#/shared/components/button/auto-button.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { useFormComponents } from "#/shared/composables/use-form-component.js";

const props = defineProps({
    tableMaxHeight: {
        required: false,
        type: Number,
        default: 420,
    },
    headers: {
        required: false,
        type: Array,
        default: () => [],
    },
    model: {
        required: true,
        type: String,
    },
    searchCreatedAt: {
        required: false,
        type: Boolean,
        default: true,
    },
    searchFilters: {
        required: false,
        type: Array,
        default: () => [],
    },
    hiddenData: {
        required: false,
        type: Array,
        default: () => [],
    },
    url: {
        required: false,
        type: [String, Object],
        default: () => {
            return '/dt/';
        },
    },
    noExport: {
        required: false,
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['loaded']);

const $slots = useSlots();
const $i18n = useI18n();
const $role = inject('$role');
const $helper = inject('$helper');
const $accountStore = inject('$accountStore');

const {formComponents} = useFormComponents();

const tableReady = ref(false);
const busy = ref(false);
const currentPage = ref(1);
const itemPerPage = ref(null);
const totalRecords = ref(0);
const totalPages = ref(0);
const records = ref([]);
const sorting = ref(null);
const order = ref('desc');
const rememberTableKey = `${$role}_ipp`;
const rememberTableAutoRefresh = `${$role}_auto_refresh`;
const ippLists = ref([100, 250, 500, 1000, 3000, 5000]);
const autoRefresh = ref(false);
const autoRefreshSetup = ref(false);
const autoRefreshSeconds = 60;
const autoRefreshCountdown = ref(autoRefreshSeconds);
const autoRefreshInterval = ref(null);
const filtered = ref(false);

const searchData = ref({
    "f-date-from_created_at": '',
    "f-date-to_created_at": '',
});
const createdAtSearchFields = computed(() => {
    if (!props.searchCreatedAt) return null;
    return [
        { type: 'date-picker', name: 'f-date-from-created_at', label: $i18n.t('From date') },
        { type: 'date-picker', name: 'f-date-to-created_at', label: $i18n.t('To date') },
    ];
});

const hasSearchBox = computed(() => {
    return props.searchCreatedAt || props.searchFilters.length > 0 || !!$slots.filters;
});

const hasTools = computed(() => {
    return !!$slots.tools;
});

const getFormData = () => {
    const fd = new FormData();

    fd.set('model', props.model);
    fd.set('p', currentPage.value);
    fd.set('ipp', itemPerPage.value);
    fd.set('sorting_column', sorting.value);
    fd.set('sorting', order.value);

    props.hiddenData.forEach((field) => {
        fd.append(field.name, field.value);
    });

    for (const key in searchData.value) {
        if (searchData.value[key] && String(searchData.value[key]).length > 0) {
            fd.append(key, searchData.value[key]);
        }
    }

    return fd;
};

const autoRefreshMessage = computed(() => {
    if (!autoRefreshSetup.value || !autoRefresh.value) {
        return $i18n.t('Auto refresh');
    } else {
        if (!autoRefresh.value) {
            return $i18n.t('Auto refresh');
        } else {
            return $i18n.t('Auto refresh') + ' (' + autoRefreshCountdown.value + 's)';
        }
    }
});
const canGoBack = computed(() => {
    return parseInt(currentPage.value) > 1;
});
const canGoNext = computed(() => {
    return parseInt(currentPage.value) < parseInt(totalPages.value);
});
const fromAndToRecords = computed(() => {
    let from = 1;
    if (currentPage.value > 1) {
        from = (currentPage.value - 1) * itemPerPage.value + 1;
    }

    let to = currentPage.value * itemPerPage.value;

    if (to >= totalRecords.value) {
        to = totalRecords.value;
    }

    return {
        from,
        to,
    };
});
const hasSums = computed(() => {
    if (!records.value || !records.value.length) return false;
    if (!props.headers || !props.headers.length) return false;
    const first = props.headers.find((header) => {
        return header.sum === true;
    });

    return first ?? false;
});
const footerSums = computed(() => {
    const sums = {};
    props.headers.forEach((header) => {
        if (header.sum) {
            let s = parseFloat(0);
            records.value.forEach((record) => {
                s += parseFloat(record[header.column]);
            });
            sums[header.column] = s;
        }
    });

    return sums;
});
const goBackPage = async () => {
    if (canGoBack.value) {
        currentPage.value--;
    }
};
const goNextPage = async () => {
    if (canGoNext.value) {
        currentPage.value++;
    }
};
const goFirstPage = async () => {
    if (canGoBack.value) {
        currentPage.value = 1;
    }
};
const goLastPage = async () => {
    if (canGoNext.value) {
        currentPage.value = parseInt(totalPages.value);
    }
};

const onSorting = async header => {
    if (typeof header.sortable === 'undefined' || header.sortable !== false) {
        if (header.column === sorting.value) {
            if (order.value === 'desc') order.value = 'asc';
            else order.value = 'desc';
        } else {
            order.value = 'desc';
        }

        sorting.value = header.column;
        currentPage.value = 1; //Back to page 1

        await fetchData();
    }
};

const hasExport = computed(() => {
    if (props.noExport) return false;
    if ($role === 'admin') {
        const $admin = $accountStore.account;
        return $helper.hasPermission($admin, 'Export');
    }
    return !props.noExport;
});

const onExport = async () => {
    if (!busy.value) {
        try {
            busy.value = true;

            const fd = getFormData();
            const fileName = Math.floor(Date.now() / 1000) + '.csv';

            fd.set('headers', JSON.stringify(props.headers));
            fd.set('export', 1);
            fd.set('export_file_name', fileName);

            const data = await axios.post(props.url, fd);

            const temp = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement('a');
            link.href = temp;
            link.setAttribute('download', fileName);
            document.body.appendChild(link);
            link.click();
            busy.value = false;
        } catch (e) {
            console.error(e);
            busy.value = false;
        }
    }
};

const triggerAutoRefreshCheck = async () => {
    if (autoRefresh.value) {
        autoRefreshInterval.value = setTimeout(autoRefreshCheck, 1000);
    } else {
        autoRefreshCountdown.value = autoRefreshSeconds;
    }
};

const autoRefreshCheck = async () => {
    if (autoRefreshCountdown.value <= 0) {
        await fetchData();
    } else {
        autoRefreshCountdown.value--;
    }

    autoRefreshInterval.value = setTimeout(autoRefreshCheck, 1000);
};

watch(() => currentPage.value, async () => {
    await fetchData();
});
watch(() => itemPerPage.value, async () => {
    currentPage.value = 1;

    Cookies.set(rememberTableKey, itemPerPage.value);

    await fetchData();
});
watch(() => autoRefresh.value, async (newValue, oldValue) => {
    Cookies.set(rememberTableAutoRefresh, newValue);

    //DISABLED TURN TO ENABLED
    if (!oldValue && newValue) {
        autoRefreshCountdown.value = autoRefreshSeconds;

        await triggerAutoRefreshCheck();
    } else if (!newValue && oldValue) {
        //ENABLED TURN TO DISABLE
        autoRefreshCountdown.value = autoRefreshSeconds;

        if (autoRefreshInterval.value) {
            clearInterval(autoRefreshInterval.value);
        }
    }
});

const onFilterSubmit = async () => {
    if (!busy.value) {
        currentPage.value = 1;

        filtered.value = true;

        await fetchData();
    }
};

const onFilterReset = async () => {
    if (filtered.value) {
        Object.keys(searchData.value).forEach(key => {
            if (searchData.value[key] && searchData.value[key] !== '') {
                searchData.value[key] = '';
            }
        });

        currentPage.value = 1;
        filtered.value = false;

        await fetchData();
    }
};

const fetchData = async () => {
    if (!busy.value) {
        try {
            busy.value = true;
            const fd = getFormData();
            const data = await axios.post(props.url, fd);

            totalRecords.value = data.total_records;
            totalPages.value = data.total_pages;
            records.value = data.records;

            busy.value = false;
            autoRefreshCountdown.value = autoRefreshSeconds;
            emit('loaded', fd);
        } catch (e) {
            console.error(e);
            busy.value = false;
        }
    }
};

onMounted(async () => {
    //REMEMBER IPP
    if (Cookies.get(rememberTableKey)) {
        itemPerPage.value = Cookies.get(rememberTableKey);
    } else {
        itemPerPage.value = ippLists.value[0];
    }

    //REMEMBER AUTO REFRESH
    if (Cookies.get(rememberTableAutoRefresh)) {
        autoRefresh.value = Cookies.get(rememberTableAutoRefresh);
    }

    //FIND FIRST SORTABLE
    sorting.value =
        props.headers.find(r => {
            return typeof r.sortable === 'undefined' || r.sortable !== false;
        }).column || null;

    await nextTick(async() => {
        tableReady.value = true;
        await nextTick(() => {
            autoRefreshSetup.value = true;
        });
    });
});

onBeforeUnmount(() => {
    if (autoRefreshInterval.value) {
        clearInterval(autoRefreshInterval.value);
    }
});

defineExpose({
    records,
    fetchData,
});
</script>
