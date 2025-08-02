<template>
    <router-view />
    <!-- Modal Portal using auto-modal -->
    <teleport to="body">
        <auto-modal v-if="$modalStore.isOpen" />
    </teleport>
</template>
<script setup>
import { inject } from 'vue';
import AutoModal from "#/shared/components/auto-modal.vue";

const $modalStore = inject('$modalStore');
</script>
