<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
</script>

<template>
    <div class="insurance-hero position-relative py-5">
        <div class="container py-lg-4 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8 animate__animated animate__fadeInUp">
                    <div class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold mb-4">
                        {{ __('Insurance Marketplace') }}
                    </div>
                    <h1 class="display-3 fw-bold mb-4 text-dark-emphasis">
                        {{ __('Find the best insurance') }} <br>
                        <span class="text-primary">{{ __('for any occasion') }}</span>
                    </h1>
                    <p class="lead text-muted mb-5 fs-4 opacity-75">
                        {{ __('Compare offers from leading insurance companies, save up to 40%, and buy online in 5 minutes.') }}
                    </p>

                    <div class="d-flex align-items-center justify-content-center gap-4 gap-md-5">
                        <div class="text-center">
                            <h4 class="fw-bold mb-0 text-dark">20+</h4>
                            <small class="text-muted">{{ __('Partners') }}</small>
                        </div>
                        <div class="vr opacity-25"></div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-0 text-dark">5 {{ __('min') }}</h4>
                            <small class="text-muted">{{ __('To Buy') }}</small>
                        </div>
                        <div class="vr opacity-25"></div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-0 text-dark">100%</h4>
                            <small class="text-muted">{{ __('Online') }}</small>
                        </div>
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
    background-color: #ffffff;
    z-index: 1;
}

.bg-primary-soft {
    background-color: rgba(39, 70, 104, 0.08);
}

.hero-shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: -1;
    opacity: 0.3;
}

.hero-shape-1 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, var(--bs-primary) 0%, transparent 70%);
    top: -50px;
    left: -50px;
}

.hero-shape-2 {
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, var(--bs-accent) 0%, transparent 70%);
    bottom: -20px;
    right: -20px;
}

@media (max-width: 991.98px) {
    .display-3 {
        font-size: 2.5rem;
    }
}
</style>
