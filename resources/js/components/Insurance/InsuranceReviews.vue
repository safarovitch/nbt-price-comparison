<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const reviews = [
    {
        id: 1,
        author: 'Александр К.',
        date: '20.01.2026',
        rating: 5,
        text: __('Very convenient service. Compared several companies and chose the best price for OSAGO. Registration took parallel to 10 minutes.'),
        company: 'Takafful'
    },
    {
        id: 2,
        author: 'Марина С.',
        date: '15.01.2026',
        rating: 4,
        text: __('Everything is clear and simple. The policy came to my email instantly after payment. Will recommend to friends.'),
        company: 'BIMA'
    },
    {
        id: 3,
        author: 'Джамшед Р.',
        date: '10.01.2026',
        rating: 5,
        text: __('Best way to get insurance in Dushanbe. No need to visit offices. Great support team through the chat.'),
        company: 'Spitamenskaya'
    }
];
</script>

<template>
    <div class="py-5 bg-light-gray">
        <div class="container py-lg-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-4">
                <div class="text-center text-md-start">
                    <h2 class="fw-bold fs-1 mb-2">{{ __('Customer Reviews') }}</h2>
                    <p class="text-muted mb-0">{{ __('What our clients say about insurance registration') }}</p>
                </div>
                <div class="review-stats bg-white p-4 rounded-4 shadow-sm border border-light d-flex align-items-center gap-4">
                    <div class="text-center">
                        <div class="display-5 fw-bold text-dark">4.9</div>
                        <div class="text-warning small">
                            <i v-for="i in 5" :key="i" class="fa-solid fa-star"></i>
                        </div>
                    </div>
                    <div class="vr opacity-25"></div>
                    <div>
                        <div class="fw-bold text-dark fs-5">12,450+</div>
                        <small class="text-muted">{{ __('Verified Reviews') }}</small>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div v-for="review in reviews" :key="review.id" class="col-lg-4">
                    <div class="review-card bg-white p-4 p-xl-5 h-100 rounded-5 transition-all shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex gap-2">
                                <i v-for="i in 5" :key="i" class="fa-solid fa-star small" :class="i <= review.rating ? 'text-warning' : 'text-light'"></i>
                            </div>
                            <span class="small text-muted opacity-50">{{ review.date }}</span>
                        </div>
                        
                        <p class="review-text text-dark lh-base mb-4 italic">"{{ review.text }}"</p>
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px;">
                                {{ review.author[0] }}
                            </div>
                            <div>
                                <h5 class="fs-6 fw-bold mb-0">{{ review.author }}</h5>
                                <small class="text-muted">{{ __('Insured in') }} {{ review.company }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <button class="btn btn-outline-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                    {{ __('Leave a Review') }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-light-gray {
    background-color: #f9fbff;
}

.review-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -15px rgba(0,0,0,0.08) !important;
}

.avatar {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.review-text {
    font-size: 0.95rem;
    min-height: 80px;
}

.italic { font-style: italic; }

.rounded-5 {
    border-radius: 2rem !important;
}
</style>
