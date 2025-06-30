import { createLocaleStore } from "#/shared/composables/pinia/create-locale-store.js";
import { defineStore } from "pinia";
import {changeLocale} from "#/admin/i18n.js";
import i18n from "#/admin/i18n.js";

const role = 'admin';
const baseStore = createLocaleStore(role);

export const useAdminLocaleStore = defineStore(`${role}_locale`, {
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

export default useAdminLocaleStore;
