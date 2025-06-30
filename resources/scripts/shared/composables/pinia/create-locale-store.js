import axios from "axios";
import { useStorage } from "@vueuse/core";
import { useStorageDefaultOptions } from '#/shared/composables/use-storage.js';
import Cookies from "js-cookie";

export function createLocaleStore(role) {
    const localeKey = `${role}_locale`;
    const defaultLocale = window.config.locales[0] ?? 'en';

    return {
        state: () => {
            return {
                localeKey: localeKey,
                locale: useStorage(localeKey, defaultLocale, localStorage, useStorageDefaultOptions),
                locales: window.config.locales,
            };
        },
        actions: {
            updateAxios() {
                axios.defaults.headers['Accept-Language'] = this.locale || defaultLocale;
            },
            async updateLocale(l) {
                this.locale = l;
                Cookies.set(localeKey, l, { expires: 365 });

                // Update Axios headers
                this.updateAxios();


                // Call backend API to persist language preference
                // try {
                //     await axios.post('/change_language', {
                //         language: l,
                //     });
                // } catch (error) {
                //     console.error('Failed to update language on the server:', error);
                // }

                // Dynamically load and set the new locale without reloading the page

                await this.postChangeLocale(l);
            },
            async postChangeLocale() {
                console.warn("postChangeLocale() not implemented in child store.");
            }
        }
    }
}
