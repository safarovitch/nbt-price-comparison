<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { getTranslation } from '@/utils/translations';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

interface Organization {
    uuid: string;
    name: Record<string, string>;
}

interface Product {
    uuid: string;
    name: Record<string, string>;
    type: string;
    currency_code?: number;
    interest_rate_min?: number | null;
    interest_rate_max?: number | null;
    term_months_min?: number | null;
    term_months_max?: number | null;
    min_amount?: number | null;
    max_amount?: number | null;
    purpose?: string | null;
    organization: Organization;
}

interface Props {
    product: Product;
    initialAmount?: number;
    initialTerm?: number;
}

const props = withDefaults(defineProps<Props>(), {
    initialAmount: 0, // 0 means use product default
    initialTerm: 0,
});

const emit = defineEmits<{
    close: [];
    success: [application: any];
}>();

// Product limits (with sensible defaults)
const minAmount = computed(() => props.product.min_amount ?? 1000);
const maxAmount = computed(() => props.product.max_amount ?? 1000000);
const minTerm = computed(() => props.product.term_months_min ?? 1);
const maxTerm = computed(() => props.product.term_months_max ?? 60);

// Dynamic loan parameters
const loanAmount = ref(props.initialAmount || minAmount.value);
const loanTerm = ref(props.initialTerm || minTerm.value);

// Ensure values are within bounds when product limits change
watch([minAmount, maxAmount], () => {
    if (loanAmount.value < minAmount.value) loanAmount.value = minAmount.value;
    if (loanAmount.value > maxAmount.value) loanAmount.value = maxAmount.value;
});

watch([minTerm, maxTerm], () => {
    if (loanTerm.value < minTerm.value) loanTerm.value = minTerm.value;
    if (loanTerm.value > maxTerm.value) loanTerm.value = maxTerm.value;
});

// Validate manual inputs
const validateLoanAmount = () => {
    if (loanAmount.value < minAmount.value) loanAmount.value = minAmount.value;
    if (loanAmount.value > maxAmount.value) loanAmount.value = maxAmount.value;
};

const validateLoanTerm = () => {
    if (loanTerm.value < minTerm.value) loanTerm.value = minTerm.value;
    if (loanTerm.value > maxTerm.value) loanTerm.value = maxTerm.value;
};

// Calculate monthly payment estimate
const monthlyPayment = computed(() => {
    const rate = (props.product.interest_rate_min ?? 15) / 100 / 12;
    const n = loanTerm.value;
    const p = loanAmount.value;
    if (rate === 0) return Math.round(p / n);
    return Math.round((p * rate * Math.pow(1 + rate, n)) / (Math.pow(1 + rate, n) - 1));
});

// Form state
const firstName = ref('');
const lastName = ref('');
const phone = ref('');
const otp = ref('');

// UI state
const step = ref<'form' | 'otp' | 'success'>('form');
const isLoading = ref(false);
const isSendingOtp = ref(false);
const isVerifyingOtp = ref(false);
const phoneVerified = ref(false);
const error = ref<string | null>(null);
const successMessage = ref<string | null>(null);

// OTP state
const otpSent = ref(false);
const otpResendCountdown = ref(0);
const devOtp = ref<string | null>(null);
let countdownInterval: ReturnType<typeof setInterval> | null = null;

// Validation
const isPhoneValid = computed(() => {
    const cleaned = phone.value.replace(/\D/g, '');
    return cleaned.length >= 9 && cleaned.length <= 15;
});

const isFormValid = computed(() => {
    return firstName.value.trim().length > 0 &&
        lastName.value.trim().length > 0 &&
        isPhoneValid.value &&
        phoneVerified.value;
});

const canSendOtp = computed(() => {
    return isPhoneValid.value && !isSendingOtp.value && otpResendCountdown.value === 0;
});

// CSRF token
const getCsrfToken = () => {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    return tokenMeta ? tokenMeta.getAttribute('content') : '';
};

// Send OTP
const sendOtp = async () => {
    if (!canSendOtp.value) return;

    isSendingOtp.value = true;
    error.value = null;
    devOtp.value = null;

    try {
        const response = await fetch('/api/otp/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() || '',
            },
            body: JSON.stringify({ phone: phone.value }),
        });

        const data = await response.json();

        if (data.success) {
            otpSent.value = true;
            step.value = 'otp';

            // Show dev OTP in development
            if (data.otp_dev) {
                devOtp.value = data.otp_dev;
            }

            // Start countdown for resend
            startResendCountdown();
        } else {
            error.value = data.message || 'Failed to send OTP';
        }
    } catch (e) {
        error.value = 'Network error. Please try again.';
    } finally {
        isSendingOtp.value = false;
    }
};

// Start resend countdown
const startResendCountdown = () => {
    otpResendCountdown.value = 60;
    if (countdownInterval) clearInterval(countdownInterval);

    countdownInterval = setInterval(() => {
        otpResendCountdown.value--;
        if (otpResendCountdown.value <= 0) {
            if (countdownInterval) clearInterval(countdownInterval);
        }
    }, 1000);
};

// Verify OTP
const verifyOtp = async () => {
    if (otp.value.length !== 6) return;

    isVerifyingOtp.value = true;
    error.value = null;

    try {
        const response = await fetch('/api/otp/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() || '',
            },
            body: JSON.stringify({
                phone: phone.value,
                otp: otp.value,
            }),
        });

        const data = await response.json();

        if (data.success) {
            phoneVerified.value = true;
            step.value = 'form';
            successMessage.value = 'Phone verified!';

            // Clear success message after 3 seconds
            setTimeout(() => {
                successMessage.value = null;
            }, 3000);
        } else {
            error.value = data.message || 'Invalid OTP';
        }
    } catch (e) {
        error.value = 'Network error. Please try again.';
    } finally {
        isVerifyingOtp.value = false;
    }
};

// Submit application
const submitApplication = async () => {
    if (!isFormValid.value) return;

    isLoading.value = true;
    error.value = null;

    try {
        const response = await fetch('/api/applications', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() || '',
            },
            body: JSON.stringify({
                product_uuid: props.product.uuid,
                first_name: firstName.value.trim(),
                last_name: lastName.value.trim(),
                phone: phone.value,
                phone_verified: true,
                loan_amount: loanAmount.value,
                loan_term: loanTerm.value,
            }),
        });

        const data = await response.json();

        if (data.success) {
            step.value = 'success';
            emit('success', data.application);
        } else {
            error.value = data.message || 'Failed to submit application';
        }
    } catch (e) {
        error.value = 'Network error. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

// Close modal
const closeModal = () => {
    emit('close');
};

// Format phone for display
const formatPhoneDisplay = (value: string) => {
    const cleaned = value.replace(/\D/g, '');
    if (cleaned.length <= 3) return cleaned;
    if (cleaned.length <= 5) return `+${cleaned.slice(0, 3)} ${cleaned.slice(3)}`;
    if (cleaned.length <= 7) return `+${cleaned.slice(0, 3)} ${cleaned.slice(3, 5)} ${cleaned.slice(5)}`;
    return `+${cleaned.slice(0, 3)} ${cleaned.slice(3, 5)} ${cleaned.slice(5, 8)} ${cleaned.slice(8, 12)}`;
};

// Go back to form from OTP step
const goBackToForm = () => {
    step.value = 'form';
    otp.value = '';
    error.value = null;
};

// Cleanup on unmount
watch(() => step.value, () => {
    if (step.value !== 'otp' && countdownInterval) {
        clearInterval(countdownInterval);
    }
});
</script>

<template>
    <div class="modal-overlay" @click.self="closeModal">
        <div class="modal-container">
            <!-- Header -->
            <div class="modal-header">
                <h2 v-if="step === 'success'">{{ __('Application Submitted') }}</h2>
                <h2 v-else-if="step === 'otp'">{{ __('Verify Phone') }}</h2>
                <h2 v-else>{{ __('Submit Application') }}</h2>
                <button class="close-btn" @click="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Success Step -->
            <div v-if="step === 'success'" class="modal-body success-body">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>{{ __('Thank you!') }}</h3>
                <p>{{ __('Your application has been submitted successfully. We will contact you shortly.') }}</p>
                <button class="btn-primary" @click="closeModal">
                    {{ __('Close') }}
                </button>
            </div>

            <!-- OTP Step -->
            <div v-else-if="step === 'otp'" class="modal-body">
                <div class="otp-info">
                    <p>{{ __('Enter the 6-digit code sent to') }}</p>
                    <strong>{{ formatPhoneDisplay(phone) }}</strong>
                </div>

                <!-- Dev OTP hint -->
                <div v-if="devOtp" class="dev-hint">
                    <i class="fas fa-info-circle"></i>
                    Dev mode: {{ devOtp }}
                </div>

                <!-- Error message -->
                <div v-if="error" class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ error }}
                </div>

                <!-- OTP Input -->
                <div class="otp-input-group">
                    <input v-model="otp" type="text" maxlength="6" placeholder="000000" class="otp-input" @keyup.enter="verifyOtp">
                </div>

                <!-- Actions -->
                <div class="otp-actions">
                    <button class="btn-primary" :disabled="otp.length !== 6 || isVerifyingOtp" @click="verifyOtp">
                        <span v-if="isVerifyingOtp"><i class="fas fa-spinner fa-spin"></i></span>
                        <span v-else>{{ __('Verify') }}</span>
                    </button>

                    <button class="btn-link" :disabled="otpResendCountdown > 0 || isSendingOtp" @click="sendOtp">
                        <span v-if="otpResendCountdown > 0">
                            {{ __('Resend in') }} {{ otpResendCountdown }}{{ __('s') }}
                        </span>
                        <span v-else>{{ __('Resend code') }}</span>
                    </button>
                </div>

                <button class="btn-back" @click="goBackToForm">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Change phone number') }}
                </button>
            </div>

            <!-- Form Step -->
            <div v-else class="modal-body">
                <!-- Product Info -->
                <div class="product-info">
                    <div class="product-badge">
                        <i class="fas fa-university"></i>
                        <span>{{ getTranslation(product.organization.name) }}</span>
                    </div>
                    <h4>{{ getTranslation(product.name) }}</h4>
                </div>

                <!-- Loan Parameters -->
                <div class="loan-params">
                    <!-- Loan Amount Slider -->
                    <div class="param-group">
                        <div class="param-header">
                            <label>{{ __('Loan amount') }}</label>
                            <div class="input-with-suffix">
                                <input v-model.number="loanAmount" type="number" class="param-input" @blur="validateLoanAmount">
                                <span class="suffix">с.</span>
                            </div>
                        </div>
                        <input v-model.number="loanAmount" type="range" :min="minAmount" :max="maxAmount" :step="(maxAmount - minAmount) / 100" class="slider">
                        <div class="param-range">
                            <span>{{ minAmount.toLocaleString('ru-RU') }} с.</span>
                            <span>{{ maxAmount.toLocaleString('ru-RU') }} с.</span>
                        </div>
                    </div>

                    <!-- Loan Term Slider -->
                    <div class="param-group">
                        <div class="param-header">
                            <label>{{ __('Loan term') }}</label>
                            <div class="input-with-suffix">
                                <input v-model.number="loanTerm" type="number" class="param-input" @blur="validateLoanTerm">
                                <span class="suffix">мес.</span>
                            </div>
                        </div>
                        <input v-model.number="loanTerm" type="range" :min="minTerm" :max="maxTerm" step="1" class="slider">
                        <div class="param-range">
                            <span>{{ minTerm }} мес.</span>
                            <span>{{ maxTerm }} мес.</span>
                        </div>
                    </div>

                    <!-- Monthly Payment Estimate -->
                    <div class="monthly-payment">
                        <span class="payment-label">{{ __('Monthly payment') }} ≈</span>
                        <span class="payment-value">{{ monthlyPayment.toLocaleString('ru-RU') }} с.</span>
                    </div>
                </div>

                <!-- Error message -->
                <div v-if="error" class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ error }}
                </div>

                <!-- Success message -->
                <div v-if="successMessage" class="success-message">
                    <i class="fas fa-check-circle"></i>
                    {{ successMessage }}
                </div>

                <!-- Form Fields -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>{{ __('First name') }} *</label>
                        <input v-model="firstName" type="text" :placeholder="__('Enter first name')" class="form-input">
                    </div>

                    <div class="form-group">
                        <label>{{ __('Last name') }} *</label>
                        <input v-model="lastName" type="text" :placeholder="__('Enter last name')" class="form-input">
                    </div>
                </div>

                <!-- Phone with OTP -->
                <div class="form-group phone-group">
                    <label>{{ __('Phone number') }} *</label>
                    <div class="phone-input-row">
                        <input v-model="phone" type="tel" placeholder="+992 XXX XXXXXX" class="form-input" :class="{ verified: phoneVerified }" :disabled="phoneVerified">
                        <button v-if="!phoneVerified" class="btn-otp" :disabled="!canSendOtp" @click="sendOtp">
                            <span v-if="isSendingOtp"><i class="fas fa-spinner fa-spin"></i></span>
                            <span v-else>{{ __('Get code') }}</span>
                        </button>
                        <span v-else class="verified-badge">
                            <i class="fas fa-check-circle"></i>
                            {{ __('Verified') }}
                        </span>
                    </div>
                </div>

                <!-- Submit -->
                <button class="btn-submit" :disabled="!isFormValid || isLoading" @click="submitApplication">
                    <span v-if="isLoading"><i class="fas fa-spinner fa-spin"></i></span>
                    <span v-else>{{ __('Submit application') }}</span>
                </button>

                <p class="form-note">
                    <i class="fas fa-shield-alt"></i>
                    {{ __('Your data is protected and will not be shared with third parties') }}
                </p>
            </div>
        </div>
    </div>
</template>
