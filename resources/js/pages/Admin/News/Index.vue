<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { FileText, Edit, Plus, Trash2 } from 'lucide-vue-next';
import { formatDistanceToNow } from 'date-fns';

interface Topic {
    id: string;
    name: string;
    slug: string;
}

interface Post {
    id: string;
    title: string;
    slug: string;
    published_at: string | null;
    created_at: string;
    topic: Topic | null;
}

interface Props {
    posts: {
        data: Post[];
        links: any[];
    };
}

defineProps<Props>();

const breadcrumbs = [
    {
        title: 'News',
        href: '/admin/news',
    },
];

const deletePost = (post: Post) => {
    if (confirm('Are you sure you want to delete this news item?')) {
        router.delete(`/admin/news/${post.id}`);
    }
};

const formatDate = (date: string | null) => {
    if (!date) return 'Draft';
    return new Date(date).toLocaleDateString();
};

const isPublished = (date: string | null) => {
    return date && new Date(date) <= new Date();
};

const getLocalizedValue = (value: any) => {
    if (!value) return '';
    if (typeof value === 'object') {
        const locale = document.documentElement.lang || 'en';
        return value[locale] || value.en || value.ru || Object.values(value)[0] || '';
    }
    // Attempt to parse JSON string if it looks like JSON
    if (typeof value === 'string' && value.trim().startsWith('{')) {
        try {
            const parsed = JSON.parse(value);
            const locale = document.documentElement.lang || 'en';
            return parsed[locale] || parsed.en || parsed.ru || Object.values(parsed)[0] || '';
        } catch (e) {
            return value;
        }
    }
    return value;
};
</script>

<template>

    <Head title="News" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0 d-flex align-items-center gap-2">
                    <FileText class="text-muted" />
                    News
                </h2>
                <Link href="/admin/news/create" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <Plus style="width: 1.25rem; height: 1.25rem;" />
                    Add News
                </Link>
            </div>

            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="px-4 py-3">Title</th>
                                <th class="py-3">Topic</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Published At</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="post in posts.data" :key="post.id">
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ getLocalizedValue(post.title) }}</div>
                                    <div class="small text-muted">/news/{{ post.slug }}</div>
                                </td>
                                <td>
                                    <span v-if="post.topic" class="badge bg-light text-dark border">
                                        {{ post.topic.name }}
                                    </span>
                                    <span v-else class="text-muted small">-</span>
                                </td>
                                <td>
                                    <span :class="['badge', isPublished(post.published_at) ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning']">
                                        {{ isPublished(post.published_at) ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ formatDate(post.published_at) }}
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <Link :href="`/admin/news/${post.id}/edit`" class="btn btn-sm btn-outline-primary p-1" title="Edit">
                                            <Edit style="width: 1rem; height: 1rem;" />
                                        </Link>
                                        <button @click="deletePost(post)" class="btn btn-sm btn-outline-danger p-1" title="Delete">
                                            <Trash2 style="width: 1rem; height: 1rem;" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!posts.data || posts.data.length === 0">
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No news found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="posts.links && posts.links.length > 3" class="mt-4 d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-sm">
                        <li v-for="(link, k) in posts.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                            <Link :href="link.url || '#'" class="page-link" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
