<template>
    <div class="w-full">
        <div>THIS IS HOME / Window Width: {{ $windowState.windowWidth }}</div>
        <div>
            <auto-button
                type="danger"
                @click="onLogout"
            >
                LOGOUT
            </auto-button>
        </div>
    </div>
</template>
<script setup>
import AutoButton from "#/shared/components/button/auto-button.vue";
import { inject } from "vue";
import { useRouter } from "vue-router";

const $router = useRouter();
const $accountStore = inject("$accountStore");
const $windowState = inject("$windowState");

const onLogout = async () => {
    await $accountStore.logoutAccount();
    await $router.replace({name: `${$accountStore.role}.auth.login`});
};
</script>
