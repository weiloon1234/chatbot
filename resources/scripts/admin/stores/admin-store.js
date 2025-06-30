import {createAccountStore} from "#/shared/composables/pinia/create-account-store.js";
import { defineStore } from "pinia";

const role = 'admin';
const baseStore = createAccountStore(role);

export const useAdminStore = defineStore(role, {
    state: () => ({
        ...baseStore.state(),
        extra: {}
    }),
    getters: {
        ...baseStore.getters,
    },
    actions: {
        ...baseStore.actions,
    }
});

export default useAdminStore;
