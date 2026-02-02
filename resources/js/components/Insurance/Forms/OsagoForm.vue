<script setup lang="ts">
import { ref } from 'vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
const carPlate = ref('');
const vehicleType = ref('car');

const handleCalculate = () => {
    // Basic validation or logic can go here
    if (!carPlate.value && carPlate.value !== 'TEST') {
        // Trigger some UI feedback?
    }
    console.log('Calculating OSAGO for:', carPlate.value, vehicleType.value);
};
</script>

<template>
    <div>
        <h3 class="fs-4 fw-bold mb-4 position-relative text-center text-lg-start">{{ __('Calculate OSAGO Price') }}</h3>

        <div class="mb-4 position-relative">
            <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-3">{{ __('Car Plate Number') }}</label>
            <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="fa-solid fa-car text-primary opacity-50"></i>
                </span>
                <input v-model="carPlate" type="text" class="form-control border-0 ps-2" placeholder="A 000 AA 01" aria-label="Car Plate">
            </div>
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted italic">{{ __('Example: 5555 AB 01') }}</span>
                <a href="#" class="text-primary small text-decoration-none fw-bold hover-underline">{{ __("I don't have a number yet") }}</a>
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-3">{{ __('Vehicle Type') }}</label>
            <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="fa-solid fa-truck-pickup text-primary opacity-50"></i>
                </span>
                <select v-model="vehicleType" class="form-select border-0 ps-2">
                    <option value="car">{{ __('Car') }}</option>
                    <option value="truck">{{ __('Truck') }}</option>
                    <option value="motorcycle">{{ __('Motorcycle') }}</option>
                </select>
            </div>
        </div>

        <button @click="handleCalculate" class="btn btn-primary w-100 btn-lg rounded-pill py-3 fw-bold fs-5 shadow-lg border-0 transition-transform hover-scale">
            {{ __('Calculate') }} <i class="fa-solid fa-chevron-right ms-2 fs-6"></i>
        </button>

        <p class="text-center text-muted small mt-4 mb-0 opacity-75">
            <i class="fa-solid fa-lock me-1"></i> {{ __('Your data is secure and protected') }}
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

.italic {
    font-style: italic;
}
</style>
