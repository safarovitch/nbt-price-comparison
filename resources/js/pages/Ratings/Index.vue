<script setup lang="ts">
import { ref, watch } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';
import Pagination from '@/components/Pagination.vue'; // Assuming we need pagination component or simple links

const { __ } = useTrans();

const props = defineProps<{
    comments: {
        data: Array<{
            id: number;
            rating: number;
            content: string;
            created_at: string;
            organization: {
                name: string;
                logo: string | null;
                type: string;
            } | null;
            user_name: string;
        }>;
        links: any[];
    };
    filters: {
        type?: string;
        sort?: string;
    };
}>();

const type = ref(props.filters.type || '');
const sort = ref(props.filters.sort || 'newest');

const updateFilters = () => {
    router.get('/ratings', {
        type: type.value,
        sort: sort.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch([type, sort], () => {
    updateFilters();
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString();
};
</script>

<template>

    <Head :title="__('Ratings & Comments')" />

    <GuestLayout>
        <div class="bg-light-gray min-vh-100 py-5">
            <div class="container">
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <Link href="/" class="text-decoration-none text-muted mb-2 d-inline-block">
                            <i class="fa-solid fa-arrow-left me-2"></i> {{ __('Back to Home') }}
                        </Link>
                        <h1 class="fw-bold">{{ __('Ratings & Comments') }}</h1>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('Organization Type') }}</label>
                            <select v-model="type" class="form-select form-select-lg">
                                <option value="" selected>{{ __('All Types') }}</option>
                                <option value="bank">{{ __('Banks') }}</option>
                                <option value="insurance">{{ __('Insurance Companies') }}</option>
                                <option value="mfo">{{ __('Microfinance Organizations') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('Sort By') }}</label>
                            <select v-model="sort" class="form-select form-select-lg">
                                <option value="newest">{{ __('Newest First') }}</option>
                                <option value="oldest">{{ __('Oldest First') }}</option>
                                <option value="rating_high">{{ __('Highest Rating') }}</option>
                                <option value="rating_low">{{ __('Lowest Rating') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Comments List -->
                <div v-if="comments.data.length > 0" class="row g-4">
                    <div v-for="comment in comments.data" :key="comment.id" class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div v-if="comment.organization?.logo" class="rounded-circle bg-light border d-flex align-items-center justify-content-center overflow-hidden" style="width: 50px; height: 50px;">
                                        <img :src="comment.organization.logo" :alt="comment.organization.name" class="w-100 h-100 object-fit-contain">
                                    </div>
                                    <div v-else class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fa-solid fa-building-columns text-muted fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="fw-bold mb-1">{{ comment.organization?.name }}</h5>
                                            <div class="text-warning small mb-1">
                                                <i v-for="i in 5" :key="i" class="fa-solid fa-star" :class="i <= comment.rating ? 'text-warning' : 'text-muted opacity-25'"></i>
                                            </div>
                                        </div>
                                        <span class="text-muted small">{{ formatDate(comment.created_at) }}</span>
                                    </div>
                                    <p class="mb-0 text-dark">{{ comment.content }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-5">
                    <div class="mb-3">
                        <i class="fa-regular fa-comment-dots fs-1 text-muted opacity-25"></i>
                    </div>
                    <h3 class="fw-bold text-muted">{{ __('No comments found') }}</h3>
                    <p class="text-muted">{{ __('Try adjusting your filters') }}</p>
                </div>

                <!-- Pagination -->
                <div v-if="comments.links.length > 3" class="d-flex justify-content-center mt-5">
                    <!-- Simple pagination implementation using bootstrap classes -->
                    <nav>
                        <ul class="pagination">
                            <li v-for="(link, index) in comments.links" :key="index" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                                <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label"></Link>
                                <span v-else class="page-link" v-html="link.label"></span>
                            </li>
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </GuestLayout>
</template>
