import {createWebHistory, createRouter} from 'vue-router';
import GlobalLayout from './layouts/global-layout.vue';
import MainLayout from './layouts/main-layout.vue';
import AuthLayout from './layouts/auth-layout.vue';
import useAdminStore from "#/admin/stores/admin-store.js";
import useAdminSettingStore from "#/admin/stores/setting-store.js";
import BlankRouter from "#/shared/components/blank-router.vue";

const routes = [
    {
        path: '/admin',
        component: GlobalLayout,
        children: [
            {
                path: '',
                component: MainLayout,
                meta: { auth: true },
                redirect: {name: 'admin.home'},
                children: [
                    {
                        path: 'home',
                        component: () => import('./pages/main/home-page.vue'),
                        name: 'admin.home',
                    },
                    {
                        path: 'user',
                        redirect: {name: 'admin.user.user.list'},
                        children: [
                            {
                                path: 'user/list',
                                component: () => import('./pages/main/user/user/list-page.vue'),
                                name: 'admin.user.user.list',
                            },
                            {
                                path: 'credit/list',
                                component: () => import('./pages/main/user/credit/list-page.vue'),
                                name: 'admin.user.credit.list',
                            },
                            {
                                path: 'deposit/list',
                                component: () => import('./pages/main/user/deposit/list-page.vue'),
                                name: 'admin.user.deposit.list',
                            },
                            {
                                path: 'withdrawal/list',
                                component: () => import('./pages/main/user/withdrawal/list-page.vue'),
                                name: 'admin.user.withdrawal.list',
                            },
                        ],
                    },
                    {
                        path: 'article',
                        component: BlankRouter,
                        redirect: {name: 'admin.article.article.list'},
                        children: [
                            {
                                path: 'article/list',
                                component: () => import('./pages/main/article/article/list-page.vue'),
                                name: 'admin.article.article.list',
                            },
                            {
                                path: 'article/form/:id?',
                                component: () => import('./pages/main/article/article/model-form.vue'),
                                name: 'admin.article.article.form',
                            },
                            {
                                path: 'category/list',
                                component: () => import('./pages/main/article/category/list-page.vue'),
                                name: 'admin.article.category.list',
                            },
                        ],
                    },
                    {
                        path: 'management',
                        component: BlankRouter,
                        redirect: {name: 'admin.management.admin.list'},
                        children: [
                            {
                                path: 'admin/list',
                                component: () => import('./pages/main/management/admin/list-page.vue'),
                                name: 'admin.management.admin.list',
                            },
                            {
                                path: 'admin/group/list',
                                component: () => import('./pages/main/management/admin/group/list-page.vue'),
                                name: 'admin.management.admin.group.list',
                            },
                        ],
                    },
                    {
                        path: 'whatsapp',
                        component: BlankRouter,
                        redirect: {name: 'admin.whatsapp.home'},
                        children: [
                            {
                                path: 'home',
                                name: 'admin.whatsapp.home',
                                component: () => import('./pages/main/whatsapp/home-page.vue'),
                            }
                        ],
                    },
                    {
                        path: 'other',
                        component: BlankRouter,
                        redirect: {name: 'admin.other.setting.list'},
                        children: [
                            {
                                path: 'setting/list',
                                component: () => import('./pages/main/other/setting/list-page.vue'),
                                name: 'admin.other.setting.list',
                            },
                            {
                                path: 'page/list',
                                component: () => import('./pages/main/other/page/list-page.vue'),
                                name: 'admin.other.page.list',
                            },
                            {
                                path: 'page/form/:id?',
                                component: () => import('./pages/main/other/page/model-form.vue'),
                                name: 'admin.other.page.form',
                            },
                            {
                                path: 'country/list',
                                component: () => import('./pages/main/other/country/list-page.vue'),
                                name: 'admin.other.country.list',
                            },
                            {
                                path: 'bank/list',
                                component: () => import('./pages/main/other/bank/list-page.vue'),
                                name: 'admin.other.bank.list',
                            },
                            {
                                path: 'company-bank/list',
                                component: () => import('./pages/main/other/company-bank/list-page.vue'),
                                name: 'admin.other.company-bank.list',
                            },
                            {
                                path: 'audit-trail/list',
                                component: () => import('./pages/main/other/audit-trail/list-page.vue'),
                                name: 'admin.other.audit-trail.list',
                            },
                        ],
                    },
                    {
                        path: 'flow',
                        component: BlankRouter,
                        redirect: {name: 'admin.flow.designer'},
                        children: [
                            {
                                path: 'designer',
                                component: () => import('./pages/main/flow/designer/home-page.vue'),
                                name: 'admin.flow.designer',
                            },
                        ],
                    },
                    {
                        path: 'logs',
                        name: 'admin.etc.logs',
                        component: () => import('./pages/main/etc/logs-page.vue'),
                    },
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
                        name: 'admin.auth.login',
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
    const $accountStore = useAdminStore();
    const $settingStore = useAdminSettingStore();
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
