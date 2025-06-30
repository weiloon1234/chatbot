<template>
    <div>
        <admin-content-card>
            <div class="p-4">
                <template v-if="whatsappData">
                    <!-- Show QR if not logged in yet -->
                    <div v-if="whatsappData.qr" class="flex flex-col space-y-4 justify-center items-center">
                        <img :src="whatsappData.qr" alt="QR Code" class="w-fit h-auto aspect-square" />
                        <div v-if="qrExpired" class="text-red-500 text-sm">
                            {{ $t('QR expired, regenerating...') }}
                        </div>
                        <div class="font-semibold">
                            {{ $t('Use your WhatsApp linked device page Scanner to scan this QrCode') }}
                        </div>
                    </div>

                    <!-- Logged in -->
                    <template v-else-if="whatsappData.api_success && whatsappData.is_active">
                        <div class="flex justify-between space-x-4 items-center">
                            <div class="font-semibold">{{ $t('Login successfully') }}!</div>
                            <auto-button
                                type="danger"
                                :full="false"
                                size="sm"
                                @click="terminateSession"
                            >
                                <font-awesome-icon icon="fas fa-trash" />
                            </auto-button>
                        </div>

                        <div class="py-2 flex flex-col space-y-4">
                            <form-contact
                                v-model:select-value="formState.contact_country_id"
                                v-model:input-value="formState.contact_number"
                                input-name="contact_number"
                                select-name="contact_country_id"
                                :label="$t('Contact number')"
                                :errors="formError"
                            />
                            <form-textarea
                                v-model="formState.content"
                                :label="$t('Message')"
                                name="content"
                                rows="10"
                                :errors="formError?.content"
                            />
                            <form-file
                                v-model="formState.files"
                                :label="$t('Attachments')"
                                name="files"
                                :accept="'image/*, video/*, audio/*'"
                                :max-files="5"
                                :errors="formError?.files"
                                :multiple="true"
                            />
                            <auto-button
                                :disabled="formBusy"
                                :busy="formBusy"
                                class="capitalize"
                                @click="sendMessage"
                            >
                                {{ $t('Send message') }}
                            </auto-button>
                        </div>
                    </template>

                    <!-- Error state -->
                    <template v-else>
                        <div class="text-red-500 font-semibold">
                            {{ $t('System error, please try again later') }}
                        </div>
                    </template>
                </template>

                <!-- Initial loading -->
                <template v-else>
                    {{ $t('Loading') }}...
                </template>
            </div>

            <template v-if="pageReady">
                <auto-button
                    v-if="!whatsappData.api_success || serverError"
                    :disabled="formBusy"
                    :busy="formBusy"
                    @click="fetchWhatsappData"
                >
                    Re-Check
                </auto-button>
            </template>
        </admin-content-card>
    </div>
</template>

<script setup>
import { inject, onMounted, onBeforeUnmount, ref } from "vue";
import axios from "axios";
import AdminContentCard from "#/admin/components/admin-content-card.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";
import FormContact from "#/shared/components/form/form-contact.vue";
import FormTextarea from "#/shared/components/form/form-textarea.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { useI18n } from "vue-i18n";
import FormFile from "#/shared/components/form/form-file.vue";

const $helper = inject('$helper');
const { t } = useI18n();

const whatsappData = ref(null);
const pageReady = ref(false);
const serverError = ref(false);
const { formState, formBusy, formError, submitForm, fillData } = useAutoForm({}, {
    multipleFiles: ['files']
});

let pollingInterval = null;
let pollingRetryCount = 0;
const maxPollingRetries = 10; // 10 × 3s = ~30s
const qrExpired = ref(false);

const fetchWhatsappData = async () => {
    try {
        if (serverError.value) serverError.value = false;

        const data = await axios.post('/etc/check_whatsapp');
        pageReady.value = true;

        if (data.api_success) {
            whatsappData.value = {
                api_success: true,
                is_active: data.is_active,
                qr: data.qr || null
            };
        } else {
            whatsappData.value = {
                api_success: false,
                error: data.api_message || 'Unknown error'
            };
        }

        handleQrPolling();
    } catch (err) {
        console.error("Error fetching WhatsApp data", err);
        serverError.value = true;
    }
};

const checkLoginStatus = async () => {
    try {
        const { data } = await axios.post('/etc/check_whatsapp_status');
        const isLoggedIn = data === true || data?.data === true;

        if (isLoggedIn) {
            whatsappData.value = {
                api_success: true,
                is_active: true,
                qr: null
            };
            resetPolling(30000); // Logged in → slow polling
        }

        return isLoggedIn;
    } catch (err) {
        console.error("Error checking WhatsApp status", err);
        return false;
    }
};

const terminateSession = async () => {
    try {
        const data = await axios.post('/etc/disconnect_whatsapp');
        if (data.api_success) {
            await $helper.alertSuccess({
                message: t('Operation success'),
                callback: () => location.reload()
            });
        } else {
            console.error("Failed to terminate WhatsApp session");
        }
    } catch (err) {
        console.error("Error terminating WhatsApp session", err);
    }
};

const sendMessage = async () => {
    if (formBusy.value) return;
    try {
        const result = await submitForm('/etc/send_message_whatsapp');

        if (!result?.api_success) {
            throw new Error(result?.api_message || 'Failed to send');
        }

        fillData('content', '');
        fillData('files', undefined);
        fillData('files[]', undefined);
        await $helper.alertSuccess({
            message: t('Message sent successfully'),
        });
    } catch (err) {
        console.error("Error sending message", err);
        await $helper.alertError({
            message: t('Failed to send message'),
        });
    }
};

const handleQrPolling = () => {
    resetPolling();
    pollingRetryCount = 0;

    if (whatsappData.value?.qr) {
        // QR is shown → not logged in → check every 3s
        resetPolling(3000);
    } else if (whatsappData.value?.is_active) {
        // Already logged in → slow down polling
        resetPolling(30000);
    }
};

const resetPolling = (intervalMs = null) => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }

    if (intervalMs) {
        pollingInterval = setInterval(async () => {
            pollingRetryCount++;

            const status = await checkLoginStatus();

            // If not yet authenticated, and retry limit reached, refresh QR
            if (
                !status &&  // still pending
                pollingRetryCount >= maxPollingRetries
            ) {
                qrExpired.value = true;

                // Regenerate QR after short pause
                setTimeout(async () => {
                    await fetchWhatsappData(); // Get new QR
                    pollingRetryCount = 0;     // Reset retry count
                    qrExpired.value = false;
                }, 1000);
            }

        }, intervalMs);
    }
};

const stopPolling = () => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
};

onMounted(() => {
    fetchWhatsappData();
});

onBeforeUnmount(() => {
    stopPolling();
});
</script>
