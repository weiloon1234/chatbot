<template>
    <div
        v-if="pageReady"
    >
        <admin-content-card>
            <textarea
                v-model="logs"
                class="w-full h-1/2 overflow-y-scroll border border-gray-400 special-textarea mb-4"
                rows="24"
            />
            <auto-button
                @click="onCleanUp"
            >
                Clean up
            </auto-button>
        </admin-content-card>
    </div>
</template>
<script setup>
import { inject, onMounted, ref } from "vue";
import axios from 'axios';
import AdminContentCard from "#/admin/components/admin-content-card.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";

const $helper = inject('$helper');
const pageReady = ref(false);
const logs = ref('');

const loadLog = async () => {
    const data = await axios.post('/etc/load_log');
    logs.value = data.log ?? '';
    if (!pageReady.value) pageReady.value = true;
}
onMounted(async () => {
    await loadLog();
});

const onCleanUp = async () => {
    pageReady.value = false;
    const data = await axios.post('/etc/clear_log');
    await $helper.alertSuccess({
        message: data.message,
        callback: async () => {
            await loadLog();
        }
    });
};
</script>

<style scoped>
.special-textarea {
    background-color: #fefefe;
}
</style>
