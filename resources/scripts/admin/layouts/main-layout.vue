<template>
    <admin-sidebar
        :side-bar-opened="sideBarOpened"
        @on-toggle-side-bar="onToggleSideBar"
    />
    <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
        <div
            class="fixed z-9 h-screen w-full bg-gray-900/50"
            :class="[
                sideBarOpened ? 'block lg:hidden' : 'hidden'
            ]"
        />
        <main>
            <admin-header
                :side-bar-opened="sideBarOpened"
                :menu-bar-opened="menuBarOpened"
                @on-toggle-side-bar="onToggleSideBar"
                @on-toggle-menu-bar="onToggleMenuBar"
            />
            <div
                id="main-content-container"
                class="p-4 mx-auto max-w-(--breakpoint-4xl) md:p-6"
            >
                <router-view />
            </div>
        </main>
    </div>
</template>
<script setup>
import AdminSidebar from "#/admin/components/admin-sidebar.vue";
import AdminHeader from "#/admin/components/admin-header.vue";
import { ref } from "vue";

const sideBarOpened = ref(false);
const menuBarOpened = ref(false);

const onToggleSideBar = () => {
    sideBarOpened.value = !sideBarOpened.value;
};

const onToggleMenuBar = () => {
    menuBarOpened.value = !menuBarOpened.value;
};
</script>
