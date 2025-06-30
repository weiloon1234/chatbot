import {createWebHistory, createRouter} from 'vue-router';
import GlobalLayout from './layouts/global-layout.vue';
import MainLayout from './layouts/main-layout.vue';
import AuthLayout from './layouts/auth-layout.vue';
import useUserStore from "#/user/stores/user-store.js";
import useUserSettingStore from "#/user/stores/setting-store.js";

const routes = [
    {
        path: '/',
        component: GlobalLayout,
        redirect: {name: 'user.home'},
        children: [
            {
                path: '',
                component: MainLayout,
                meta: { auth: true },
                children: [
                    {
                        path: 'home',
                        component: () => import('./pages/main/home-page.vue'),
                        name: 'user.home',
                    }
                ],
            },
            {
                path: 'auth',
                component: AuthLayout,
                meta: { auth: false },
                children: [
                    {
                        path: 'login',
                        component: () => import('./pages/auth/login-page.vue'),
                        name: 'user.auth.login',
                    }
                ]
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        if (to.hash) {
            return {el: to.hash, behavior: 'smooth'};
        } else {
            // window.scrollTo(0, 0);
            return {left: 0, top: 0};
        }
    },
    // async scrollBehavior (to, from, savedPosition) {
    //     if (to.hash) {
    //         return {
    //             selector: to.hash,
    //             behavior: 'smooth',
    //         };
    //     } else {
    //         if (savedPosition) {
    //             return savedPosition;
    //         } else {
    //             return new Promise((resolve) => {
    //                 window.scrollTo(0, 0);
    //                 resolve();
    //             });
    //         }
    //     }
    // }
});

router.beforeEach(async (to, from, next) => {
    const $accountStore = useUserStore();
    const $settingStore = useUserSettingStore();
    const $role = $accountStore.role;

    if ($accountStore.token) {
        if (!$accountStore.account) {
            await $accountStore.loginAccount($accountStore.token);
        }

        await $accountStore.updateAxios();
        await $accountStore.checkIfShouldRefreshFromServer();
    }

    await $settingStore.checkIfShouldRefreshFromServer();

    //NOT LOGGED IN WHILE THE ROUTE GROUP NEED AUTH
    if (!$accountStore.account) {
        let needAuth = [...to.matched].reverse().find(r => r.meta.auth);
        if (needAuth) {
            return next({name: `${$role}.auth.login`});
        }
    } else {
        let guestOnly = [...to.matched].reverse().find(r => r.meta.auth === false);
        if (guestOnly) {
            return next({name: `${$role}.home`});
        }
    }

    return next();
});
export default router;
