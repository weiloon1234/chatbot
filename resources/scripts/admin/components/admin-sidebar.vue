<template>
    <aside
        class="sidebar fixed top-0 left-0 z-999 flex h-screen w-[290px] flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-all duration-300 lg:static lg:translate-x-0 dark:border-gray-800 dark:bg-black"
        :class="{
            'translate-x-0 lg:w-[90px]':
                $windowState.$isMobile && sideBarOpened && false,
            '-translate-x-full': $windowState.$isMobile && !sideBarOpened,
        }"
        v-click-outside="onOutsideClick"
    >
        <div
            class="sidebar-header flex items-center gap-2 pt-8 pb-7 justify-center"
        >
            <router-link
                :to="{ name: 'admin.home' }"
                class="flex justify-center items-center space-x-2"
            >
                <img src="/logo.png" class="h-8 w-auto" alt="Logo" />
                <span class="uppercase text-lg">{{ $appName }}</span>
            </router-link>
        </div>
        <div
            class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear"
        >
            <nav>
                <div>
                    <h3
                        class="mb-4 text-xs leading-[20px] text-gray-400 uppercase"
                    >
                        <span class="menu-group-title">
                            {{ $t("Menu") }}
                        </span>
                        <svg
                            class="menu-group-icon mx-auto fill-current hidden"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                                fill="currentColor"
                            ></path>
                        </svg>
                    </h3>
                    <ul class="mb-6 flex flex-col gap-4">
                        <li
                            v-for="(parent, index) in menus"
                            :key="`parent-${index}`"
                        >
                            <a
                                v-if="parent.children?.length"
                                href="javascript:"
                                class="menu-item group"
                                :class="[
                                    isParentActive(parent) ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                                @click.prevent="toggleParent(parent.name)"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    :class="[
                                        isParentActive(parent) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'
                                    ]"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                                <span class="menu-item-text capitalize"> {{ $t(parent.name) }} </span>
                                <span
                                    v-if="parent.totalNotification > 0"
                                    class="bg-red-500 text-white aspect-square rounded-md px-2 text-xs flex flex-col items-center justify-center"
                                >
                                    <span>
                                        {{ parent.totalNotification }}
                                    </span>
                                </span>
                                <template
                                    v-if="parent.children?.length"
                                >
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="menu-item-arrow"
                                        :class="[
                                            isParentExpanded(parent.name) ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive'
                                        ]"
                                    >
                                        <path
                                            d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585"
                                            stroke=""
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        ></path>
                                    </svg>
                                </template>
                            </a>
                            <router-link
                                v-else
                                :to="{name: parent.route}"
                                class="menu-item group"
                                :class="[
                                    isRouteActive(parent.route) ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                                @click="handleMenuClicked"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    :class="[
                                        isParentExpanded(parent.name) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'
                                    ]"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                                <span class="menu-item-text capitalize flex-1">
                                    <span class="w-full flex justify-between space-x-2 items-center">
                                        <span class="flex-1">
                                            {{ $t(parent.name) }}
                                        </span>
                                        <span
                                            v-if="parent.notification && notifications[parent.notification] && parseInt(notifications[parent.notification]) > 0"
                                            class="bg-red-500 text-white aspect-square rounded-md px-2 text-xs flex flex-col items-center justify-center"
                                        >
                                            <span>
                                                {{ notifications[parent.notification] }}
                                            </span>
                                        </span>
                                    </span>
                                </span>
                            </router-link>
                            <div
                                v-if="parent.children?.length"
                                class="translate transform overflow-hidden"
                                :class="[
                                     isParentExpanded(parent.name) ? 'block' : 'hidden'
                                ]"
                            >
                                <ul class="menu-dropdown mt-2 flex flex-col gap-1 pl-9">
                                    <li
                                        v-for="(child, index) in parent.children"
                                        :key="`child-${index}`"
                                    >
                                        <router-link
                                            :to="{ name: child.route }"
                                            class="menu-dropdown-item group capitalize"
                                            :class="[
                                                isRouteActive(child.route) ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'
                                            ]"
                                            @click="handleMenuClicked"
                                        >
                                            <span class="w-full flex justify-between space-x-2 items-center">
                                                <span class="flex-1">
                                                    {{ $t(child.name) }}
                                                </span>
                                                <span
                                                    v-if="child.notification && notifications[child.notification] && parseInt(notifications[child.notification]) > 0"
                                                    class="bg-red-500 text-white aspect-square rounded-md px-2 text-xs flex flex-col items-center justify-center"
                                                >
                                                    <span>
                                                        {{ notifications[child.notification] }}
                                                    </span>
                                                </span>
                                            </span>
                                        </router-link>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { computed, inject, onMounted, ref, watch } from "vue";
import SideBar from "#/admin/side-bar.js";
import { useRoute } from "vue-router";

const props = defineProps({
    sideBarOpened: {
        required: true,
        type: Boolean,
    },
});

const emit = defineEmits(["on-toggle-side-bar"]);

const $helper = inject("$helper");
const $windowState = inject("$windowState");
const $accountStore = inject("$accountStore");
const $admin = computed(() => $accountStore.account);
const $route = useRoute();

const notifications = computed(() => {
    return $accountStore.notifications ?? [];
});

const expandedParents = ref(new Set());
const menus = computed(() => {
    const isDeveloper = parseInt($admin.value.type) === 2;

    return SideBar.filter((parent) => {
        // Handle parent items without children
        if (!parent.children?.length) {
            if (parent.isDeveloper && !isDeveloper) return false;

            return parent.permissions?.length
                ? $helper.hasPermission($admin.value, parent.permissions)
                : true;
        }

        // For parents with children, check if any child is accessible
        return parent.children.some((child) => {
            if (child.isDeveloper && !isDeveloper) return false;

            return child.permissions?.length
                ? $helper.hasPermission($admin.value, child.permissions)
                : true;
        });
    }).map((parent) => {
        let filteredChildren = [];
        if (parent.children) {
            filteredChildren = parent.children.filter((child) => {
                if (child.isDeveloper && !isDeveloper) return false;
                return child.permissions?.length
                    ? $helper.hasPermission($admin.value, child.permissions)
                    : true;
            });
        }

        // Calculate total child notifications
        let totalNotification = 0;
        if (filteredChildren.length) {
            totalNotification = filteredChildren.reduce((sum, child) => {
                if (child.notification && notifications.value[child.notification]) {
                    return sum + parseInt(notifications.value[child.notification] || 0);
                }
                return sum;
            }, 0);
        }

        return {
            ...parent,
            children: filteredChildren,
            totalNotification,
        };
    });
});

watch(() => $route.name, () => {
    autoExpandParents();
});

const toggleParent = (parentName) => {
    if (expandedParents.value.has(parentName)) {
        expandedParents.value.delete(parentName);
    } else {
        expandedParents.value.add(parentName);
    }
}

// Check if parent is expanded
const isParentExpanded = (parentName) => {
    return expandedParents.value.has(parentName)
}

// Check if route matches parent or any child
const isParentActive = (parent) => {
    if (parent.route) return isRouteActive(parent.route);
    return parent.children?.some(child => isRouteActive(child.route));
};

// Check route match
const isRouteActive = (routeName) => {
    return $route.name === routeName || $route.name?.startsWith(`${routeName}.`)
};

const hasActiveChild = (parent) => {
    return parent.children?.some(child => {
        if (child.children) return hasActiveChild(child)
        return child.route ? isRouteActive(child.route) : false
    })
}

const autoExpandParents = () => {
    expandedParents.value.clear()
    menus.value.forEach(parent => {
        if (parent.children?.some(child =>
            isRouteActive(child.route) || hasActiveChild(parent))
        ) {
            expandedParents.value.add(parent.name)
        }
    })
}

const onOutsideClick = (event) => {
    if ($helper.hasClassOrAncestor(event.target, "sidebar-toggle-button")) {
        return;
    }

    if (props.sideBarOpened) {
        emit("on-toggle-side-bar");
    }
};

const handleMenuClicked = () => {
    if (props.sideBarOpened) {
        emit("on-toggle-side-bar");
    }
};

onMounted(() => {
    autoExpandParents();
});
</script>
