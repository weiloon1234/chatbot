<template>
    <Dialog :open="true" class="modal relative z-9999" @close="onClose">
        <div class="fixed inset-0 bg-black/25" aria-hidden="true" />

        <div class="fixed inset-0">
            <transition-root
                appear
                :show="isShowing"
                as="template"
                enter="transform transition duration-[400ms]"
                enter-from="opacity-0 rotate-[-120deg] scale-50"
                enter-to="opacity-100 rotate-0 scale-100"
                leave="transform duration-200 transition ease-in-out"
                leave-from="opacity-100 rotate-0 scale-100 "
                leave-to="opacity-0 scale-95 ">
                <div class="flex min-h-full items-center justify-center p-4 text-center z-10">
                    <dialog-panel
                        class="z-20 w-full transform overflow-hidden bg-white text-left align-middle transition-all"
                        :class="[
                            small ? 'lg:max-w-[40dvw] 2xl:max-w-[30dvw] rounded-lg p-3 shadow-md' : 'lg:max-w-[70dvw] 2xl:max-w-[50dvw] rounded-2xl p-4 shadow-xl',
                        ]"
                    >
                        <dialog-title>
                            <div class="flex justify-between space-x-2 items-center">
                                <slot name="header" />
                                <template v-if="!$slots.header">
                                    <h3 class="text-title font-semibold capitalize leading-6 text-gray-900">
                                        {{ title }}
                                    </h3>
                                </template>
                                <div class="cursor-pointer p-2" @click="onClose">
                                    <font-awesome-icon icon="fas fa-close" />
                                </div>
                            </div>
                        </dialog-title>
                        <dialog-description class="py-2 overflow-y-scroll px-0.5" :style="[`max-height: ${computedMaxHeight}px`]">
                            <slot />
                        </dialog-description>
                        <div v-if="$slots.footer" class="mt-4">
                            <slot name="footer" />
                        </div>
                        <div
                            v-else
                            class="mt-4"
                            v-bind="footerAttr"
                        />
                    </dialog-panel>
                </div>
            </transition-root>
        </div>
    </Dialog>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, useSlots } from 'vue';
import { Dialog, DialogPanel, DialogTitle, DialogDescription, TransitionRoot } from '@headlessui/vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import EventBus, { EVENT } from '#/event-bus.js';
import MobileDetect from 'mobile-detect';

const props = defineProps({
    id: {
        required: false,
        type: String,
        default: null,
    },
    title: {
        required: false,
        type: [String, Number],
        default: null,
    },
    small: {
        required: false,
        type: Boolean,
        default: false,
    },
    maxHeight: {
        required: false,
        type: Number,
        default: 600,
    },
});
const emit = defineEmits(['close']);

const $slots = useSlots();

const md = new MobileDetect(navigator.userAgent);
const isShowing = ref(true);
const computedMaxHeight = computed(() => {
    if (md.mobile()) {
        return Math.min(props.maxHeight, window.innerHeight * 0.65);
    } else {
        return window.innerHeight * 0.70;
    }
});

const onClose = async () => {
    isShowing.value = false;
    await nextTick(() => {
        emit('close');
    });
};

const footerAttr = computed(() => {
    if (props.id) {
        return {
            id: `modal-footer-${props.id}`
        }
    } else {
        return {
            id: 'modal-footer',
        }
    }
});

onMounted(async () => {
    await nextTick(async () => {
        EventBus.emit(EVENT.MODAL_OPEN);
    });
});

onBeforeUnmount(async () => {
    EventBus.emit(EVENT.MODAL_CLOSE);
});
</script>
