import moment from "moment";
import axios from "axios";
import Cookies from "js-cookie";
import { computed, createApp } from "vue";
import {createPinia} from 'pinia';
import { useWindowState } from "~/shared/composables/use-window-state.js";
import NumericDirective from "#/shared/directives/numeric-directive.js";
import helper from '~/shared/utils/helper.js';

import VClickOutside from "click-outside-vue3";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { Money3Directive } from 'v-money3';
import 'vue3-carousel/carousel.css';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import 'sweetalert2/dist/sweetalert2.min.css';

import "./css/app.css";
import './font-awesome.js';
import AppVue from './app.vue';
import router from "./router.js";
import i18n from './i18n.js';

import useUserStore from "#/user/stores/user-store.js";
import useUserSettingStore from "#/user/stores/setting-store.js";
import useUserLocaleStore from "#/user/stores/locale-store.js";

const role = 'user';
const api_url = `${window.location.origin}/api/${role}`;

const app = createApp(AppVue);
const pinia = createPinia();
const windowState = useWindowState();

const helpers = {
    install(app) {
        app.helper = helper;
        app.provide('$helper', helper);
        app.config.globalProperties.$helper = helper;

        app.moment = moment;
        app.provide('$moment', moment);
        app.config.globalProperties.$moment = moment;

        app.windowState = computed(() => windowState);
        app.provide('$windowState', windowState);
        app.config.globalProperties.$windowState = computed(() => windowState);

        app.appName = import.meta.env.APP_NAME;
        app.provide('$appName', import.meta.env.APP_NAME);
        app.config.globalProperties.$appName = import.meta.env.APP_NAME;

        app.accountStore = useUserStore();
        app.provide('$accountStore', app.accountStore);
        app.config.globalProperties.$accountStore = app.accountStore;

        app.settingStore = useUserSettingStore();
        app.provide('$settingStore', app.settingStore);
        app.config.globalProperties.$settingStore = app.settingStore;

        app.localeStore = useUserLocaleStore();
        app.provide('$localeStore', app.localeStore);
        app.config.globalProperties.$localeStore = app.localeStore;

        app.role = role;
        app.provide('$role', role);
        app.config.globalProperties.$role = role;

        app.locale = app.localeStore.locale;
        app.provide('$locale', app.locale);
        app.config.globalProperties.$locale = app.locale;

        app.locales = Object.keys(helper.getLanguages());
        app.provide('$locales', app.locales);
        app.config.globalProperties.$locales = app.locales;
    }
};

/**
 * SETUP AXIOS -- START
 */
axios.defaults.withCredentials = true;
axios.defaults.baseURL = api_url;
axios.defaults.headers['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers['Cache-Control'] = 'no-cache';
axios.defaults.headers['Pragma'] = 'no-cache';
axios.defaults.headers['Expires'] = 0;
// axios.defaults.headers['Accept'] = 'application/json';
// axios.defaults.headers['Content-Type'] = 'application/json';
axios.defaults.headers['Accept-Language'] = Cookies.get(`${role}_locale`) || 'en';
axios.defaults.headers['TIMEZONE'] = Intl.DateTimeFormat().resolvedOptions().timeZone;
// axios.defaults.headers.post['Content-Type'] = 'multipart/form-data';

const rev = document.querySelector('meta[name="v-rev-v"]');
if (rev && rev.content && rev.content.length) {
    axios.defaults.headers['VREVV'] = rev.content;
}

axios.interceptors.request.use((request) => {
    if (request.data) {
        if (request.data instanceof FormData) {
            for (let [key, value] of request.data.entries()) {
                if (value === '' || value === 'null' || value === null) request.data.delete(key);
            }
        } else if (typeof request.data === 'object') {
            for (let key in Object.keys(request.data)) {
                if (request.data[key] === '' || request.data[key] === 'null' || request.data[key] === null) {
                    delete request.data[key];
                }
            }
        }
    }
    return request;
});
axios.interceptors.response.use(
    async response => {
        if (response.config && response.config.url === '/dt/') {
            const $accountStore = useUserStore();
            await $accountStore.fetchNotifications();
        }
        return response.data;
    },
    async error => {
        if (typeof error.response !== 'undefined') {
            const {status, data} = error.response;
            if (status === 401) {
                try {
                    const $accountStore = useUserStore();
                    await $accountStore.logoutAccount();
                } catch (e) {
                    console.log(e);
                } finally {
                    window.location.href = `${window.location.origin}/${role}`;
                }
            } else if (status === 403) {
                window.history.back();
            } else if (status === 422 && data.message) {
                await helper.alertError({message: data.message});
            } else if (status === 418) {
                await helper.alertWarning({
                    message: i18n.global.t('A new version is available, please refresh page'),
                    callback: () => {
                        location.reload();
                    }
                });
            } else if (status === 419) {
                await axios.get(`${window.location.origin}/sanctum/csrf-cookie`).then(async response => {
                    console.log(response);
                });
            }
        } else {
            console.log(error);
        }
        return Promise.reject(error);
    },
);
/**
 * SETUP AXIOS -- END
 */

app.use(pinia)
    .use(i18n)
    .use(helpers)
    .use(router)
    .use(VClickOutside)
    .use(NumericDirective)
    .component('VueDatePicker', VueDatePicker)
    .component('FontAwesomeIcon', FontAwesomeIcon)
    .directive('money3', Money3Directive)
    .mount('#app');


