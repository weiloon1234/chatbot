<template>
    <div
        v-if="componentReady"
        class="form-input-container"
        :class="{
            'has-error': hasError,
            disabled: disabled || readonly,
        }"
    >
        <template v-if="hasLabelSlot">
            <slot name="label" />
        </template>
        <template v-else-if="!hideLabel && (label || name)">
            <form-field-label :label="label ?? name" />
        </template>
        <div
            v-if="editor"
            class="w-full h-auto"
        >
            <div class="w-full border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex flex-wrap items-center">
                        <div class="flex items-center space-x-1 rtl:space-x-reverse flex-wrap">
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().toggleBold().run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5h4.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0-7H6m2 7h6.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0 0H6"/>
                                </svg>
                                <span class="sr-only">Bold</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().toggleItalic().run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.874 19 6.143-14M6 19h6.33m-.66-14H18"/>
                                </svg>
                                <span class="sr-only">Italic</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().toggleUnderline().run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 19h12M8 5v9a4 4 0 0 0 8 0V5M6 5h4m4 0h4"/>
                                </svg>
                                <span class="sr-only">Underline</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().toggleStrike().run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6.2V5h12v1.2M7 19h6m.2-14-1.677 6.523M9.6 19l1.029-4M5 5l6.523 6.523M19 19l-7.477-7.477"/>
                                </svg>
                                <span class="sr-only">Strike</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="onToggleHighlight"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 19.2H5.5c-.3 0-.5-.2-.5-.5V16c0-.2.2-.4.5-.4h13c.3 0 .5.2.5.4v2.7c0 .3-.2.5-.5.5H18m-6-1 1.4 1.8h.2l1.4-1.7m-7-5.4L12 4c0-.1 0-.1 0 0l4 8.8m-6-2.7h4m-7 2.7h2.5m5 0H17"/>
                                </svg>
                                <span class="sr-only">Highlight</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().toggleCode().run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 8-4 4 4 4m8 0 4-4-4-4m-2-3-4 14"/>
                                </svg>
                                <span class="sr-only">Code</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="onToggleLink"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961"/>
                                </svg>
                                <span class="sr-only">Link</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().unsetLink().run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M13.2 9.8a3.4 3.4 0 0 0-4.8 0L5 13.2A3.4 3.4 0 0 0 9.8 18l.3-.3m-.3-4.5a3.4 3.4 0 0 0 4.8 0L18 9.8A3.4 3.4 0 0 0 13.2 5l-1 1m7.4 14-1.8-1.8m0 0L16 16.4m1.8 1.8 1.8-1.8m-1.8 1.8L16 20"/>
                                </svg>
                                <span class="sr-only">Remove link</span>
                            </button>
                            <popover
                                class="relative"
                            >
                                <popover-button
                                    class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                >
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6.2V5h11v1.2M8 5v14m-3 0h6m2-6.8V11h8v1.2M17 11v8m-1.5 0h3"/>
                                    </svg>
                                    <span class="sr-only">Text size</span>
                                </popover-button>
                                <popover-panel
                                    class="absolute z-10"
                                >
                                    <div class="w-72 rounded-sm bg-white p-2 shadow-sm dark:bg-gray-700">
                                        <ul class="space-y-1 text-sm font-medium" aria-labelledby="toggleTextSizeButton">
                                            <li>
                                                <button
                                                    type="button" data-text-size="default" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                    @click="onChangeFontSize($event)"
                                                >
                                                    Default
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-text-size="12px" class="flex justify-between items-center w-full text-xs rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                    @click="onChangeFontSize($event)"
                                                >
                                                    12px (Tiny)
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-text-size="14px" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                    @click="onChangeFontSize($event)"
                                                >
                                                    14px (Small)
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-text-size="18px" class="flex justify-between items-center w-full text-lg rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                    @click="onChangeFontSize($event)"
                                                >
                                                    18px (Lead)
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-text-size="24px" class="flex justify-between items-center w-full text-2xl rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                    @click="onChangeFontSize($event)"
                                                >
                                                    24px (Large)
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-text-size="36px" class="flex justify-between items-center w-full text-4xl rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                    @click="onChangeFontSize($event)"
                                                >
                                                    36px (Huge)
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </popover-panel>
                            </popover>
                            <popover
                                class="relative"
                            >
                                <popover-button
                                    class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                >
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="25" height="24" fill="none" viewBox="0 0 25 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m6.532 15.982 1.573-4m-1.573 4h-1.1m1.1 0h1.65m-.077-4 2.725-6.93a.11.11 0 0 1 .204 0l2.725 6.93m-5.654 0H8.1m.006 0h5.654m0 0 .617 1.569m5.11 4.453c0 1.102-.854 1.996-1.908 1.996-1.053 0-1.907-.894-1.907-1.996 0-1.103 1.907-4.128 1.907-4.128s1.909 3.025 1.909 4.128Z"/>
                                    </svg>
                                    <span class="sr-only">Text color</span>
                                </popover-button>
                                <popover-panel
                                    class="absolute z-10"
                                >
                                    <div class="w-48 rounded-sm bg-white p-2 shadow-sm dark:bg-gray-700">
                                        <div class="grid grid-cols-6 gap-2 group mb-3 items-center p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <input
                                                ref="colorInput"
                                                type="color" value="#e66465" class="border-gray-200 border bg-gray-50 dark:bg-gray-700 dark:border-gray-600 rounded-md p-px px-1 hover:bg-gray-50 group-hover:bg-gray-50 dark:group-hover:bg-gray-700 w-full h-8 col-span-3"
                                                @input="handleColorChange"
                                            />
                                            <label for="color" class="text-gray-500 dark:text-gray-400 text-sm font-medium col-span-3 group-hover:text-gray-900 dark:group-hover:text-white">Pick a color</label>
                                        </div>
                                        <div class="grid grid-cols-6 gap-1 mb-3">
                                            <button type="button" data-hex-color="#1A56DB" style="background-color: #1A56DB" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Blue</span></button>
                                            <button type="button" data-hex-color="#0E9F6E" style="background-color: #0E9F6E" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Green</span></button>
                                            <button type="button" data-hex-color="#FACA15" style="background-color: #FACA15" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Yellow</span></button>
                                            <button type="button" data-hex-color="#F05252" style="background-color: #F05252" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Red</span></button>
                                            <button type="button" data-hex-color="#FF8A4C" style="background-color: #FF8A4C" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Orange</span></button>
                                            <button type="button" data-hex-color="#0694A2" style="background-color: #0694A2" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Teal</span></button>
                                            <button type="button" data-hex-color="#B4C6FC" style="background-color: #B4C6FC" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light indigo</span></button>
                                            <button type="button" data-hex-color="#8DA2FB" style="background-color: #8DA2FB" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Indigo</span></button>
                                            <button type="button" data-hex-color="#5145CD" style="background-color: #5145CD" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Purple</span></button>
                                            <button type="button" data-hex-color="#771D1D" style="background-color: #771D1D" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Brown</span></button>
                                            <button type="button" data-hex-color="#FCD9BD" style="background-color: #FCD9BD" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light orange</span></button>
                                            <button type="button" data-hex-color="#99154B" style="background-color: #99154B" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Bordo</span></button>
                                            <button type="button" data-hex-color="#7E3AF2" style="background-color: #7E3AF2" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Dark Purple</span></button>
                                            <button type="button" data-hex-color="#CABFFD" style="background-color: #CABFFD" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light</span></button>
                                            <button type="button" data-hex-color="#D61F69" style="background-color: #D61F69" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Dark Pink</span></button>
                                            <button type="button" data-hex-color="#F8B4D9" style="background-color: #F8B4D9" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Pink</span></button>
                                            <button type="button" data-hex-color="#F6C196" style="background-color: #F6C196" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Cream</span></button>
                                            <button type="button" data-hex-color="#A4CAFE" style="background-color: #A4CAFE" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light Blue</span></button>
                                            <button type="button" data-hex-color="#5145CD" style="background-color: #5145CD" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Dark Blue</span></button>
                                            <button type="button" data-hex-color="#B43403" style="background-color: #B43403" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Orange Brown</span></button>
                                            <button type="button" data-hex-color="#FCE96A" style="background-color: #FCE96A" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light Yellow</span></button>
                                            <button type="button" data-hex-color="#1E429F" style="background-color: #1E429F" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Navy Blue</span></button>
                                            <button type="button" data-hex-color="#768FFD" style="background-color: #768FFD" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light Purple</span></button>
                                            <button type="button" data-hex-color="#BCF0DA" style="background-color: #BCF0DA" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light Green</span></button>
                                            <button type="button" data-hex-color="#EBF5FF" style="background-color: #EBF5FF" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Sky Blue</span></button>
                                            <button type="button" data-hex-color="#16BDCA" style="background-color: #16BDCA" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Cyan</span></button>
                                            <button type="button" data-hex-color="#E74694" style="background-color: #E74694" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Pink</span></button>
                                            <button type="button" data-hex-color="#83B0ED" style="background-color: #83B0ED" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Darker Sky Blue</span></button>
                                            <button type="button" data-hex-color="#03543F" style="background-color: #03543F" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Forest Green</span></button>
                                            <button type="button" data-hex-color="#111928" style="background-color: #111928" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Black</span></button>
                                            <button type="button" data-hex-color="#4B5563" style="background-color: #4B5563" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Stone</span></button>
                                            <button type="button" data-hex-color="#6B7280" style="background-color: #6B7280" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Gray</span></button>
                                            <button type="button" data-hex-color="#D1D5DB" style="background-color: #D1D5DB" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Light Gray</span></button>
                                            <button type="button" data-hex-color="#F3F4F6" style="background-color: #F3F4F6" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Cloud Gray</span></button>
                                            <button type="button" data-hex-color="#F3F4F6" style="background-color: #F3F4F6" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Cloud Gray</span></button>
                                            <button type="button" data-hex-color="#F9FAFB" style="background-color: #F9FAFB" class="w-6 h-6 rounded-md" @click="onSetColor($event)"><span class="sr-only">Heaven Gray</span></button>
                                        </div>
                                        <button
                                            type="button" class="py-1.5 text-sm font-medium text-gray-500 focus:outline-none bg-white rounded-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-700 dark:text-gray-400 dark:hover:text-white w-full dark:hover:bg-gray-600"
                                            @click="editor.commands.unsetColor()"
                                        >
                                            Reset color
                                        </button>
                                    </div>
                                </popover-panel>
                            </popover>
                            <popover
                                class="relative"
                            >
                                <popover-button
                                    class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                >
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10.6 19 4.298-10.93a.11.11 0 0 1 .204 0L19.4 19m-8.8 0H9.5m1.1 0h1.65m7.15 0h-1.65m1.65 0h1.1m-7.7-3.985h4.4M3.021 16l1.567-3.985m0 0L7.32 5.07a.11.11 0 0 1 .205 0l2.503 6.945h-5.44Z"/>
                                    </svg>
                                    <span class="sr-only">Font family</span>
                                </popover-button>
                                <popover-panel
                                    class="absolute z-10"
                                >
                                    <div
                                        class="w-48 rounded-sm bg-white p-2 shadow-sm dark:bg-gray-700"
                                    >
                                        <ul class="space-y-1 text-sm font-medium" aria-labelledby="toggleFontFamilyButton">
                                            <li>
                                                <button
                                                    type="button" data-font-family="default" class="flex justify-between items-center w-full text-sm font-sans rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Default
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="Arial, sans-serif" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: Arial, sans-serif;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Arial
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="'Courier New', monospace" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: 'Courier New', monospace;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Courier New
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="Georgia, serif" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: Georgia, serif;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Georgia
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="'Lucida Sans Unicode', sans-serif" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: 'Lucida Sans Unicode', sans-serif;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Lucida Sans Unicode
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="Tahoma, sans-serif" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: Tahoma, sans-serif;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Tahoma
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="'Times New Roman', serif;" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: 'Times New Roman', serif;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Times New Roman
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="'Trebuchet MS', sans-serif" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: 'Trebuchet MS', sans-serif;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Trebuchet MS
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    type="button" data-font-family="Verdana, sans-serif" class="flex justify-between items-center w-full text-sm rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white" style="font-family: Verdana, sans-serif;"
                                                    @click="onFontFamilyChange($event)"
                                                >
                                                    Verdana
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </popover-panel>
                            </popover>
                            <div class="px-1">
                                <span class="block w-px h-4 bg-gray-300 dark:bg-gray-600"></span>
                            </div>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().setTextAlign('left').run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h8m-8 4h12M6 14h8m-8 4h12"/>
                                </svg>
                                <span class="sr-only">Align left</span>
                            </button>
                            <button
                                type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().setTextAlign('center').run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h8M6 10h12M8 14h8M6 18h12"/>
                                </svg>
                                <span class="sr-only">Align center</span>
                            </button>
                            <button
                                type="button"
                                class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="editor.chain().focus().setTextAlign('right').run()"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6h-8m8 4H6m12 4h-8m8 4H6"/>
                                </svg>
                                <span class="sr-only">Align right</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-2 flex-wrap">
                        <popover
                            class="relative"
                        >
                            <popover-button
                                class="flex items-center justify-center rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-50 dark:bg-gray-600 dark:text-gray-400 dark:hover:bg-gray-500 dark:hover:text-white dark:focus:ring-gray-600"
                            >
                                Format
                                <svg class="-me-0.5 ms-1.5 h-3.5 w-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                </svg>
                            </popover-button>
                            <popover-panel
                                class="absolute z-10"
                            >
                                <div class="w-72 rounded-sm bg-white p-2 shadow-sm dark:bg-gray-700">
                                    <ul class="space-y-1 text-sm font-medium">
                                        <li>
                                            <button
                                                type="button" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                @click="editor.chain().focus().setParagraph().run()"
                                            >
                                                Paragraph
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                type="button" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                @click="onToggleHeading(1)"
                                            >
                                                Heading 1
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                type="button" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                @click="onToggleHeading(2)"
                                            >
                                                Heading 2
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                type="button" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                @click="onToggleHeading(3)"
                                            >
                                                Heading 3
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                type="button" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                @click="onToggleHeading(4)"
                                            >
                                                Heading 4
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                type="button" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                @click="onToggleHeading(5)"
                                            >
                                                Heading 5
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                 type="button" class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white"
                                                 @click="onToggleHeading(6)"
                                            >
                                                Heading 6
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </popover-panel>
                        </popover>
                        <div class="ps-1.5">
                            <span class="block w-px h-4 bg-gray-300 dark:bg-gray-600"></span>
                        </div>
                        <template
                            v-if="uploadImageUrl && folder"
                        >
                            <input type="file" @change="handleImageUpload" accept="image/*" class="hidden" ref="fileInput">
                            <button
                                type="button" data-tooltip-target="tooltip-image" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                                @click="onAddImage"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M13 10a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2H14a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12c0 .556-.227 1.06-.593 1.422A.999.999 0 0 1 20.5 20H4a2.002 2.002 0 0 1-2-2V6Zm6.892 12 3.833-5.356-3.99-4.322a1 1 0 0 0-1.549.097L4 12.879V6h16v9.95l-3.257-3.619a1 1 0 0 0-1.557.088L11.2 18H8.892Z" clip-rule="evenodd"/>
                                </svg>
                                <span class="sr-only">Add image</span>
                            </button>
                        </template>
                        <button
                            type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                            @click="onAddVideo"
                        >
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm-2 4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H9Zm0 2h2v2H9v-2Zm7.965-.557a1 1 0 0 0-1.692-.72l-1.268 1.218a1 1 0 0 0-.308.721v.733a1 1 0 0 0 .37.776l1.267 1.032a1 1 0 0 0 1.631-.776v-2.984Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="sr-only">Add video</span>
                        </button>
                        <button
                            type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                            @click="editor.chain().focus().toggleBulletList().run();"
                        >
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>
                            </svg>
                            <span class="sr-only">Toggle list</span>
                        </button>
                        <button
                            type="button" data-tooltip-target="tooltip-ordered-list" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                            @click="editor.chain().focus().toggleOrderedList().run()"
                        >
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h8m-8 6h8m-8 6h8M4 16a2 2 0 1 1 3.321 1.5L4 20h5M4 5l2-1v6m-2 0h4"/>
                            </svg>
                            <span class="sr-only">Toggle ordered list</span>
                        </button>
                        <button
                            type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                            @click="editor.chain().focus().toggleBlockquote().run()"
                        >
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M6 6a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a3 3 0 0 1-3 3H5a1 1 0 1 0 0 2h1a5 5 0 0 0 5-5V8a2 2 0 0 0-2-2H6Zm9 0a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a3 3 0 0 1-3 3h-1a1 1 0 1 0 0 2h1a5 5 0 0 0 5-5V8a2 2 0 0 0-2-2h-3Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="sr-only">Toggle blockquote</span>
                        </button>
                        <button
                            type="button" class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                            @click="editor.chain().focus().setHorizontalRule().run()"
                        >
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 12h14"/>
                                <path stroke="currentColor" stroke-linecap="round" d="M6 9.5h12m-12 9h12M6 7.5h12m-12 9h12M6 5.5h12m-12 9h12"/>
                            </svg>
                            <span class="sr-only">Toggle Horizontal Rule</span>
                        </button>
                    </div>
                </div>
                <div class="px-4 py-2 bg-white rounded-b-lg dark:bg-gray-800">
                    <editor-content
                        :editor="editor"
                        class="prose block w-full px-0 text-sm text-gray-800 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400"
                    />
                </div>
            </div>
        </div>
        <template v-if="hasNotesSlot">
            <slot name="notes" />
        </template>
        <template v-else>
            <form-field-note v-if="notes" :notes="notes">
                <template #notes />
            </form-field-note>
        </template>
        <form-field-error v-if="hasError" :errors="errors">
            <template #errors />
        </form-field-error>
    </div>
</template>

<script setup>
import { computed, inject, onMounted, ref, useSlots, watch } from "vue";
import FormFieldNote from "#/shared/components/form/form-field-note.vue";
import FormFieldError from "#/shared/components/form/form-field-error.vue";
import FormFieldLabel from "#/shared/components/form/form-field-label.vue";
import axios from "axios";
import { useEditor, EditorContent, markInputRule } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Highlight from "@tiptap/extension-highlight";
import Underline from "@tiptap/extension-underline";
import Link from "@tiptap/extension-link";
import TextAlign from "@tiptap/extension-text-align";
import YouTube from "@tiptap/extension-youtube";
import TextStyle from "@tiptap/extension-text-style";
import FontFamily from "@tiptap/extension-font-family";
import { Color } from "@tiptap/extension-color";
import Bold from "@tiptap/extension-bold";
import Image from "@tiptap/extension-image";
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';

const props = defineProps({
    modelValue: {
        required: false,
        type: [String, Number],
        default: "",
    },
    hideLabel: {
        required: false,
        type: Boolean,
        default: false,
    },
    label: {
        required: false,
        type: [String, Number],
        default: null,
    },
    name: {
        required: false,
        type: [String, Number],
        default: null,
    },
    placeholder: {
        required: false,
        type: [String, Number],
        default: null,
    },
    notes: {
        required: false,
        type: [String, Array],
        default: null,
    },
    errors: {
        required: false,
        type: [Array],
        default: () => null,
    },
    disabled: {
        required: false,
        type: Boolean,
        default: false,
    },
    readonly: {
        required: false,
        type: Boolean,
        default: false,
    },
    folder: {
        required: false,
        type: String,
        default: null,
    },
    uploadImageUrl: {
        required: false,
        type: String,
        default: "/editor_upload_image",
    },
});

const emit = defineEmits(["blur", "update:modelValue"]);

const $slots = useSlots();
const $helper = inject('$helper');

const componentReady = ref(false);
const fileInput = ref();
const colorInput = ref()
const hasError = computed(() => {
    return props.errors?.length > 0;
});
const hasNotesSlot = computed(() => {
    return !!$slots.notes;
});
const hasLabelSlot = computed(() => {
    return !!$slots.label;
});

const FontSizeTextStyle = TextStyle.extend({
    addAttributes() {
        return {
            fontSize: {
                default: null,
                parseHTML: (element) => element.style.fontSize,
                renderHTML: (attributes) => {
                    if (!attributes.fontSize) {
                        return {};
                    }
                    return { style: "font-size: " + attributes.fontSize };
                },
            },
        };
    },
});
const CustomBold = Bold.extend({
    renderHTML({ HTMLAttributes }) {
        const { style, ...rest } = HTMLAttributes;
        const newStyle = "font-weight: bold;" + (style ? " " + style : "");
        return ["span", { ...rest, style: newStyle.trim() }, 0];
    },
    addOptions() {
        return {
            ...this.parent?.(),
            HTMLAttributes: {},
        };
    },
});

const editor = useEditor({
    content: '',
    extensions: [
        StarterKit.configure({
            textStyle: false,
            bold: false,
            marks: {
                bold: false,
            },
        }),
        CustomBold,
        Color,
        FontSizeTextStyle,
        FontFamily,
        Highlight,
        Underline,
        Link.configure({
            openOnClick: false,
            autolink: true,
            defaultProtocol: 'https',
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        ...(props.uploadImageUrl && props.folder
            ? [
                Image.configure({
                    HTMLAttributes: { class: "max-w-full" },
                    allowBase64: false,
                }).extend({
                    addInputRules() {
                        return [
                            markInputRule({
                                find: /!\[(.+|:?)]\((\S+)(?:(?:\s+)["'](\S+)["'])?\)/,
                                type: this.type,
                                getAttributes: async (match) => {
                                    const [, alt, src] = match;
                                    const url = await handleImageUpload(src);
                                    return { src: url, alt };
                                },
                            }),
                        ];
                    },
                }),
            ]
            : []),
        YouTube,
    ],
    editorProps: {
        attributes: {
            class: 'format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none',
        },
    },
    onUpdate: ({ editor }) => {
        if (editor.getHTML() !== props.modelValue) {
            emit("update:modelValue", editor.getHTML())
        }
    },
});

const onToggleHighlight = () => {
    const isHighlighted = editor.value.isActive('highlight');
    // when using toggleHighlight(), judge if is is already highlighted.
    editor.value.chain().focus().toggleHighlight({
        color: isHighlighted ? undefined : '#ffc078' // if is already highlighted，unset the highlight color
    }).run();
};
const onToggleLink = () => {
    const url = window.prompt('Enter URL:', $helper.getCurrentDomain());
    editor.value.chain().focus().toggleLink({ href: url }).run();
};
const onAddImage = () => {
    fileInput.value.click();
};
// Handle image uploads
const handleImageUpload = async (event) => {
    if (!props.uploadImageUrl || !props.folder) return;

    if (event && event.target && event.target.files && event.target.files[0]) {
        const formData = new FormData();
        formData.append("image", event.target.files[0]);
        formData.append("folder", props.folder);

        try {
            const data = await axios.post(props.uploadImageUrl, formData);
            editor.value.chain().focus().setImage({ src: data.url }).run();
        } catch (error) {
            console.error("Upload failed:", error);
        }
    }
};
const onAddVideo = () => {
    const url = window.prompt('Enter YouTube URL:', 'https://www.youtube.com/watch?v=60ItHLz5WEA');
    if (url) {
        editor.value.commands.setYoutubeVideo({
            src: url,
            width: 640,
            height: 480,
        })
    }
};
const onToggleHeading = (level) => {
    editor.value.chain().focus().toggleHeading({ level: parseInt(level) }).run();
};
const onFontFamilyChange = (event) => {
    const fontFamily = event.target.getAttribute('data-font-family');

    if (fontFamily === 'default') {
        editor.value.chain().focus().unsetFontFamily().run();
    } else {
        editor.value.chain().focus().setFontFamily(fontFamily).run();
    }
};
const onSetColor = (event) => {
    const color = event.target.getAttribute('data-hex-color');

    editor.value.chain().focus().setColor(color).run();
};
const handleColorChange = (event) => {
    const selectedColor = event.target.value;
    editor.value.chain().focus().setColor(selectedColor).run();
};
const onChangeFontSize = (event) => {
    const fontSize = event.target.getAttribute('data-text-size');

    if (fontSize === 'default') {
        editor.value.chain().focus().unsetMark('textStyle', { fontSize: null }).run();
    } else {
        editor.value.chain().focus().setMark('textStyle', { fontSize }).run();
    }
};

watch(() => props.modelValue, (newValue) => {
    if (editor.value && newValue !== editor.value.getHTML()) {
        editor.value.commands.setContent(newValue)
    }
})
onMounted(async () => {
    if (editor.value && props.modelValue) {
        editor.value.commands.setContent(props.modelValue)
    }
    componentReady.value = true;
});
</script>
