<template>
    <div
        class="form-input-container"
        :class="{
            'has-error': hasError,
            'disabled': disabled || readonly,
        }">
        <template v-if="hasLabelSlot">
            <slot name="label" />
        </template>
        <template v-else-if="!hideLabel && (label || name)">
            <form-field-label :label="label ?? name" />
        </template>
        <div class="form-input-wrapper file-input">
            <div v-if="isComponentReady" class="flex-1 flex justify-start">
                <input
                    :id="id"
                    ref="filePicker"
                    type="file"
                    :name="name"
                    :placeholder="placeholder"
                    :multiple="multiple"
                    class="w-full sm:text-sm border rounded-md p-2 text-black hidden"
                    :accept="accept"
                    @blur="emit('blur')"
                    @change="fileChanged" />
                <div class="form-control cursor-pointer" @click="triggerUpload">
                    {{ selectText }}
                </div>
            </div>
            <span v-if="selectedFile" class="absolute top-1/2 right-4 z-10 -translate-y-1/2 cursor-pointer" @click="clearFile">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                </svg>
            </span>
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
        <template v-if="isComponentReady && previewFileLists && previewFileLists.length">
            <carousel v-bind="carouselConfig">
                <slide v-for="(preview, index) in previewFileLists" :key="`slide-${name}-${index}`">
                    <div class="carousel__item w-full h-full flex flex-col justify-center items-center border border-gray-300 bg-gray-200" @click="handleAttachmentClick(preview)">
                        <template v-if="preview.is_image">
                            <img :src="preview.src" class="w-full cursor-pointer" />
                        </template>
                        <template v-else>
                            <svg class="w-12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13.5 3H12H8C6.34315 3 5 4.34315 5 6V18C5 19.6569 6.34315 21 8 21H12M13.5 3L19 8.625M13.5 3V7.625C13.5 8.17728 13.9477 8.625 14.5 8.625H19M19 8.625V11.8125"
                                    stroke="#000000"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M17.5 15V21M17.5 21L15 18.5M17.5 21L20 18.5" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </template>
                    </div>
                </slide>
                <template #addon>
                    <navigation />
                    <pagination />
                </template>
            </carousel>
        </template>
        <teleport
            to="body"
        >
            <vue-easy-lightbox :visible="lightBoxRef" :imgs="lightBoxGallery" :index="lightBoxIndex" @hide="onLightBoxHide" />
        </teleport>
    </div>
</template>

<script setup>
import { computed, inject, nextTick, ref, useSlots } from 'vue';
import FormFieldNote from '#/shared/components/form/form-field-note.vue';
import FormFieldError from '#/shared/components/form/form-field-error.vue';
import FormFieldLabel from '#/shared/components/form/form-field-label.vue';
import { Carousel, Navigation, Pagination, Slide } from 'vue3-carousel';
import { useI18n } from 'vue-i18n';
import VueEasyLightbox from 'vue-easy-lightbox';

const props = defineProps({
    id: {
        required: false,
        type: [String, Number],
        default: null,
    },
    modelValue: {
        required: false,
        type: [String, File, FileList, Array],
        default: '',
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
    multiple: {
        required: false,
        type: Boolean,
        default: false,
    },
    accept: {
        required: false,
        type: [String],
        default: '*/*',
    },
    previewFile: {
        required: false,
        type: [String, Array],
        default: null,
    },
    previewFileHeight: {
        required: false,
        type: [Number],
        default: 200,
    },
});

const emit = defineEmits(['blur', 'update:modelValue']);

const $slots = useSlots();
const $i18n = useI18n();
const $helper = inject('$helper');

const isComponentReady = ref(true);
const filePicker = ref();
const lightBoxRef = ref(false);
const lightBoxIndex = ref(0);
const carouselConfig = ref({
    height: props.previewFileHeight,
    itemsToShow: 2,
    gap: 5,
    wrapAround: true,
});

const hasError = computed(() => {
    return props.errors?.length > 0;
});
const hasNotesSlot = computed(() => {
    return !!$slots.notes;
});
const hasLabelSlot = computed(() => {
    return !!$slots.label;
});

const triggerUpload = () => {
    filePicker.value.click();
};

const selectedFile = computed(() => {
    if (!props.modelValue) return 0;
    if (typeof props.modelValue === 'string') return 1;
    if (props.modelValue instanceof File) return 1;
    if (props.modelValue instanceof FileList) return props.modelValue.length;
    if (typeof props.modelValue === 'object') {
        return Object.keys(props.modelValue).length;
    }
    return 0;
});

const fileChanged = async (event) => {
    try {
        const files = event.target.files;
        if (!files || files.length === 0) {
            emit("update:modelValue", "");
            return;
        }

        if (props.multiple) {
            emit("update:modelValue", files); // Convert FileList to an Array
        } else {
            emit("update:modelValue", files[0]); // Store single file
        }
    } catch (e) {
        console.error("File upload error:", e);
        emit("update:modelValue", "");
    } finally {
        await nextTick(() => {
            isComponentReady.value = false;
            nextTick(() => {
                isComponentReady.value = true; // Force reactivity
            });
        });
    }
};

const clearFile = async () => {
    emit("update:modelValue", "");

    if (filePicker.value) {
        filePicker.value.value = ""; // Reset input field
    }

    await nextTick(() => {
        isComponentReady.value = false;
        nextTick(() => {
            isComponentReady.value = true; // Force UI update
        });
    });
};

const selectText = computed(() => {
    if (props.modelValue) {
        if (props.multiple) {
            return String($i18n.t('Selected x file')).replace(/:x/i, String(selectedFile.value));
        } else if (props.modelValue.name) {
            return props.modelValue.name;
        }
    }

    if (props.placeholder) {
        return props.placeholder;
    } else {
        return $i18n.t('Click to select file');
    }
});

const previewFileLists = computed(() => {
    if (!isComponentReady.value) return [];

    const list = [];

    // Process external preview files (if any)
    if (props.previewFile) {
        (Array.isArray(props.previewFile) ? props.previewFile : [props.previewFile]).forEach((previewFile) => {
            list.push({
                src: previewFile,
                is_image: $helper.isImage(previewFile),
                file_name: previewFile,
            });
        });
    }

    // Process newly selected files
    if (Array.isArray(props.modelValue)) {
        list.push(...props.modelValue.map((file) => ({
            src: URL.createObjectURL(file),
            is_image: $helper.isImage(file.name),
            file_name: file.name,
        })));
    } else if (props.modelValue instanceof File) {
        list.push({
            src: URL.createObjectURL(props.modelValue),
            is_image: $helper.isImage(props.modelValue.name),
            file_name: props.modelValue.name,
        });
    } else if (props.modelValue instanceof FileList) {
        list.push(...Array.from(props.modelValue).map((file) => ({
            src: URL.createObjectURL(file),
            is_image: $helper.isImage(file.name),
            file_name: file.name,
        })));
    }

    return props.multiple ? list : list.slice(-1);
});

const lightBoxGallery = computed(() => {
    if (!previewFileLists.value.length) return null;
    return previewFileLists.value
        .filter((item) => {
            return item.is_image;
        })
        .map((item) => {
            return item.src;
        });
});

const handleAttachmentClick = (item) => {
    if (item.is_image) {
        launchLightBox(item);
    } else {
        const a = document.createElement('a');
        a.setAttribute('href', item.src);
        a.setAttribute('target', '_blank');
        a.setAttribute('download', item.file_name);
        a.click();
    }
};
const launchLightBox = (item) => {
    if (!lightBoxGallery.value) return;

    const idx = lightBoxGallery.value.findIndex((g) => {
        return g === item.src;
    });

    if (idx !== -1) {
        lightBoxIndex.value = idx;
    } else {
        lightBoxIndex.value = 0;
    }

    lightBoxRef.value = true;
};

const onLightBoxHide = () => {
    lightBoxIndex.value = 0;
    lightBoxRef.value = false;
};
</script>
