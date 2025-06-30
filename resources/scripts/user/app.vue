<template>
    <router-view />
</template>

<script setup>
import EventBus,{EVENT} from '#/event-bus.js';
import { inject, onBeforeUnmount, onMounted } from "vue";

const $localeStore = inject('$localeStore');

const onModalToggle = () => {
    const b = document.body;
    b.classList.toggle('modal-open');
};

const onLoadingToggle = () => {
    const b = document.body;
    b.classList.toggle('is-system-loading');
};

const onLocaleChanged = async (newLocale) => {
    if (newLocale !== $localeStore.locale) {
        await $localeStore.updateLocale(newLocale);
    }
};

onMounted(() => {
    EventBus.on(EVENT.MODAL_OPEN, onModalToggle);
    EventBus.on(EVENT.START_LOADING, onLoadingToggle);
    EventBus.on(EVENT.MODAL_CLOSE, onModalToggle);
    EventBus.on(EVENT.STOP_LOADING, onLoadingToggle);
    EventBus.on(EVENT.LOCALE_CHANGED, onLocaleChanged);
});

onBeforeUnmount(() => {
    EventBus.off(EVENT.MODAL_OPEN, onModalToggle);
    EventBus.off(EVENT.START_LOADING, onLoadingToggle);
    EventBus.off(EVENT.MODAL_CLOSE, onModalToggle);
    EventBus.off(EVENT.STOP_LOADING, onLoadingToggle);
    EventBus.off(EVENT.LOCALE_CHANGED, onLocaleChanged);
})
</script>
