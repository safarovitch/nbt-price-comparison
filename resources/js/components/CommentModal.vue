<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits(['close']);

// Step 1: Organization Search
const searchQuery = ref('');
const organizations = ref<any[]>([]);
const selectedOrganization = ref<any>(null);
const searching = ref(false);

const searchOrganizations = async () => {
    if (searchQuery.value.length < 2) return;
    searching.value = true;
    try {
        const response = await axios.get(route('api.ratings.organizations'), {
            params: { query: searchQuery.value }
        });
        organizations.value = response.data;
    } catch (e) {
        console.error(e);
    } finally {
        searching.value = false;
    }
};

let debounceTimeout: any;
watch(searchQuery, () => {
    if (selectedOrganization.value && searchQuery.value === selectedOrganization.value.name) return; // Selected
    if (debounceTimeout) clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        searchOrganizations();
    }, 300);
});

const selectOrganization = (org: any) => {
    selectedOrganization.value = org;
    searchQuery.value = org.name;
    organizations.value = [];
    form.organization_uuid = org.uuid;
};

// Step 2: Rating & Comment
const form = useForm({
    organization_uuid: '',
    rating: 0,
    comment: '',
    phone: '',
    phone_verified: false as boolean,
    otp: ''
});

// Step 3: OTP Flow
const otpSent = ref(false);
const otpCoolDown = ref(0);
const otpLoading = ref(false);

const sendOtp = async () => {
    if (!form.phone || form.phone.length < 9) return;
    otpLoading.value = true;
    try {
        await axios.post(route('api.otp.send'), { phone: form.phone });
        otpSent.value = true;
        otpCoolDown.value = 60;
        const interval = setInterval(() => {
            otpCoolDown.value--;
            if (otpCoolDown.value <= 0) clearInterval(interval);
        }, 1000);
    } catch (e) {
        console.error(e);
        // Handle error (show toast?)
    } finally {
        otpLoading.value = false;
    }
};

const verifyOtp = async () => {
    if (!form.otp || form.otp.length !== 6) return;
    otpLoading.value = true;
    try {
        await axios.post(route('api.otp.verify'), { phone: form.phone, otp: form.otp });
        form.phone_verified = true;
        otpSent.value = false; // Hide OTP field, show success?
    } catch (e) {
        console.error(e);
        // Handle error
    } finally {
        otpLoading.value = false;
    }
};

const submitComment = () => {
    form.post(route('ratings.store'), {
        onSuccess: () => {
            emit('close');
            form.reset();
            selectedOrganization.value = null;
            searchQuery.value = '';
            // Reset OTP state too? Maybe keep phone cached?
        }
    });
};

</script>

<template>
    <div v-if="show" class="modal-backdrop" @click.self="emit('close')">
        <div class="modal-content p-4 rounded-4 bg-white shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fs-4 fw-bold mb-0">{{ __('Leave a Comment') }}</h3>
                <button class="btn-close" @click="emit('close')"></button>
            </div>

            <form @submit.prevent="submitComment">
                <!-- Organization Search -->
                <div class="mb-4 position-relative">
                    <label class="form-label fw-bold">{{ __('Select Organization') }}</label>
                    <input type="text" v-model="searchQuery" class="form-control" :placeholder="__('Search bank or organization...')" :disabled="!!selectedOrganization">
                    <button v-if="selectedOrganization" type="button" class="btn btn-sm btn-light position-absolute top-50 end-0 me-2 mt-2 translate-middle-y" @click="selectedOrganization = null; searchQuery = ''; form.organization_uuid = ''">
                        <i class="fa-solid fa-times"></i>
                    </button>

                    <!-- Dropdown results -->
                    <div v-if="organizations.length > 0 && !selectedOrganization" class="list-group position-absolute w-100 shadow-sm mt-1" style="max-height: 200px; overflow-y: auto; z-index: 1050;">
                        <button v-for="org in organizations" :key="org.uuid" type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-2" @click="selectOrganization(org)">
                            <img v-if="org.logo" :src="org.logo" class="rounded-circle" width="24" height="24" style="object-fit: contain;">
                            <span>{{ org.name }}</span>
                        </button>
                    </div>
                </div>

                <!-- Rating -->
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold d-block mb-2">{{ __('Your Rating') }}</label>
                    <div class="rating-stars fs-2">
                        <i v-for="i in 5" :key="i" class="fa-star cursor-pointer transition-all" :class="[i <= form.rating ? 'fa-solid text-warning' : 'fa-regular text-muted']" @click="form.rating = i"></i>
                    </div>
                    <div v-if="form.errors.rating" class="text-danger small mt-1">{{ form.errors.rating }}</div>
                </div>

                <!-- Comment -->
                <div class="mb-4">
                    <label class="form-label fw-bold">{{ __('Your Comment') }}</label>
                    <textarea v-model="form.comment" class="form-control" rows="3" :placeholder="__('Share your experience...')"></textarea>
                    <div v-if="form.errors.comment" class="text-danger small mt-1">{{ form.errors.comment }}</div>
                </div>

                <!-- Phone Verification -->
                <div class="mb-4 p-3 bg-light rounded-3">
                    <div v-if="!form.phone_verified">
                        <label class="form-label fw-bold">{{ __('Verify Phone Number') }}</label>
                        <div class="input-group mb-2">
                            <input type="tel" v-model="form.phone" class="form-control" :placeholder="__('+992...')" :disabled="otpSent">
                            <button v-if="!otpSent" type="button" class="btn btn-primary" @click="sendOtp" :disabled="!form.phone || otpLoading">
                                <span v-if="otpLoading" class="spinner-border spinner-border-sm"></span>
                                <span v-else>{{ __('Send OTP') }}</span>
                            </button>
                        </div>

                        <div v-if="otpSent" class="mt-3">
                            <label class="form-label small text-muted">{{ __('Enter OTP code') }}</label>
                            <div class="input-group">
                                <input type="text" v-model="form.otp" class="form-control text-center letter-spacing-2" placeholder="000000" maxlength="6">
                                <button type="button" class="btn btn-success" @click="verifyOtp" :disabled="!form.otp || otpLoading">
                                    <span v-if="otpLoading" class="spinner-border spinner-border-sm"></span>
                                    <span v-else>{{ __('Verify') }}</span>
                                </button>
                            </div>
                            <div class="text-center mt-2">
                                <small v-if="otpCoolDown > 0" class="text-muted">{{ __('Resend in') }} {{ otpCoolDown }}s</small>
                                <button v-else type="button" class="btn btn-link btn-sm p-0" @click="sendOtp">{{ __('Resend OTP') }}</button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-success text-center py-2">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        <span class="fw-bold">{{ __('Phone Verified') }}: {{ form.phone }}</span>
                    </div>
                    <div v-if="form.errors.phone_verified" class="text-danger small mt-1">{{ form.errors.phone_verified }}</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" :disabled="form.processing || !form.phone_verified || !form.organization_uuid || form.rating === 0">
                        {{ __('Submit Comment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

.modal-content {
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    /* Prevent overflow */
    overflow-y: auto;
    /* Allow scrolling content */
}

.cursor-pointer {
    cursor: pointer;
}

.letter-spacing-2 {
    letter-spacing: 0.5em;
}

.transition-all {
    transition: all 0.2s;
}

.fa-star:hover {
    transform: scale(1.1);
}
</style>
