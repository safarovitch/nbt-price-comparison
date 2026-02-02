<script setup lang="ts">
import { ref } from 'vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
const destination = ref('');
const dates = ref('');

const handleCalculate = () => {
    console.log('Calculating Travel for:', destination.value, dates.value);
};
</script>

<template>
    <div>
        <h3 class="fs-4 fw-bold mb-4 position-relative text-center text-lg-start">{{ __('Travel Insurance') }}</h3>

        <div class="mb-4 position-relative">
            <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-3">{{ __('Destination Country') }}</label>
            <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="fa-solid fa-plane-departure text-primary opacity-50"></i>
                </span>
                <select v-model="destination" class="form-select border-0 ps-2">
                    <option value="" disabled selected>{{ __('Select destination') }}</option>
                    <option value="wo">{{ __('Worldwide (excl. USA/Canada)') }}</option>
                    <option value="ww">{{ __('Worldwide') }}</option>
                    <option value="eu">{{ __('Schengen Area') }}</option>
                    <option value="tr">{{ __('Turkey') }}</option>
                    <option value="ae">{{ __('UAE') }}</option>
                </select>
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-3">{{ __('Trip Dates') }}</label>
            <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="fa-regular fa-calendar text-primary opacity-50"></i>
                </span>
                <input v-model="dates" type="date" class="form-control border-0 ps-2">
            </div>
        </div>

        <button @click="handleCalculate" class="btn btn-primary w-100 btn-lg rounded-pill py-3 fw-bold fs-5 shadow-lg border-0 transition-transform hover-scale">
            {{ __('Get Quote') }} <i class="fa-solid fa-chevron-right ms-2 fs-6"></i>
        </button>

        <p class="text-center text-muted small mt-4 mb-0 opacity-75">
            <i class="fa-solid fa-shield-halved me-1"></i> {{ __('Coverage for medical expenses & lost luggage') }}
        </p>
    </div>
</template>

<style scoped>
.ls-1 {
    letter-spacing: 0.05em;
}

.rounded-4 {
    border-radius: 1rem !important;
}

.hover-scale:hover {
    transform: scale(1.02);
}

input:focus,
select:focus {
    outline: none !important;
    box-shadow: none !important;
}
</style>
