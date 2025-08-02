<template>
    <header
        class="sticky top-0 z-999 flex w-full border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 lg:border-b"
    >
        <div
            class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6"
        >
            <div
                class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 dark:border-gray-800 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4"
            >
                <button
                    type="button"
                    class="z-999 flex h-10 w-10 items-center justify-center rounded-lg border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400 lg:h-11 lg:w-11 lg:border sidebar-toggle-button"
                    :class="[
                        sideBarOpened
                            ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800'
                            : '',
                    ]"
                    @click="emit('on-toggle-side-bar')"
                >
                    <svg
                        class="hidden fill-current lg:block"
                        width="16"
                        height="12"
                        viewBox="0 0 16 12"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z"
                            fill=""
                        ></path>
                    </svg>
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="fill-current lg:hidden block"
                        :class="[sideBarOpened ? 'hidden' : 'block lg:hidden']"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M3.25 6C3.25 5.58579 3.58579 5.25 4 5.25L20 5.25C20.4142 5.25 20.75 5.58579 20.75 6C20.75 6.41421 20.4142 6.75 20 6.75L4 6.75C3.58579 6.75 3.25 6.41422 3.25 6ZM3.25 18C3.25 17.5858 3.58579 17.25 4 17.25L20 17.25C20.4142 17.25 20.75 17.5858 20.75 18C20.75 18.4142 20.4142 18.75 20 18.75L4 18.75C3.58579 18.75 3.25 18.4142 3.25 18ZM4 11.25C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75L12 12.75C12.4142 12.75 12.75 12.4142 12.75 12C12.75 11.5858 12.4142 11.25 12 11.25L4 11.25Z"
                            fill=""
                        ></path>
                    </svg>
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="fill-current"
                        :class="[sideBarOpened ? 'block lg:hidden' : 'hidden']"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
                            fill=""
                        ></path>
                    </svg>
                </button>
                <router-link
                    :to="{ name: 'admin.home' }"
                    class="lg:hidden flex justify-center items-center space-x-1"
                >
                    <img src="/logo.png" class="h-8 w-auto" alt="Logo" />
                    <span class="uppercase text-lg">{{ $appName }}</span>
                </router-link>
                <button
                    type="button"
                    class="z-999 flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden"
                    :class="[
                        menuBarOpened ? 'bg-gray-100 dark:bg-gray-800' : ''
                    ]"
                    @click="emit('on-toggle-menu-bar')"
                >
                    <svg
                        class="fill-current"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M5.99902 10.4951C6.82745 10.4951 7.49902 11.1667 7.49902 11.9951V12.0051C7.49902 12.8335 6.82745 13.5051 5.99902 13.5051C5.1706 13.5051 4.49902 12.8335 4.49902 12.0051V11.9951C4.49902 11.1667 5.1706 10.4951 5.99902 10.4951ZM17.999 10.4951C18.8275 10.4951 19.499 11.1667 19.499 11.9951V12.0051C19.499 12.8335 18.8275 13.5051 17.999 13.5051C17.1706 13.5051 16.499 12.8335 16.499 12.0051V11.9951C16.499 11.1667 17.1706 10.4951 17.999 10.4951ZM13.499 11.9951C13.499 11.1667 12.8275 10.4951 11.999 10.4951C11.1706 10.4951 10.499 11.1667 10.499 11.9951V12.0051C10.499 12.8335 11.1706 13.5051 11.999 13.5051C12.8275 13.5051 13.499 12.8335 13.499 12.0051V11.9951Z"
                            fill=""
                        ></path>
                    </svg>
                </button>
            </div>
            <div
                class="w-full items-center justify-between gap-4 px-5 py-4 shadow-theme-md lg:flex lg:justify-end lg:px-0 lg:shadow-none"
                :class="[menuBarOpened ? 'flex' : 'hidden']"
            >
                <div />
                <div
                    class="relative profile-avatar"
                    v-click-outside="onOutsideClick"
                >
                    <div
                        class="flex items-center text-gray-700 dark:text-gray-400 space-x-2 cursor-pointer"
                        @click="dropDownOpened = !dropDownOpened"
                    >
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-100"
                        >
                            <span class="text-xs font-semibold text-brand-500">
                                {{ avatarInitials }}
                            </span>
                        </div>
                        <span class="block text-theme-sm font-medium">
                            {{ $accountStore?.account?.username }}
                        </span>
                        <svg
                            width="18"
                            height="20"
                            viewBox="0 0 18 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="stroke-gray-500 dark:stroke-gray-400"
                            :class="{
                                'rotate-180': dropDownOpened,
                            }"
                        >
                            <path
                                d="M4.3125 8.65625L9 13.3437L13.6875 8.65625"
                                stroke=""
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            ></path>
                        </svg>
                    </div>
                    <div
                        v-if="dropDownOpened"
                        class="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
                    >
                        <div>
                            <span
                                class="block text-theme-sm font-medium text-gray-700 dark:text-gray-400"
                            >
                                {{ $accountStore?.account?.username }}
                            </span>
                            <span
                                v-if="$accountStore?.account?.email"
                                class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ $accountStore?.account?.email }}
                            </span>
                        </div>
                        <ul
                            class="flex flex-col gap-1 border-b border-gray-200 pb-3 pt-4 dark:border-gray-800"
                        >
                            <li>
                                <a
                                    href="javascript:"
                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                                    @click="onProfileFormOpen"
                                >
                                    <svg
                                        class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z"
                                            fill=""
                                        ></path>
                                    </svg>
                                    {{ $t("Profile") }}
                                </a>
                            </li>
                            <li>
                                <a
                                    href="javascript:"
                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                                    @click="onSecurityFormOpen"
                                >
                                    <svg
                                        class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M10.4858 3.5L13.5182 3.5C13.9233 3.5 14.2518 3.82851 14.2518 4.23377C14.2518 5.9529 16.1129 7.02795 17.602 6.1682C17.9528 5.96567 18.4014 6.08586 18.6039 6.43667L20.1203 9.0631C20.3229 9.41407 20.2027 9.86286 19.8517 10.0655C18.3625 10.9253 18.3625 13.0747 19.8517 13.9345C20.2026 14.1372 20.3229 14.5859 20.1203 14.9369L18.6039 17.5634C18.4013 17.9142 17.9528 18.0344 17.602 17.8318C16.1129 16.9721 14.2518 18.0471 14.2518 19.7663C14.2518 20.1715 13.9233 20.5 13.5182 20.5H10.4858C10.0804 20.5 9.75182 20.1714 9.75182 19.766C9.75182 18.0461 7.88983 16.9717 6.40067 17.8314C6.04945 18.0342 5.60037 17.9139 5.39767 17.5628L3.88167 14.937C3.67903 14.586 3.79928 14.1372 4.15026 13.9346C5.63949 13.0748 5.63946 10.9253 4.15025 10.0655C3.79926 9.86282 3.67901 9.41401 3.88165 9.06303L5.39764 6.43725C5.60034 6.08617 6.04943 5.96581 6.40065 6.16858C7.88982 7.02836 9.75182 5.9539 9.75182 4.23399C9.75182 3.82862 10.0804 3.5 10.4858 3.5ZM13.5182 2L10.4858 2C9.25201 2 8.25182 3.00019 8.25182 4.23399C8.25182 4.79884 7.64013 5.15215 7.15065 4.86955C6.08213 4.25263 4.71559 4.61859 4.0986 5.68725L2.58261 8.31303C1.96575 9.38146 2.33183 10.7477 3.40025 11.3645C3.88948 11.647 3.88947 12.3531 3.40026 12.6355C2.33184 13.2524 1.96578 14.6186 2.58263 15.687L4.09863 18.3128C4.71562 19.3814 6.08215 19.7474 7.15067 19.1305C7.64015 18.8479 8.25182 19.2012 8.25182 19.766C8.25182 20.9998 9.25201 22 10.4858 22H13.5182C14.7519 22 15.7518 20.9998 15.7518 19.7663C15.7518 19.2015 16.3632 18.8487 16.852 19.1309C17.9202 19.7476 19.2862 19.3816 19.9029 18.3134L21.4193 15.6869C22.0361 14.6185 21.6701 13.2523 20.6017 12.6355C20.1125 12.3531 20.1125 11.647 20.6017 11.3645C21.6701 10.7477 22.0362 9.38152 21.4193 8.3131L19.903 5.68667C19.2862 4.61842 17.9202 4.25241 16.852 4.86917C16.3632 5.15138 15.7518 4.79856 15.7518 4.23377C15.7518 3.00024 14.7519 2 13.5182 2ZM9.6659 11.9999C9.6659 10.7103 10.7113 9.66493 12.0009 9.66493C13.2905 9.66493 14.3359 10.7103 14.3359 11.9999C14.3359 13.2895 13.2905 14.3349 12.0009 14.3349C10.7113 14.3349 9.6659 13.2895 9.6659 11.9999ZM12.0009 8.16493C9.88289 8.16493 8.1659 9.88191 8.1659 11.9999C8.1659 14.1179 9.88289 15.8349 12.0009 15.8349C14.1189 15.8349 15.8359 14.1179 15.8359 11.9999C15.8359 9.88191 14.1189 8.16493 12.0009 8.16493Z"
                                            fill=""
                                        ></path>
                                    </svg>
                                    {{ $t("Account security") }}
                                </a>
                            </li>
                            <li
                                v-if="parseInt($accountStore.account.type) === 2"
                            >
                                <a
                                    href="javascript:"
                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                                    @click="onLogOpen"
                                >
                                    <svg
                                        class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 512 512"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <title>log</title>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g id="log-white" fill="currentColor" transform="translate(85.572501, 42.666667)">
                                                <path d="M236.349632,7.10542736e-15 L1.68296533,7.10542736e-15 L1.68296533,234.666667 L44.349632,234.666667 L44.349632,192 L44.349632,169.6 L44.349632,42.6666667 L218.642965,42.6666667 L300.349632,124.373333 L300.349632,169.6 L300.349632,192 L300.349632,234.666667 L343.016299,234.666667 L343.016299,106.666667 L236.349632,7.10542736e-15 L236.349632,7.10542736e-15 Z M4.26325641e-14,405.333333 L4.26325641e-14,277.360521 L28.8096875,277.360521 L28.8096875,382.755208 L83.81,382.755208 L83.81,405.333333 L4.26325641e-14,405.333333 Z M153.17,275.102708 C173.279583,275.102708 188.692917,281.484792 199.41,294.248958 C209.705625,306.47125 214.853437,322.185625 214.853437,341.392083 C214.853437,362.404792 208.772396,379.112604 196.610312,391.515521 C186.134062,402.232604 171.653958,407.591146 153.17,407.591146 C133.060417,407.591146 117.647083,401.209062 106.93,388.444896 C96.634375,376.222604 91.4865625,360.267396 91.4865625,340.579271 C91.4865625,319.988021 97.5676042,303.490937 109.729687,291.088021 C120.266146,280.431146 134.74625,275.102708 153.17,275.102708 Z M153.079687,297.680833 C142.663646,297.680833 134.625833,302.015833 128.96625,310.685833 C123.848542,318.512917 121.289687,328.567708 121.289687,340.850208 C121.289687,355.059375 124.330208,366.0775 130.41125,373.904583 C136.131042,381.310208 143.717292,385.013021 153.17,385.013021 C163.525833,385.013021 171.59375,380.647917 177.37375,371.917708 C182.491458,364.211042 185.050312,354.035833 185.050312,341.392083 C185.050312,327.483958 182.009792,316.616354 175.92875,308.789271 C170.208958,301.383646 162.592604,297.680833 153.079687,297.680833 Z M343.91,333.715521 L343.91,399.011458 C336.564583,401.48 331.386667,403.105625 328.37625,403.888333 C319.043958,406.356875 309.019271,407.591146 298.302187,407.591146 C277.229271,407.591146 261.18375,402.292812 250.165625,391.696146 C237.943333,380.015729 231.832187,363.729375 231.832187,342.837083 C231.832187,318.813958 239.418437,300.69125 254.590937,288.468958 C265.609062,279.558125 280.480521,275.102708 299.205312,275.102708 C315.220729,275.102708 330.122292,278.022812 343.91,283.863021 L334.065937,306.350833 C327.563437,303.099583 321.87375,300.826719 316.996875,299.53224 C312.12,298.23776 306.761458,297.590521 300.92125,297.590521 C286.952917,297.590521 276.657292,302.13625 270.034375,311.227708 C264.435,318.934375 261.635312,329.079479 261.635312,341.663021 C261.635312,356.775312 265.849896,368.154687 274.279062,375.801146 C281.022396,381.942396 289.391354,385.013021 299.385937,385.013021 C305.226146,385.013021 310.765312,384.019583 316.003437,382.032708 L316.003437,356.293646 L293.967187,356.293646 L293.967187,333.715521 L343.91,333.715521 Z" id="XLS">

                                                </path>
                                            </g>
                                        </g>
                                    </svg>
                                    {{ $t("Logs") }}
                                </a>
                            </li>
                        </ul>
                        <button
                            class="group mt-3 flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                            @click="onLogout"
                        >
                            <svg
                                class="fill-gray-500 group-hover:fill-gray-700 dark:group-hover:fill-gray-300"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M15.1007 19.247C14.6865 19.247 14.3507 18.9112 14.3507 18.497L14.3507 14.245H12.8507V18.497C12.8507 19.7396 13.8581 20.747 15.1007 20.747H18.5007C19.7434 20.747 20.7507 19.7396 20.7507 18.497L20.7507 5.49609C20.7507 4.25345 19.7433 3.24609 18.5007 3.24609H15.1007C13.8581 3.24609 12.8507 4.25345 12.8507 5.49609V9.74501L14.3507 9.74501V5.49609C14.3507 5.08188 14.6865 4.74609 15.1007 4.74609L18.5007 4.74609C18.9149 4.74609 19.2507 5.08188 19.2507 5.49609L19.2507 18.497C19.2507 18.9112 18.9149 19.247 18.5007 19.247H15.1007ZM3.25073 11.9984C3.25073 12.2144 3.34204 12.4091 3.48817 12.546L8.09483 17.1556C8.38763 17.4485 8.86251 17.4487 9.15549 17.1559C9.44848 16.8631 9.44863 16.3882 9.15583 16.0952L5.81116 12.7484L16.0007 12.7484C16.4149 12.7484 16.7507 12.4127 16.7507 11.9984C16.7507 11.5842 16.4149 11.2484 16.0007 11.2484L5.81528 11.2484L9.15585 7.90554C9.44864 7.61255 9.44847 7.13767 9.15547 6.84488C8.86248 6.55209 8.3876 6.55226 8.09481 6.84525L3.52309 11.4202C3.35673 11.5577 3.25073 11.7657 3.25073 11.9984Z"
                                    fill=""
                                ></path>
                            </svg>
                            {{ $t("Sign out") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Store-based modals -->
        <auto-modal v-if="$modalStore.isOpen" />
    </header>
</template>

<script setup>
import { computed, inject, ref } from "vue";
import { useRouter } from "vue-router";
import AutoModal from "#/shared/components/auto-modal.vue";
import ProfileForm from "#/admin/layouts/header/profile-form.vue";
import SecurityForm from "#/admin/layouts/header/security-form.vue";

defineProps({
    sideBarOpened: {
        required: true,
        type: Boolean,
    },
    menuBarOpened: {
        required: true,
        type: Boolean,
    },
});

const emit = defineEmits(["on-toggle-side-bar", "on-toggle-menu-bar"]);

const $helper = inject("$helper");
const $router = useRouter();
const $accountStore = inject("$accountStore");
const $modalStore = inject("$modalStore");
if (!$modalStore) {
    throw new Error('Modal store is required. Please provide $modalStore via injection.');
}

const dropDownOpened = ref(false);

const onOutsideClick = (event) => {
    if ($helper.hasClassOrAncestor(event.target, "profile-avatar")) {
        return;
    }

    if (dropDownOpened.value) {
        dropDownOpened.value = false;
    }
};

const onProfileFormOpen = () => {
    dropDownOpened.value = false;
    $modalStore.open(ProfileForm, {}, { 
        small: true, 
        title: 'Profile Settings' 
    });
};
const onSecurityFormOpen = () => {
    dropDownOpened.value = false;
    $modalStore.open(SecurityForm, {}, { 
        small: true, 
        title: 'Security Settings' 
    });
};

const avatarInitials = computed(() => {
    const account = $accountStore.account;

    // Get name parts or username
    const nameParts = account?.name?.trim().split(/\s+/) || [];
    const username = account?.username?.trim() || '';

    // Try to get from name first
    if (nameParts.length > 0) {
        // First letter of first name
        const first = nameParts[0][0] || '';
        // First letter of last name (if exists)
        const last = nameParts.length > 1 ? nameParts[nameParts.length - 1][0] : '';
        return `${first}${last}`.toUpperCase();
    }

    // Fallback to username
    if (username.length >= 2) {
        return username.substring(0, 2).toUpperCase();
    }

    // Fallback for empty cases
    return username.length === 1 ? username.toUpperCase() : '??';
});

const onLogOpen = async () => {
    dropDownOpened.value = false;
    await $router.push({name: 'admin.etc.logs'});
};

const onLogout = async () => {
    await $accountStore.logoutAccount();
    await $router.replace({ name: `${$accountStore.role}.auth.login` });
};
</script>
