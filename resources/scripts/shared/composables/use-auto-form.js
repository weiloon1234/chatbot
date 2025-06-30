import { reactive, ref, watch } from "vue";
import axios from "axios";
import $helper from "#/shared/utils/helper.js";

export default function useAutoForm(defaultData = {}, options = {}) {
    const { multipleFiles = [], multipleSelects = [] } = options;

    const formState = reactive({});
    const formData = new FormData();
    const formReady = ref(false);
    const formBusy = ref(false);
    const formError = ref({});

    const initializeForm = () => {
        // Unwrap the ref value
        const data = defaultData.value || {};

        // Clear existing state while preserving reactivity
        Object.keys(formState).forEach(key => delete formState[key]);

        // Initialize new state
        Object.keys(data).forEach((key) => {
            if (multipleSelects.includes(key) || multipleFiles.includes(key)) {
                formState[key] = Array.isArray(data[key]) ? [...data[key]] : [];
            } else {
                formState[key] = data[key] ?? "";
            }
        });

        setTimeout(() => {
            formReady.value = true;
        }, 500);
    };

    const fillData = (fieldName, value) => {
        if (multipleFiles.includes(fieldName)) {
            formData.delete(fieldName);
            if (Array.isArray(value)) {
                value.forEach((file) => formData.append(`${fieldName}[]`, file));
                formState[fieldName] = [...value];
            } else {
                formData.set(fieldName, value);
                formState[fieldName] = value;
            }
        } else if (multipleSelects.includes(fieldName)) {
            formData.delete(fieldName);
            if (Array.isArray(value)) {
                value.forEach((v) => formData.append(`${fieldName}[]`, v));
                formState[fieldName] = [...value];
            } else {
                formData.append(`${fieldName}[]`, value);
                formState[fieldName] = [value];
            }
        } else {
            formData.set(fieldName, value);
            formState[fieldName] = value;
        }
    };

    // Create FormData on demand
    const createFormData = () => {
        const formData = new FormData();

        Object.entries(formState).forEach(([key, value]) => {
            // Handle multiple files
            if (multipleFiles.includes(key)) {
                formData.delete(`${key}[]`);

                // Convert FileList to array
                const files = value instanceof FileList
                    ? Array.from(value)
                    : Array.isArray(value) ? value : [];

                files.forEach(file => {
                    if (file) formData.append(`${key}[]`, file);
                });
            }
            // Handle multi-select values
            else if (multipleSelects.includes(key)) {
                formData.delete(`${key}[]`);
                const values = Array.isArray(value) ? value : [value];
                values.forEach(val => {
                    if (val) formData.append(`${key}[]`, val);
                });
            }
            // Handle regular fields
            else {
                if (value !== null && value !== undefined) {
                    formData.set(key, value);
                }
            }
        });

        return formData;
    };

    const submitForm = async (url) => {
        try {
            formBusy.value = true;
            formError.value = {};

            const data = await axios.post(url, createFormData(), {
                headers: { "Content-Type": "multipart/form-data" },
            });

            formBusy.value = false;
            return data;
        } catch (error) {
            formError.value = await $helper.handleFormError(error);
            console.error("Submit Error:", formError.value);

            formBusy.value = false;

            throw error;
        }
    };

    watch(() => defaultData.value, initializeForm, { deep: true, immediate: true });

    watch(() => ({ ...formState }), (newValue, oldValue) => {
        Object.keys(newValue).forEach(key => {
            if (formError.value[key] !== 'undefined') {
                if (newValue[key] !== oldValue[key]) {
                    delete formError.value[key];
                }
            }
        });
    });

    if (!formReady.value) {
        initializeForm();
    }

    return {
        formReady,
        formState,
        formData,
        formBusy,
        formError,
        fillData,
        submitForm,
    };
}
