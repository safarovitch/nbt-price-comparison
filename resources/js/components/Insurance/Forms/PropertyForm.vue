<script setup lang="ts">
import { ref } from 'vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
const propertyType = ref('apartment');
const propertyValue = ref('');

const handleCalculate = () => {
    console.log('Calculating Property for:', propertyType.value, propertyValue.value);
};
</script>

<template>
    <div>
        <h3 class="fs-4 fw-bold mb-4 position-relative text-center text-lg-start">{{ __('Home Insurance') }}</h3>

        <div class="mb-4 position-relative">
            <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-3">{{ __('Property Type') }}</label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" class="btn-check" name="propType" id="prop-apt" autocomplete="off" value="apartment" v-model="propertyType" checked>
                    <label class="btn btn-outline-primary w-100 py-3 rounded-4" for="prop-apt">
                        <i class="fa-solid fa-building mb-2 d-block fs-4"></i>
                        {{ __('Apartment') }}
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check" name="propType" id="prop-house" autocomplete="off" value="house" v-model="propertyType">
                    <label class="btn btn-outline-primary w-100 py-3 rounded-4" for="prop-house">
                        <i class="fa-solid fa-house mb-2 d-block fs-4"></i>
                        {{ __('House') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-3">{{ __('Estimated Value (TJS)') }}</label>
            <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="fa-solid fa-coins text-primary opacity-50"></i>
                </span>
                <input v-model="propertyValue" type="number" class="form-control border-0 ps-2" placeholder="e.g. 500000">
            </div>
        </div>

        <button @click="handleCalculate" class="btn btn-primary w-100 btn-lg rounded-pill py-3 fw-bold fs-5 shadow-lg border-0 transition-transform hover-scale">
            {{ __('Protect My Home') }} <i class="fa-solid fa-chevron-right ms-2 fs-6"></i>
        </button>

        <p class="text-center text-muted small mt-4 mb-0 opacity-75">
            <i class="fa-solid fa-fire me-1"></i> {{ __('Protection against fire, water damage & theft') }}
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
