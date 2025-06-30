import FormCheckbox from '#/shared/components/form/form-contact.vue';
import FormContact from '#/shared/components/form/form-contact.vue';
import FormDatePicker from '#/shared/components/form/form-date-picker.vue';
import FormDateTimePicker from '#/shared/components/form/form-date-time-picker.vue';
import FormFile from '#/shared/components/form/form-file.vue';
import FormInput from '#/shared/components/form/form-input.vue';
import FormPassword from '#/shared/components/form/form-password.vue';
import FormPin from '#/shared/components/form/form-pin.vue';
import FormSelect from '#/shared/components/form/form-select.vue';
import FormTextarea from "#/shared/components/form/form-textarea.vue";
import FormEditor from "#/shared/components/form/form-editor.vue";

// Export a function that provides access to the component map
export function useFormComponents() {
    // Store the static component mapping
    const formComponents = {
        "form-checkbox": FormCheckbox,
        "form-contact": FormContact,
        "form-date-picker": FormDatePicker,
        "form-date-time-picker": FormDateTimePicker,
        "form-file": FormFile,
        "form-password": FormPassword,
        "form-pin": FormPin,
        "form-select": FormSelect,
        "form-textarea": FormTextarea,
        "form-input": FormInput,
        "form-text": FormInput,
        "form-number": FormInput,
        "form-tel": FormInput,
        "form-email": FormInput,
        "form-editor": FormEditor,
    };

    return { formComponents };
}
