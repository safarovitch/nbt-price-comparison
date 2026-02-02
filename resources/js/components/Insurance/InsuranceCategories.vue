<script setup lang="ts">
import { ref } from 'vue';
import { useTrans } from '@/composables/useTrans';
import InsuranceCategoryCard from './InsuranceCategoryCard.vue';
import OsagoForm from './Forms/OsagoForm.vue';
import TravelForm from './Forms/TravelForm.vue';
import PropertyForm from './Forms/PropertyForm.vue';

const { __ } = useTrans();

const showModal = ref(false);
const activeCategory = ref<string | null>(null);

const categories = [
    {
        id: 'transport',
        title: __('Transport'),
        items: [
            { id: 'osago', title: __('OSAGO'), desc: __('Mandatory car insurance'), icon: 'fa-solid fa-car', color: 'bg-primary-soft text-primary' },
            { id: 'kasko', title: __('Kasko'), desc: __('Full coverage'), icon: 'fa-solid fa-shield-halved', color: 'bg-primary-soft text-primary' },
        ]
    },
    {
        id: 'health',
        title: __('Health'),
        items: [
            { id: 'medical', title: __('Medical'), desc: __('Health insurance'), icon: 'fa-solid fa-briefcase-medical', color: 'bg-success-soft text-success' },
            { id: 'accident', title: __('Accidents'), desc: __('Personal injury'), icon: 'fa-solid fa-crutch', color: 'bg-success-soft text-success' },
            { id: 'sport', title: __('Sports'), desc: __('For athletes'), icon: 'fa-solid fa-person-running', color: 'bg-success-soft text-success' },
        ]
    },
    {
        id: 'property',
        title: __('Property'),
        items: [
            { id: 'apartment', title: __('Apartment'), desc: __('Flat insurance'), icon: 'fa-solid fa-building', color: 'bg-warning-soft text-warning' },
            { id: 'house', title: __('House'), desc: __('Private house'), icon: 'fa-solid fa-house', color: 'bg-warning-soft text-warning' },
            { id: 'mortgage', title: __('Mortgage'), desc: __('For bank loans'), icon: 'fa-solid fa-key', color: 'bg-warning-soft text-warning' },
        ]
    },
    {
        id: 'travel',
        title: __('Travel'),
        items: [
            { id: 'travel_abroad', title: __('Abroad'), desc: __('Visa & Travel'), icon: 'fa-solid fa-plane', color: 'bg-info-soft text-info' },
            { id: 'travel_russia', title: __('Local'), desc: __('In Tajikistan'), icon: 'fa-solid fa-map-location-dot', color: 'bg-info-soft text-info' },
        ]
    }
];

const openCategory = (id: string) => {
    activeCategory.value = id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    setTimeout(() => activeCategory.value = null, 300);
};

const getModalTitle = () => {
    if (activeCategory.value === 'osago') return __('Calculate OSAGO');
    if (activeCategory.value?.startsWith('travel')) return __('Travel Insurance');
    if (['apartment', 'house', 'mortgage'].includes(activeCategory.value || '')) return __('Property Insurance');
    return __('Insurance Request');
};
</script>

<template>
    <div class="py-5 bg-light-subtle">
        <div class="container py-lg-5">
            <div v-for="section in categories" :key="section.id" class="mb-5">
                <h2 class="fw-bold fs-4 mb-4 text-dark">{{ section.title }}</h2>
                <div class="row g-3 g-lg-4">
                    <div v-for="item in section.items" :key="item.id" class="col-6 col-md-4 col-lg-3">
                        <InsuranceCategoryCard :title="item.title" :description="item.desc" :icon="item.icon" :color-class="item.color" @click="openCategory(item.id)" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Modal -->
        <Transition name="modal-fade">
            <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
                <div class="modal-dialog-custom bg-white rounded-5 shadow-lg overflow-hidden position-relative">
                    <button @click="closeModal" class="btn btn-icon position-absolute top-0 end-0 m-4 z-2">
                        <i class="fa-solid fa-xmark fs-4 text-muted"></i>
                    </button>

                    <div class="modal-header-custom p-4 border-bottom bg-light bg-opacity-50">
                        <h3 class="fw-bold mb-0 fs-4">{{ getModalTitle() }}</h3>
                    </div>

                    <div class="p-4 p-lg-5 overflow-auto" style="max-height: 80vh;">
                        <OsagoForm v-if="activeCategory === 'osago'" />
                        <TravelForm v-else-if="activeCategory?.startsWith('travel')" />
                        <PropertyForm v-else-if="['apartment', 'house', 'mortgage'].includes(activeCategory || '')" />

                        <div v-else class="text-center py-5">
                            <i class="fa-solid fa-screwdriver-wrench fs-1 text-muted mb-4 opacity-50"></i>
                            <h4 class="fw-bold">{{ __('Coming Soon') }}</h4>
                            <p class="text-muted">{{ __('This insurance product will be available shortly.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.bg-light-subtle {
    background-color: #f8f9fa;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-dialog-custom {
    width: 100%;
    max-width: 600px;
    animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-icon:hover {
    background-color: rgba(0, 0, 0, 0.05);
    transform: rotate(90deg);
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
