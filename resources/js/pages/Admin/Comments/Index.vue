<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { MessageSquare, Check, X, Trash2 } from 'lucide-vue-next';

interface Comment {
    id: number;
    comment: string;
    approved: boolean;
    created_at: string;
    commented: {
        id: number;
        name: string;
        email: string;
    } | null;
    commentable: {
        id: number;
        title?: string;
        name?: string;
    } | null;
}

interface Props {
    comments: {
        data: Comment[];
        links: any[];
    };
}

defineProps<Props>();

const breadcrumbs = [
    {
        title: 'Comments',
        href: '/admin/comments',
    },
];

const toggleApproval = (comment: Comment) => {
    router.put(`/admin/comments/${comment.id}`, {
        approved: !comment.approved,
    }, {
        preserveScroll: true,
    });
};

const deleteComment = (comment: Comment) => {
    if (confirm('Are you sure you want to delete this comment?')) {
        router.delete(`/admin/comments/${comment.id}`, {
            preserveScroll: true,
        });
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString() + ' ' + new Date(date).toLocaleTimeString();
};
</script>

<template>
    <Head title="Comments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0 d-flex align-items-center gap-2">
                    <MessageSquare class="text-muted" />
                    Comments
                </h2>
            </div>

            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="px-4 py-3">Author</th>
                                <th class="py-3">Comment</th>
                                <th class="py-3">Target</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Date</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="comment in comments.data" :key="comment.id">
                                <td class="px-4">
                                    <div v-if="comment.commented">
                                        <div class="fw-bold text-dark">{{ comment.commented.name }}</div>
                                        <div class="small text-muted">{{ comment.commented.email }}</div>
                                    </div>
                                    <span v-else class="text-muted small">Guest</span>
                                </td>
                                <td>
                                    <div class="text-break" style="max-width: 400px;">
                                        {{ comment.comment }}
                                    </div>
                                </td>
                                <td>
                                    <span v-if="comment.commentable" class="badge bg-light text-dark border">
                                        {{ comment.commentable.title || comment.commentable.name || 'Unknown' }}
                                    </span>
                                    <span v-else class="text-muted small">-</span>
                                </td>
                                <td>
                                    <span :class="['badge', comment.approved ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning']">
                                        {{ comment.approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ formatDate(comment.created_at) }}
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button @click="toggleApproval(comment)" 
                                            :class="['btn btn-sm p-1', comment.approved ? 'btn-outline-warning' : 'btn-outline-success']" 
                                            :title="comment.approved ? 'Reject' : 'Approve'">
                                            <X v-if="comment.approved" style="width: 1rem; height: 1rem;" />
                                            <Check v-else style="width: 1rem; height: 1rem;" />
                                        </button>
                                        <button @click="deleteComment(comment)" class="btn btn-sm btn-outline-danger p-1" title="Delete">
                                            <Trash2 style="width: 1rem; height: 1rem;" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!comments.data || comments.data.length === 0">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    No comments found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="comments.links && comments.links.length > 3" class="mt-4 d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-sm">
                        <li v-for="(link, k) in comments.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                            <Link :href="link.url || '#'" class="page-link" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
