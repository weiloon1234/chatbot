import { useStorageDefaultOptions } from '#/shared/composables/use-storage.js';
import { useStorage } from '@vueuse/core';
import axios from 'axios';

export function createSettingStore(role) {
    const settingKey = `${role}_setting`;
    const lastUpdateKey = `${role}_setting_last_update`;
    return {

        state: () => {
            return {
                settings: useStorage(settingKey, null, localStorage, useStorageDefaultOptions),
                lastUpdate: useStorage(lastUpdateKey, null, localStorage, useStorageDefaultOptions),
            };
        },
        getters: {
            settingKey: () => settingKey,
            lastUpdateKey: () => lastUpdateKey,
            countryForOptions: (state) => {
                if (!state.settings || !state.settings.country) return [];
                return (
                    Object.keys(state.settings.country).map((key) => {
                        return {
                            value: String(state.settings.country[key].id),
                            text: String(state.settings.country[key].name),
                        };
                    }) || []
                );
            },
            extForOptions: (state) => {
                if (!state.settings || !state.settings.country) return [];
                return (
                    Object.keys(state.settings.country).map((key) => {
                        return {
                            value: String(state.settings.country[key].id),
                            text: String(state.settings.country[key].ext),
                        };
                    }) || []
                );
            },
            defaultCountry: (state) => {
                if (!state.settings.country) return null;
                if (!state.settings.default_country_code) return null;
                return state.settings.country.find((row) => {
                    return String(row.iso2).toUpperCase() === String(state.settings.default_country_code).toUpperCase();
                });
            },
        },
        actions: {
            async checkIfShouldRefreshFromServer() {
                if (!this.lastUpdate) {
                    await this.fetchSettings();
                } else {
                    let seconds = (this.lastUpdate - Date.now()) / 1000;
                    if (seconds >= 600 || seconds <= -600) {
                        await this.fetchSettings();
                    }
                }
            },
            setSettings(_, settings) {
                this.settings = settings;
            },
            async fetchSettings() {
                await axios.post('/settings').then(async (data) => {
                    this.settings = data.settings;
                    this.lastUpdate = Date.now();
                });
            },
        },
    };
}
