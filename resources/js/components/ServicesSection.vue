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
    { name: 'ATM locations', url: '/atms', icon: '/storage/images/icons/atms-icon.webp' },
    { name: 'Leave a comment', action: () => showCommentModal.value = true, icon: '/storage/images/icons/leave-comment-icon.webp' },
    { name: 'Exchange Calculator', url: '/exchange', icon: '/storage/images/icons/credit-rating-icon.png' },
    { name: 'Financial Literacy', url: '/financial-literacy', icon: '/storage/images/icons/home-insurance-icon.webp' },
];
</script>

<template>
    <div class="services-grid services-grid-custom">
        <template v-for="service in services" :key="service.name">
            <Link v-if="service.url" :href="service.url" class="service-card short">
                <div class="card-content">
                    <h3 class="fs-6 fw-bold mb-0">{{ __(service.name) }}</h3>
                </div>
                <div class="card-icon bottom-right">
                    <!-- <img :src="service.icon" :alt="__(service.name)"> -->
                </div>
            </Link>
            <div v-else class="service-card short" @click="service.action" style="cursor: pointer">
                <div class="card-content">
                    <h3 class="fs-6 fw-bold mb-0">{{ __(service.name) }}</h3>
                </div>
                <div class="card-icon bottom-right">
                    <!-- <img :src="service.icon" :alt="__(service.name)"> -->
                </div>
            </div>
        </template>

        <CommentModal :show="showCommentModal" @close="showCommentModal = false" />
    </div>
</template>

<style scoped>
/* Redesign Services Grid */
.services-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    grid-auto-rows: 160px;
    /* Taller rows for premium feel */
    gap: 24px;
    padding: 20px 0;
}

.service-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 24px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.service-card:hover {
    box-shadow: 0 0px 2px rgba(39, 70, 104, 0.1);
    /* Primary color shadow */
    border-color: rgba(39, 70, 104, 0.1);
}

.service-card .card-content h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #274668;
    /* Primary Color */
    line-height: 1.3;
    margin: 0;
    max-width: 80%;
    /* Space for icon if needed */
    z-index: 2;
}

.service-card .card-icon {
    position: absolute;
    bottom: 20px;
    right: 20px;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 16px;
    transition: all 0.4s ease;
}

.service-card:hover .card-icon {
    background: #eef2f6;
    transform: scale(1.1) rotate(-5deg);
}

.service-card .card-icon img {
    width: 60%;
    height: auto;
    object-fit: contain;
}

/* Exchange Rate Table Card */
.service-card.exchange-table-card {
    grid-column: span 2;
    grid-row: span 2;
    padding: 0;
    /* Let table fill */
    background: #ffffff;
    border: none;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
}

/* All Services Button */
.service-card.bg-grey {
    background: #274668;
    /* Make it primary color for contrast/CTA */
    color: white;
}

.service-card.bg-grey h3 {
    color: white;
    text-align: center;
    width: 100%;
    margin-top: 10px;
}

.service-card.bg-grey .flex-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
}

.service-card.bg-grey i {
    font-size: 2rem;
    margin-bottom: 8px;
    color: #fcdf6a;
    /* Accent color */
}

/* Specific Card Colors/Gradients (Optional subtlety) */
/* Can add faint gradients to specific cards if desired for "rich aesthetics" */
/* E.g. .service-card:nth-child(2) { background: linear-gradient(135deg, #fff 0%, #fef9e8 100%); } for loans */

/* Responsive */
@media (max-width: 1200px) {
    .services-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .service-card.exchange-table-card {
        grid-column: span 2;
        grid-row: span 2;
        order: -1;
        /* Keep exchange at top */
    }
}

@media (max-width: 992px) {
    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .service-card.exchange-table-card {
        grid-column: span 2;
    }
}

@media (max-width: 576px) {
    .services-grid {
        gap: 16px;
        grid-template-columns: repeat(2, 1fr);
        /* Keep 2 cols on mobile for density? Or 1? */
        /* User usually wants density. Let's keep 2 cols but tall exchange table. */
    }

    .services-grid {
        grid-template-columns: 1fr;
        /* Actually 1 col is safer for small screens */
    }

    /* Let's try 2 cols for buttons, spanning full for table */
    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .service-card.exchange-table-card {
        grid-column: span 2;
    }
}
</style>
