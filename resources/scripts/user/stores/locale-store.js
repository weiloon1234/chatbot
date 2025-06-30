import { createLocaleStore } from "#/shared/composables/pinia/create-locale-store.js";
import { defineStore } from "pinia";
import {changeLocale} from "#/user/i18n.js";
import i18n from "#/user/i18n.js";

const role = 'user';
const baseStore = createLocaleStore(role);

export const useUserLocaleStore = defineStore(`${role}_locale`, {
    state: () => ({
        ...baseStore.state(),
        extra: {}
    }),
    getters: {
        ...baseStore.getters,
    },
    actions: {
        ...baseStore.actions,
        async postChangeLocale(locale) {
            await changeLocale(i18n, locale);
        }
    }
});

export default useUserLocaleStore;
