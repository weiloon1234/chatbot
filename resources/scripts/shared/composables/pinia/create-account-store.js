import axios from 'axios';
import Cookies from 'js-cookie';
import EventBus, { EVENT } from "#/event-bus.js";
import CookieOption from "#/shared/utils/cookie-option.js";

export function createAccountStore(role, options) {
    const refreshSeconds  = options?.refreshSeconds ?? 30;

    const tokenKey = `${role}_token`;

    return {
        state: () => {
            return {
                account: null,
                token: Cookies.get(tokenKey) ?? null,
                lastUpdate: null,
                notifications: {},
                role: role,
            };
        },
        getters: {
            tokenKey() {
                return tokenKey;
            },
            permissions(state) {
                return state.account?.group?.permissions?.map((r) => r.permission_tag) ?? [];
            },
        },
        actions: {
            getCookieOptions() {
                return CookieOption[role];
            },
            updateAxios() {
                axios.defaults.headers['Authorization'] = 'Bearer ' + this.token;
            },
            async fetchAccount() {
                const data = await axios.post('/me');
                this.updateAccount(data);
            },
            async fetchNotifications() {
                const data = await axios.post('/notifications');
                this.updateNotifications(data.notifications);
            },
            updateNotifications(notifications) {
                this.notifications = notifications;
            },
            updateAccount(acc) {
                let checkLocale = false;
                if (this.account && acc && this.account.lang !== acc.lang) {
                    checkLocale = true;
                }

                this.account = acc;
                this.lastUpdate = Date.now();

                if (checkLocale) {
                    EventBus.emit(EVENT.LOCALE_CHANGED, acc.lang);
                }
            },
            updateToken(t) {
                this.token = t;
                this.updateAxios();

                Cookies.set(tokenKey, t, this.getCookieOptions());
                // Cookies.set(tokenKey, t);
            },
            setToken(t) {//this won't update axios headers
                this.token = t;
                Cookies.set(tokenKey, t, this.getCookieOptions());
            },
            async loginAccount(t) {
                this.updateToken(t);
                await this.fetchAccount();
            },
            logoutAccount() {
                this.clearData();
            },
            logout() {
                this.logoutAccount();
            },
            clearData() {
                this.account = null;
                this.token = null;
                this.lastUpdate = null;

                Cookies.remove(tokenKey, this.getCookieOptions());
            },
            async checkIfShouldRefreshFromServer() {
                if (this.account) {
                    if (!this.lastUpdate) {
                        await this.fetchAccount();
                    } else {
                        let seconds = (this.lastUpdate - Date.now()) / 1000;
                        if (seconds >= refreshSeconds || seconds <= -refreshSeconds) {
                            await this.fetchAccount();
                        }
                    }
                }
            },
        },
    };
}
