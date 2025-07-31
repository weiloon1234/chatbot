<template>
    <div>
        <admin-content-card>
            <!-- Session Status: Waiting for Scan -->
            <div v-if="sessionStatus === 'waiting_for_scan'" class="text-center space-y-4">
                <div class="font-semibold text-lg">
                    {{ $t('Scan QR Code with WhatsApp') }}
                </div>
                <div class="text-gray-600 text-sm max-w-md mx-auto">
                    {{ $t('Open WhatsApp on your phone → Settings → Linked Devices → Link a Device → Scan this QR code') }}
                </div>
                <div v-if="qrCodeData" class="flex items-center justify-center">
                    <img :src="qrCodeData" alt="QR Code" class="w-64 h-64 border p-2 rounded-lg" />
                </div>
                <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>{{ $t('Waiting for scan...') }} ({{ pollingCountdown }}s)</span>
                </div>
                <auto-button
                    size="sm"
                    type="secondary"
                    @click="refreshQR"
                    :disabled="refreshingQR"
                >
                    <font-awesome-icon icon="fas fa-refresh" :class="{ 'animate-spin': refreshingQR }" />
                    {{ $t('Refresh QR') }}
                </auto-button>
            </div>

            <!-- Session Status: Disconnected -->
            <div v-else-if="sessionStatus === 'disconnected'" class="text-center space-y-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                    <font-awesome-icon icon="fas fa-unlink" class="text-red-500 text-2xl" />
                </div>
                <div>
                    <div class="font-semibold text-lg text-red-600">{{ $t('Session Disconnected') }}</div>
                    <div class="text-gray-600 text-sm">{{ $t('WhatsApp session was disconnected. Please reconnect.') }}</div>
                </div>
                <div class="flex space-x-2 justify-center">
                    <auto-button
                        type="primary"
                        @click="reconnectSession"
                        :disabled="refreshingSession"
                    >
                        <font-awesome-icon icon="fas fa-sync" :class="{ 'animate-spin': refreshingSession }" />
                        {{ $t('Reconnect') }}
                    </auto-button>
                    <auto-button
                        type="secondary"
                        @click="refreshSession"
                        :disabled="refreshingSession"
                    >
                        <font-awesome-icon icon="fas fa-refresh" />
                        {{ $t('Refresh') }}
                    </auto-button>
                    <auto-button
                        type="warning"
                        @click="clearSession"
                        :disabled="refreshingSession"
                    >
                        <font-awesome-icon icon="fas fa-trash" />
                        {{ $t('Clear Session') }}
                    </auto-button>
                </div>
            </div>

            <!-- Session Status: Connected -->
            <div v-else-if="sessionStatus === 'connected'" class="text-center space-y-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                    <font-awesome-icon icon="fas fa-check-circle" class="text-green-500 text-2xl" />
                </div>
                <div>
                    <div class="font-semibold text-lg text-green-600">{{ $t('Session Connected') }}</div>
                    <div class="text-gray-600 text-sm">{{ $t('Your WhatsApp session is active.') }}</div>
                </div>
                <div class="flex space-x-2 justify-center">
                    <auto-button
                        type="warning"
                        @click="clearSession"
                        :disabled="refreshingSession"
                    >
                        <font-awesome-icon icon="fas fa-trash" />
                        {{ $t('Clear Session') }}
                    </auto-button>
                </div>

                <hr class="my-4" />

                <!-- Message Sending Form -->
                <div class="text-left space-y-4">
                    <div class="font-semibold text-lg">{{ $t('Send Message') }}</div>
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
                        {{ $t('Send') }}
                    </auto-button>
                </div>
            </div>

            <!-- Session Status: Connecting/Loading -->
            <div v-else class="text-center space-y-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                    <font-awesome-icon icon="fas fa-spinner" class="text-blue-500 text-2xl animate-spin" />
                </div>
                <div>
                    <div class="font-semibold text-lg text-blue-600">{{ $t('Loading Session Status...') }}</div>
                    <div class="text-gray-600 text-sm">{{ $t('Please wait while we check your WhatsApp session.') }}</div>
                </div>
            </div>
        </admin-content-card>
    </div>
</template>

<script setup>
import { inject, onMounted, onBeforeUnmount, ref, watch } from "vue";
import axios from "axios";
import AdminContentCard from "#/admin/components/admin-content-card.vue";
import AutoButton from "#/shared/components/button/auto-button.vue";
import FormContact from "#/shared/components/form/form-contact.vue";
import FormTextarea from "#/shared/components/form/form-textarea.vue";
import FormFile from "#/shared/components/form/form-file.vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import useAutoForm from "#/shared/composables/use-auto-form.js";
import { useI18n } from "vue-i18n";

const $helper = inject('$helper');
const { t } = useI18n();

// Reactive states
const qrCodeData = ref(null);
const sessionStatus = ref('loading'); // 'loading', 'disconnected', 'waiting_for_scan', 'connected'
const pollingCountdown = ref(60);
const pollingInterval = ref(null);
const refreshingQR = ref(false);
const refreshingSession = ref(false);

// Form management with enhanced default values
const { formState, formBusy, formError, submitForm, fillData } = useAutoForm({
    contact_country_id: null,
    contact_number: '',
    content: '', // Used for text message content or media caption
    files: [],
}, {
    multipleFiles: ['files']
});

// --- Session Management Functions ---

const startPollingCountdown = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
    }
    pollingCountdown.value = 60;
    pollingInterval.value = setInterval(() => {
        if (pollingCountdown.value > 0) {
            pollingCountdown.value--;
        } else {
            clearInterval(pollingInterval.value);
            // Automatically refresh QR if still waiting for scan
            if (sessionStatus.value === 'waiting_for_scan') {
                // refreshQR();
            }
        }
    }, 1000);
};

const checkLoginStatus = async () => {
    refreshingSession.value = true;
    try {
        // Call Laravel endpoint which in turn calls microservice POST /sessions/start
        const data = await axios.post('/whatsapp/sessions/status');

        if (data.status === 'success') {
            sessionStatus.value = data.sessionStatus; // 'connected', 'disconnected', 'waiting_for_scan'
            qrCodeData.value = data.qr; // QR code data if available

            if (sessionStatus.value === 'waiting_for_scan') {
                // If we are waiting for scan but don't have a QR, request one
                if (!qrCodeData.value) {
                    console.log("Session waiting for scan, but no QR data. Requesting new QR...");
                    await startSession(); // This will get the QR and update status
                } else {
                    startPollingCountdown();
                }
            } else {
                if (pollingInterval.value) {
                    clearInterval(pollingInterval.value);
                }
            }
        } else {
            // Handle error from Laravel controller
            sessionStatus.value = 'disconnected';
            qrCodeData.value = null;
            if (pollingInterval.value) {
                clearInterval(pollingInterval.value);
            }
            await $helper.alertError({ message: `${t('Failed to check session status.')} ${data.message}` });
        }
    } catch (err) {
        console.error("Error checking WhatsApp status", err);
        sessionStatus.value = 'disconnected';
        qrCodeData.value = null;
        if (pollingInterval.value) {
            clearInterval(pollingInterval.value);
        }
        await $helper.alertError({ message: `${t('Network Error')} ${t('Could not connect to the WhatsApp microservice via Laravel.')}` });
    } finally {
        refreshingSession.value = false;
    }
};

const startSession = async () => {
    refreshingQR.value = true;
    refreshingSession.value = true;
    try {
        // Call Laravel endpoint which in turn calls microservice POST /sessions/start
        const data = await axios.post('/whatsapp/sessions/start');

        if (data.status === 'success') {
            if (data.qr) {
                qrCodeData.value = data.qr;
                sessionStatus.value = 'waiting_for_scan';
                startPollingCountdown();
                await $helper.alertSuccess({ message: `${t('QR Code Generated')} ${t('Please scan the QR code to connect.')}` });
            } else {
                sessionStatus.value = 'connected';
                qrCodeData.value = null;
                if (pollingInterval.value) {
                    clearInterval(pollingInterval.value);
                }
                await $helper.alertSuccess({ message: `${t('Session Connected')} ${t('Your WhatsApp session is now active.')}` });
            }
        } else {
            // Handle error from Laravel controller
            sessionStatus.value = 'disconnected';
            qrCodeData.value = null;
            if (pollingInterval.value) {
                clearInterval(pollingInterval.value);
            }
            await $helper.alertError({ message: `${t('Failed to Start Session')} ${data.message}` });
        }
    } catch (err) {
        console.error("Error starting session", err);
        sessionStatus.value = 'disconnected';
        qrCodeData.value = null;
        if (pollingInterval.value) {
            clearInterval(pollingInterval.value);
        }
        await $helper.alertError({ message: `${t('Network Error')} ${t('Could not connect to the WhatsApp microservice via Laravel.')}` });
    } finally {
        refreshingQR.value = false;
        refreshingSession.value = false;
    }
};

const refreshQR = () => {
    startSession(); // Re-uses startSession to get a new QR
};

const reconnectSession = () => {
    startSession(); // Re-uses startSession to attempt reconnection
};

const refreshSession = () => {
    checkLoginStatus(); // Re-checks the current status
};

const clearSession = async () => {
    refreshingSession.value = true;
    try {
        // Call Laravel endpoint which in turn calls microservice POST /sessions/terminate
        const data = await axios.post('/whatsapp/sessions/terminate');

        if (data.status === 'success') {
            sessionStatus.value = 'disconnected';
            qrCodeData.value = null;
            if (pollingInterval.value) {
                clearInterval(pollingInterval.value);
            }
            await $helper.alertSuccess({ message: `${t('Session Cleared')} ${t('WhatsApp session has been terminated.')}` });
        } else {
            await $helper.alertError({ message: `${t('Failed to Clear Session')} ${data.message}` });
        }
    } catch (err) {
        console.error("Error terminating session", err);
        await $helper.alertError({ message: `${t('Network Error')} ${t('Could not connect to the WhatsApp microservice via Laravel.')}` });
    } finally {
        refreshingSession.value = false;
    }
};

// --- Message Sending Function ---

const sendMessage = async () => {
    if (formBusy.value) return;
    try {
        const result = await submitForm('/whatsapp/messages/send');

        if (result.status === 'success') {
            fillData('content', '');
            fillData('files', undefined);
            fillData('files[]', undefined);
            await $helper.alertSuccess({
                message: t('Message sent successfully'),
            });
        } else {
            await $helper.alertError({ message: `${t('Failed to Send Message')} ${data.message}` });
        }
    } catch (err) {
        console.error("Error sending message", err);
        await $helper.alertError({
            message: t('Failed to send message'),
        });
    }
};

// --- Lifecycle Hooks ---

onMounted(() => {
    checkLoginStatus(); // Check status on component mount
});

onBeforeUnmount(() => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
    }
});

// Watch for changes in sessionStatus to manage polling
watch(sessionStatus, (newStatus) => {
    if (newStatus === 'waiting_for_scan') {
        startPollingCountdown();
    } else {
        if (pollingInterval.value) {
            clearInterval(pollingInterval.value);
        }
    }
});
</script>

<style scoped>
/* Add any component-specific styles here if needed */
</style>
