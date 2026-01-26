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
    console.log('Calculating for:', carPlate.value, vehicleType.value);
};
</script>

<template>
    <div class="insurance-hero position-relative py-5">
        <div class="container py-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold mb-4 animate__animated animate__fadeInDown">
                            {{ __('Trust & Safety') }}
                        </div>
                        <h1 class="display-3 fw-bold mb-4 text-dark-emphasis animate__animated animate__fadeInLeft">
                            {{ __('OSAGO Online') }}<br>
                            <span class="text-primary">{{ __('Compare & Save') }}</span>
                        </h1>
                        <p class="lead text-muted mb-5 fs-4 animate__animated animate__fadeInLeft animate__delay-1s">
                            {{ __('Compare prices from 18 leading insurance companies and buy a policy in 5 minutes.') }}
                        </p>
                        
                        <div class="d-flex align-items-center gap-4 animate__animated animate__fadeInUp animate__delay-2s">
                            <div class="stat-item">
                                <h4 class="fw-bold mb-0">18+</h4>
                                <small class="text-muted">{{ __('Partners') }}</small>
                            </div>
                            <div class="vr opacity-25"></div>
                            <div class="stat-item">
                                <h4 class="fw-bold mb-0">5 {{ __('min') }}</h4>
                                <small class="text-muted">{{ __('To Buy') }}</small>
                            </div>
                            <div class="vr opacity-25"></div>
                            <div class="stat-item">
                                <h4 class="fw-bold mb-0">100%</h4>
                                <small class="text-muted">{{ __('Official') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 offset-lg-1 animate__animated animate__fadeInRight">
                    <div class="calculation-card bg-white p-4 p-lg-5 shadow-2xl border border-light rounded-5 position-relative z-1 overflow-hidden">
                        <div class="card-bg-decoration"></div>
                        <h3 class="fs-3 fw-bold mb-5 position-relative text-center text-lg-start">{{ __('Insurance Price Calculation') }}</h3>
                        
                        <div class="mb-4 position-relative">
                            <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-3">{{ __('Car Plate Number') }}</label>
                            <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-4">
                                    <i class="fa-solid fa-car text-primary opacity-50"></i>
                                </span>
                                <input 
                                    v-model="carPlate"
                                    type="text" 
                                    class="form-control border-0 ps-2" 
                                    placeholder="A 000 AA 01"
                                    aria-label="Car Plate"
                                >
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
                </div>
            </div>
        </div>
        
        <!-- Background Elements -->
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
    </div>
</template>

<style scoped>
.insurance-hero {
    background-color: #fcfdfe;
    min-height: 700px;
    z-index: 1;
}

.bg-primary-soft {
    background-color: rgba(39, 70, 104, 0.08);
}

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
}

.ls-1 {
    letter-spacing: 0.05em;
}

.rounded-5 {
    border-radius: 2rem !important;
}

.hero-shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    z-index: -1;
    opacity: 0.4;
}

.hero-shape-1 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, var(--bs-primary) 0%, transparent 70%);
    top: -100px;
    right: -100px;
}

.hero-shape-2 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, var(--bs-accent) 0%, transparent 70%);
    bottom: -50px;
    left: -50px;
}

.calculation-card {
    background: #ffffff;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-bg-decoration {
    position: absolute;
    top: 0;
    right: 0;
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, rgba(39, 70, 104, 0.03) 0%, transparent 100%);
    border-bottom-left-radius: 100%;
}

.hover-scale:hover {
    transform: scale(1.02);
}

input:focus, select:focus {
    outline: none !important;
    box-shadow: none !important;
}

.italic { font-style: italic; }

@media (max-width: 991.98px) {
    .display-3 { font-size: 2.5rem; }
    .insurance-hero { min-height: auto; }
}
</style>
