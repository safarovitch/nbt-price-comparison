<script setup lang="ts">
import AlertError from '@/components/AlertError.vue';
import InputError from '@/components/InputError.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { confirm } from '@/routes/two-factor';
import { Form } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { Check, Copy, ScanLine } from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, useTemplateRef, watch } from 'vue';

interface Props {
    requiresConfirmation: boolean;
    twoFactorEnabled: boolean;
}

const props = defineProps<Props>();
const isOpen = defineModel<boolean>('isOpen');

const { copy, copied } = useClipboard();
const { qrCodeSvg, manualSetupKey, clearSetupData, fetchSetupData, errors } =
    useTwoFactorAuth();

const showVerificationStep = ref(false);
const code = ref<string>('');
const codeValue = computed<string>(() => code.value);

const pinInputContainerRef = useTemplateRef('pinInputContainerRef');
const modalId = 'twoFactorSetupModal';
const modalElement = ref<HTMLElement | null>(null);

onMounted(() => {
    modalElement.value = document.getElementById(modalId);
    if (modalElement.value) {
        modalElement.value.addEventListener('hidden.bs.modal', () => {
            isOpen.value = false;
        });
    }
});

watch(
    () => isOpen.value,
    (newValue) => {
        if (modalElement.value && window.bootstrap) {
            const modal = new window.bootstrap.Modal(modalElement.value);
            if (newValue) {
                modal.show();
            } else {
                modal.hide();
            }
        }
    },
);

const modalConfig = computed<{
    title: string;
    description: string;
    buttonText: string;
}>(() => {
    if (props.twoFactorEnabled) {
        return {
            title: 'Two-Factor Authentication Enabled',
            description:
                'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
            buttonText: 'Close',
        };
    }

    if (showVerificationStep.value) {
        return {
            title: 'Verify Authentication Code',
            description: 'Enter the 6-digit code from your authenticator app',
            buttonText: 'Continue',
        };
    }

    return {
        title: 'Enable Two-Factor Authentication',
        description:
            'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
        buttonText: 'Continue',
    };
});

const handleModalNextStep = () => {
    if (props.requiresConfirmation) {
        showVerificationStep.value = true;

        nextTick(() => {
            pinInputContainerRef.value?.querySelector('input')?.focus();
        });

        return;
    }

    clearSetupData();
    isOpen.value = false;
};

const resetModalState = () => {
    if (props.twoFactorEnabled) {
        clearSetupData();
    }

    showVerificationStep.value = false;
    code.value = '';
};

watch(
    () => isOpen.value,
    async (isOpenValue) => {
        if (!isOpenValue) {
            resetModalState();
            return;
        }

        if (!qrCodeSvg.value) {
            await fetchSetupData();
        }
    },
);
</script>

<template>
    <!-- Bootstrap Modal -->
    <div
        :id="modalId"
        class="modal fade"
        tabindex="-1"
        aria-labelledby="twoFactorModalLabel"
        aria-hidden="true"
        ref="modalElement"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-column align-items-center w-100">
                        <div
                            class="mb-3 rounded-circle border p-2 shadow-sm"
                            style="width: fit-content;"
                        >
                            <div
                                class="position-relative overflow-hidden rounded-circle border p-3 bg-light"
                            >
                                <ScanLine
                                    class="position-relative"
                                    style="width: 1.5rem; height: 1.5rem; z-index: 20;"
                                />
                            </div>
                        </div>
                        <h5 class="modal-title text-center" id="twoFactorModalLabel">
                            {{ modalConfig.title }}
                        </h5>
                        <p class="text-muted text-center small mb-0 mt-2">
                            {{ modalConfig.description }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        @click="resetModalState"
                    ></button>
                </div>

                <div class="modal-body">
                    <template v-if="!showVerificationStep">
                        <AlertError v-if="errors?.length" :errors="errors" />
                        <template v-else>
                            <div class="d-flex justify-content-center mb-4">
                                <div
                                    class="position-relative"
                                    style="width: 16rem; aspect-ratio: 1;"
                                >
                                    <div
                                        v-if="!qrCodeSvg"
                                        class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white"
                                        style="z-index: 10;"
                                    >
                                        <div
                                            class="spinner-border spinner-border-sm"
                                            role="status"
                                        >
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="position-relative border p-3"
                                        style="z-index: 10;"
                                    >
                                        <div
                                            v-html="qrCodeSvg"
                                            class="d-flex align-items-center justify-content-center w-100 h-100"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex w-100 mb-4">
                                <button
                                    type="button"
                                    class="btn btn-primary w-100"
                                    @click="handleModalNextStep"
                                >
                                    {{ modalConfig.buttonText }}
                                </button>
                            </div>

                            <div class="position-relative d-flex align-items-center justify-content-center mb-4">
                                <hr class="position-absolute w-100 m-0" style="top: 50%;" />
                                <span class="position-relative bg-white px-2 py-1 small">
                                    or, enter the code manually
                                </span>
                            </div>

                            <div class="d-flex w-100 align-items-center gap-2">
                                <div class="d-flex w-100 align-items-stretch overflow-hidden rounded border">
                                    <div
                                        v-if="!manualSetupKey"
                                        class="d-flex w-100 h-100 align-items-center justify-content-center bg-light p-3"
                                    >
                                        <div
                                            class="spinner-border spinner-border-sm"
                                            role="status"
                                        >
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <template v-else>
                                        <input
                                            type="text"
                                            readonly
                                            :value="manualSetupKey"
                                            class="form-control border-0"
                                        />
                                        <button
                                            type="button"
                                            @click="copy(manualSetupKey || '')"
                                            class="btn btn-outline-secondary border-start rounded-0"
                                        >
                                            <Check
                                                v-if="copied"
                                                style="width: 1rem; height: 1rem; color: green;"
                                            />
                                            <Copy v-else style="width: 1rem; height: 1rem;" />
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </template>

                    <template v-else>
                        <Form
                            v-bind="confirm.form()"
                            reset-on-error
                            @finish="code = ''"
                            @success="isOpen = false"
                            v-slot="{ errors, processing }"
                        >
                            <input type="hidden" name="code" :value="codeValue" />
                            <div
                                ref="pinInputContainerRef"
                                class="position-relative w-100 d-flex flex-column gap-3"
                            >
                                <div
                                    class="d-flex w-100 flex-column align-items-center justify-content-center gap-3 py-2"
                                >
                                    <input
                                        id="otp"
                                        type="text"
                                        class="form-control text-center"
                                        placeholder="000000"
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        v-model="code"
                                        :disabled="processing"
                                        autofocus
                                        style="letter-spacing: 0.5em; font-size: 1.5rem; width: 200px;"
                                    />
                                    <InputError
                                        :message="
                                            errors?.confirmTwoFactorAuthentication
                                                ?.code
                                        "
                                    />
                                </div>

                                <div class="d-flex w-100 align-items-center gap-3">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary flex-fill"
                                        @click="showVerificationStep = false"
                                        :disabled="processing"
                                    >
                                        Back
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary flex-fill"
                                        :disabled="
                                            processing || codeValue.length < 6
                                        "
                                    >
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </Form>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
