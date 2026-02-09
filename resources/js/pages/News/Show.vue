<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import BlockRenderer from '@/components/BlockRenderer.vue';
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
    body: string;
    published_at: string;
    featured_image: string | null;
    user: {
        name: string;
        avatar: string | null;
    };
    topic: {
        id: string;
        name: string;
        slug: string;
    }[];
    meta: {
        title?: string;
        description?: string;
    };
}

interface Props {
    post: Post;
    relatedPosts: Post[];
}

defineProps<Props>();
</script>

<template>

    <Head :title="post.meta?.title || post.title">
        <meta name="description" :content="post.meta?.description || post.summary || ''">
    </Head>

    <GuestLayout>
        <div class="blog-post-page py-5 bg-white">
            <div class="container-xxl"> <!-- Wider container for full screen feel -->
                <!-- Breadcrumbs/Back Link -->
                <div class="mb-4">
                    <Link href="/news" class="text-decoration-none text-muted small fw-bold">
                        <i class="fa-solid fa-arrow-left me-1"></i> {{ __('Back to News') }}
                    </Link>
                </div>

                <div class="row g-5">
                    <!-- Main Content (Left) -->
                    <div class="col-lg-8">
                        <!-- Post Header -->
                        <header class="mb-5">
                            <div class="mb-3 d-flex gap-2" v-if="post.topic && post.topic.length > 0">
                                <span v-for="topic in post.topic" :key="topic.id" class="badge bg-primary rounded-pill px-3 py-2">
                                    {{ topic.name }}
                                </span>
                            </div>
                            <h1 class="fw-bold display-5 mb-3">{{ post.title }}</h1>
                            <p class="text-muted fs-5 mb-4" v-if="post.summary">{{ post.summary }}</p>

                            <div class="d-flex align-items-center gap-3 text-muted small border-top border-bottom py-3">
                                <span><i class="fa-regular fa-user me-2"></i>{{ post.user?.name }}</span>
                                <span><i class="fa-regular fa-calendar me-2"></i>{{ new Date(post.published_at).toLocaleDateString() }}</span>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        <div class="mb-5 rounded-4 overflow-hidden shadow-sm" v-if="post.featured_image">
                            <img :src="post.featured_image" :alt="post.title" class="w-100 object-fit-cover" style="max-height: 500px;">
                        </div>

                        <!-- Post Content -->
                        <article class="blog-content fs-5 lh-lg text-dark">
                            <BlockRenderer :content="post.body" />
                        </article>

                    </div>

                    <!-- Sidebar (Right) - Latest News -->
                    <div class="col-lg-4">
                        <div class="text-light-gray p-4 rounded-4 sticky-top" style="top: 2rem;">
                            <h3 class="fw-bold mb-4">{{ __('Latest News') }}</h3>
                            <div class="d-flex flex-column gap-4">
                                <div v-for="related in relatedPosts" :key="related.id" class="card border-0 bg-transparent">
                                    <Link :href="`/news/${related.slug}`" class="text-decoration-none text-dark group">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-4">
                                                <img v-if="related.featured_image" :src="related.featured_image" :alt="related.title" class="img-fluid rounded-3 object-fit-cover" style="height: 80px; width: 100%;">
                                                <div v-else class="bg-secondary rounded-3 d-flex align-items-center justify-content-center text-white" style="height: 80px; width: 100%;">
                                                    <i class="fa-solid fa-newspaper"></i>
                                                </div>
                                            </div>
                                            <div class="col-8 ps-3">
                                                <h6 class="fw-bold mb-1 text-truncate-2">{{ related.title }}</h6>
                                                <small class="text-muted">{{ new Date(related.published_at).toLocaleDateString() }}</small>
                                            </div>
                                        </div>
                                    </Link>
                                </div>
                                <div v-if="!relatedPosts || relatedPosts.length === 0" class="text-muted">
                                    {{ __('No other news available.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
/* Global styles for blog content injected via v-html */
.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 2rem 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.blog-content h2 {
    font-weight: 700;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.blog-content h3 {
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    color: #34495e;
}

.blog-content p {
    margin-bottom: 1.5rem;
}

.blog-content ul,
.blog-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.blog-content blockquote {
    border-left: 4px solid #3498db;
    padding-left: 1.5rem;
    font-style: italic;
    color: #555;
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0 12px 12px 0;
    margin: 2rem 0;
}
</style>
