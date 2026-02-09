<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface Post {
    id: string;
    title: string;
    slug: string;
    summary: string | null;
    published_at: string;
    featured_image: string | null;
    user: {
        name: string;
        avatar: string | null;
    };
    topic: {
        name: string;
        slug: string;
    }[];
}

interface Props {
    posts: {
        data: Post[];
        links: any[]; // Pagination links
    };
}

defineProps<Props>();
</script>

<template>

    <Head :title="__('News')" />

    <GuestLayout>
        <div class="blog-index-page py-5 bg-light-gray">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h1 class="fw-bold display-5 mb-3">{{ __('News') }}</h1>
                    <p class="text-muted fs-5">{{ __('Latest news and articles') }}</p>
                </div>

                <div v-if="posts.data.length === 0" class="text-center py-5">
                    <p class="text-muted fs-4">{{ __('No news found.') }}</p>
                </div>

                <div v-else class="row g-4">
                    <div v-for="post in posts.data" :key="post.id" class="col-md-6 col-lg-4">
                        <Link :href="'/news/' + post.slug" class="card h-100 border-0 shadow-sm hover-shadow text-decoration-none transition-all overflow-hidden rounded-4">
                            <div class="card-img-top position-relative" style="height: 240px;">
                                <img v-if="post.featured_image" :src="post.featured_image" :alt="post.title" class="w-100 h-100 object-fit-cover">
                                <div v-else class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">
                                    <i class="fa-solid fa-image fs-1"></i>
                                </div>
                                <div class="position-absolute top-0 start-0 p-3" v-if="post.topic && post.topic.length > 0">
                                    <span class="badge bg-primary rounded-pill">{{ post.topic[0].name }}</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column p-4 bg-white">
                                <div class="mb-2 text-muted small">
                                    <i class="fa-regular fa-calendar me-2"></i>{{ new Date(post.published_at).toLocaleDateString() }}
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-3 line-clamp-2">{{ post.title }}</h5>
                                <p class="card-text text-muted small line-clamp-3 mb-4 flex-grow-1">
                                    {{ post.summary }}
                                </p>
                                <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                    <span class="text-primary fw-bold small">{{ __('Read more') }} <i class="fa-solid fa-arrow-right ms-1"></i></span>
                                    <span class="text-muted small"><i class="fa-regular fa-user me-1"></i> {{ post.user?.name }}</span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Pagination (Simple implementation) -->
                <div v-if="posts.links.length > 3" class="mt-5 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li v-for="(link, index) in posts.links" :key="index" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
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

<style scoped>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}

.transition-all {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
