<template>
    <div class="w-full">
        <slot :items="items" />
        <vue-eternal-loading :load="loadData">
            <template #loading>
                <div class="mx-auto justify-center text-center">
                    <img :src="'/img/loading.gif'" class="mx-auto h-12 w-12" />
                </div>
            </template>
            <template #no-more>
                <div class="my-no-more mx-auto mt-2 justify-center text-center">
                    {{ $t('No more data') }}
                </div>
            </template>
            <template #no-results>
                <div class="my-no-results mt-2">
                    {{ $t('No more data') }}
                </div>
            </template>
            <template #error>
                <div class="my-error mt-2">
                    {{ $t('Something went wrong please try again later') }}
                </div>
            </template>
        </vue-eternal-loading>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { VueEternalLoading } from '@ts-pro/vue-eternal-loading';

const props = defineProps({
    url: {
        required: true,
        type: String,
    },
    postParams: {
        required: false,
        type: Object,
        default: () => {},
    },
});

const emit = defineEmits(['items-loaded']);

const page = ref(1);
const items = ref([]);

const loadData = async ({ loaded, noMore, noResults }) => {
    try {
        const j = props.url.includes('?') ? '&' : '?';
        const data = await axios.post(`${props.url}${j}page=${page.value}`, props.postParams);

        // items.value = [...items.value, ...data.data];

        setTimeout(() => {
            if (items.value && items.value.length && data && data.data && data.data.length) {
                items.value = [...items.value, ...data.data];
                emit('items-loaded', items.value);
            } else {
                items.value = data.data || [];
                emit('items-loaded', items.value);
            }

            if (parseInt(data.last_page) <= parseInt(page.value)) {
                noMore();
            } else {
                page.value++;
                loaded();
            }
        }, 500);
    } catch (e) {
        console.log(e);
        noResults();
    }
};
</script>
