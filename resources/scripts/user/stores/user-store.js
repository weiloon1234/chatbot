import {createAccountStore} from "#/shared/composables/pinia/create-account-store.js";
import { defineStore } from "pinia";

const role = 'user';
const baseStore = createAccountStore(role);

export const useUserStore = defineStore(role, {
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

export default useUserStore;
