<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const faqs = [
    {
        q: __('Is the electronic policy valid?'),
        a: __('Yes, the electronic OSAGO policy has the same legal force as the paper one. You can show it on your smartphone or print it.')
    },
    {
        q: __('How is the price calculated?'),
        a: __('The cost depends on the base rate of the insurance company and coefficients: region, driver age/experience, power of the car, and no-claim discount (KBM).')
    },
    {
        q: __('What if I make a mistake in the data?'),
        a: __('You should contact the insurance company to make changes. Most changes can be made online through the personal cabinet on the insurer website.')
    }
];
</script>

<template>
    <div class="py-5 bg-white border-top">
        <div class="container py-lg-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold fs-1 text-center mb-5">{{ __('Frequently Asked Questions') }}</h2>
                    
                    <div class="accordion accordion-custom" id="insuranceFaq">
                        <div v-for="(faq, index) in faqs" :key="index" class="accordion-item border border-light rounded-4 mb-3 overflow-hidden shadow-sm transition-all hover-shadow">
                            <h2 class="accordion-header" :id="'heading' + index">
                                <button class="accordion-button collapsed fw-bold bg-white text-dark py-4 px-4 shadow-none" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse' + index">
                                    {{ faq.q }}
                                </button>
                            </h2>
                            <div :id="'collapse' + index" class="accordion-collapse collapse" :data-bs-parent="'#insuranceFaq'">
                                <div class="accordion-body px-4 pb-4 pt-0 text-muted lh-base">
                                    {{ faq.a }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.accordion-custom .accordion-button:not(.collapsed) {
    color: var(--bs-primary);
    background-color: #fff;
}

.accordion-custom .accordion-button::after {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-shadow:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    border-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.rounded-4 {
    border-radius: 1.25rem !important;
}
</style>
