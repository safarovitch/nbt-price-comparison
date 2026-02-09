<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

interface Category {
    name: string;
}

interface Post {
    id: number;
    title: string;
    slug: string;
    short_description: string | null;
    posted_at: string;
    image_url: string | null;
    categories: Category[];
}

const props = defineProps<{
    posts: Post[];
}>();
</script>

<template>
    <section class="latest-news py-5 bg-light-gray" v-if="posts && posts.length > 0">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold fs-2 mb-0">{{ __('Latest News') }}</h2>
                <Link href="/news" class="text-primary text-decoration-none fw-bold">
                    {{ __('View all news') }} <i class="fa-solid fa-arrow-right ms-2"></i>
                </Link>
            </div>

            <div class="row g-4">
                <div v-for="post in posts" :key="post.id" class="col-md-6 col-lg-4">
                    <Link :href="`/news/${post.slug}`" class="card h-100 border-0 shadow-sm hover-shadow text-decoration-none transition-all overflow-hidden rounded-4">
                        <div class="card-img-top position-relative" style="height: 220px;">
                            <img v-if="post.image_url" :src="post.image_url" :alt="post.title" class="w-100 h-100 object-fit-cover">
                            <div v-else class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">
                                <i class="fa-solid fa-newspaper fs-1"></i>
                            </div>
                            <!-- Category Badge -->
                            <div class="position-absolute top-0 start-0 p-3" v-if="post.categories && post.categories.length > 0">
                                <span class="badge bg-primary rounded-pill">{{ post.categories[0].name }}</span>
                            </div>
                        </div>
                        <div class="card-body bg-white p-4 d-flex flex-column">
                            <div class="mb-2 text-muted small">
                                <i class="fa-regular fa-calendar me-2"></i>{{ new Date(post.posted_at).toLocaleDateString() }}
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-3 line-clamp-2">{{ post.title }}</h5>
                            <p class="card-text text-muted small line-clamp-3 mb-4 flex-grow-1">
                                {{ post.short_description }}
                            </p>
                            <div class="mt-auto pt-3 border-top">
                                <span class="text-primary fw-bold small">{{ __('Read more') }} <i class="fa-solid fa-arrow-right ms-1"></i></span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </section>
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
