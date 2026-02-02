<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';
import { ref } from 'vue';
import CommentModal from '@/components/CommentModal.vue';

const { __ } = useTrans();
const showCommentModal = ref(false);

const services = [
    { name: 'Loan Calculator', url: '/loan-calculator', icon: '/storage/images/icons/credits-icon.png' },
    { name: 'Credit Calculator', url: '/credit-calculator', icon: '/storage/images/icons/house-financing-icon.png' },
    { name: 'Deposit Calculator', url: '/deposit-calculator', icon: '/storage/images/icons/deposit-icon.png' },
    { name: 'Exchange Rates', url: '/exchange', icon: '/storage/images/icons/credit-rating-icon.png' },
    { name: 'Bank ratings', url: '/ratings?type=bank', icon: '/storage/images/icons/shield-icon.webp' },
    { name: 'Insurance ratings', url: '/ratings?type=insurance', icon: '/storage/images/icons/insurance-icon.png' },
    { name: 'Leave a comment', action: () => showCommentModal.value = true, icon: '/storage/images/icons/leave-comment-icon.webp' },
    { name: 'Exchange Calculator', url: '/exchange', icon: '/storage/images/icons/credit-rating-icon.png' },
    { name: 'Financial Literacy', url: '/financial-literacy', icon: '/storage/images/icons/home-insurance-icon.webp' },
    { name: 'News', url: '/news', icon: '/storage/images/icons/sport-insurance-icon.png' },
];
</script>

<template>
    <div class="services-grid services-grid-custom mb-5">
        <template v-for="service in services" :key="service.name">
            <Link v-if="service.url" :href="service.url" class="service-card short">
                <div class="card-content">
                    <h3 class="fs-6 fw-bold mb-0">{{ __(service.name) }}</h3>
                </div>
                <div class="card-icon bottom-right">
                    <img :src="service.icon" :alt="__(service.name)">
                </div>
            </Link>
            <div v-else class="service-card short" @click="service.action" style="cursor: pointer">
                <div class="card-content">
                    <h3 class="fs-6 fw-bold mb-0">{{ __(service.name) }}</h3>
                </div>
                <div class="card-icon bottom-right">
                    <img :src="service.icon" :alt="__(service.name)">
                </div>
            </div>
        </template>

        <CommentModal :show="showCommentModal" @close="showCommentModal = false" />
    </div>
</template>

<style scoped>
.services-grid-custom {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (min-width: 768px) {
    .services-grid-custom {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 992px) {
    .services-grid-custom {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (min-width: 1200px) {
    .services-grid-custom {
        grid-template-columns: repeat(5, 1fr);
    }
}
</style>
