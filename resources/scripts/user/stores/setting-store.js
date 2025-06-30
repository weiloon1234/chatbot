import {createSettingStore} from "#/shared/composables/pinia/create-setting-store.js";
import { defineStore } from "pinia";

const role = 'user';
const baseStore = createSettingStore(role);

export const useUserSettingStore = defineStore(`${role}_setting`, {
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

export default useUserSettingStore;
