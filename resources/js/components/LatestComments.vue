<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';
import { Link } from '@inertiajs/vue3';

const { __ } = useTrans();

interface Comment {
    id: number;
    comment: string;
    rate: number;
    created_at: string;
    user_name: string;
    organization: {
        name: string;
        logo: string | null;
    } | null;
}

defineProps<{
    comments: Comment[];
}>();

const getStarColor = (rate: number, index: number) => {
    return index <= rate ? 'text-warning' : 'text-muted';
};
</script>

<template>
    <section class="latest-comments py-5 bg-white">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold fs-2 mb-0">{{ __('Latest Comments') }}</h2>
                <Link href="/ratings" class="text-primary text-decoration-none fw-bold">
                    {{ __('View all reviews') }} <i class="fa-solid fa-arrow-right ms-2"></i>
                </Link>
            </div>

            <div v-if="comments.length === 0" class="text-center py-5">
                <p class="text-muted">{{ __('No comments yet.') }}</p>
            </div>

            <div v-else class="row g-4">
                <div v-for="comment in comments" :key="comment.id" class="col-md-6 col-lg-4">
                    <div class="comment-card h-100 p-4 border rounded-4 bg-white position-relative hover-shadow transition-all">
                        <div class="d-flex align-items-center mb-3 gap-3">
                            <div class="organization-logo" style="width: 48px; height: 48px;">
                                <div v-if="comment.organization?.logo" class="w-100 h-100 rounded-circle border overflow-hidden d-flex align-items-center justify-content-center">
                                    <img :src="comment.organization.logo" :alt="comment.organization.name" class="w-100 h-100 object-fit-contain">
                                </div>
                                <div v-else class="w-100 h-100 rounded-circle bg-light border d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-building-columns text-muted fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="fs-6 fw-bold mb-0 text-dark">{{ comment.organization?.name || __('Unknown Organization') }}</h4>
                                <div class="rating-stars small">
                                    <i v-for="i in 5" :key="i" class="fa-solid fa-star" :class="getStarColor(comment.rate, i)"></i>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mb-3 line-clamp-3 small">{{ comment.comment }}</p>

                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                            <span class="fs-7 fw-bold text-dark">{{ comment.user_name }}</span>
                            <span class="fs-7 text-muted">{{ comment.created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
}

.transition-all {
    transition: all 0.3s ease;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.fs-7 {
    font-size: 0.85rem;
}
</style>
