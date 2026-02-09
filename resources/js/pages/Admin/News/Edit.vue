```html
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Editor from '@/components/Editor.vue';
import FormLanguageSwitcher from '@/components/FormLanguageSwitcher.vue';
import { useTranslatableForm } from '@/composables/useTranslatableForm';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ArrowLeft, Save, Trash2 } from 'lucide-vue-next';
import { watch } from 'vue';

interface TranslatableString {
    [key: string]: string;
}

interface Topic {
    id: string;
    name: string;
}

interface NewsPost {
    id: string;
    title: TranslatableString;
    slug: string;
    summary: TranslatableString;
    body: TranslatableString;
    featured_image_url: string | null;
    topic_id: string;
    published_at: string;
}

const props = defineProps<{
    post: NewsPost;
    topics: Topic[];
}>();

const { currentLang, availableLanguages, syncField, hasContent } = useTranslatableForm('ru');

const form = useForm({
    _method: 'PUT',
    title: props.post.title || { ru: '', en: '', tj: '' },
    slug: props.post.slug,
    summary: props.post.summary || { ru: '', en: '', tj: '' },
    body: props.post.body || { ru: '', en: '', tj: '' },
    featured_image: null as File | null,
    topic_id: props.post.topic_id || '',
    published_at: props.post.published_at ? props.post.published_at.slice(0, 16) : '',
});

// Auto-generate slug from title (prefer English, fallback to Russian) - Watch active language if needed
watch(() => [form.title.en, form.title.ru], ([newEn, newRu]) => {
    // Only auto-update slug if it matches the old slug-ified title, 
    // effectively "if user hasn't manually customized it".
    // For now, simpler logic: duplicate behavior from Create.vue but careful not to overwrite existing meaningful slug?
    // In edit mode, usually we don't auto-update slug unless explicitly cleared or requested.
    // Let's keep it simple: manual update only for edit, or auto if empty (unlikely in edit).
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.featured_image = target.files[0];
    }
};

const submit = () => {
    form.post(`/admin/news/${props.post.id}`, {
        forceFormData: true,
    });
};

const deletePost = () => {
    if (confirm('Are you sure you want to delete this news item?')) {
        router.delete(`/admin/news/${props.post.id}`);
    }
};

const breadcrumbs = [
    { title: 'News', href: '/admin/news' },
    { title: 'Edit News', href: `/admin/news/${props.post.id}/edit` },
];

const checkError = (langCode: string) => {
    // Cast errors key to any to avoid strict type checks on dynamic keys
    const errors = form.errors as any;
    return !!(errors[`title.${langCode}`] || errors[`summary.${langCode}`] || errors[`body.${langCode}`]);
};

const checkContent = (langCode: string) => {
    return hasContent(form, 'title', langCode) ||
        hasContent(form, 'summary', langCode) ||
        hasContent(form, 'body', langCode);
};
</script>

<template>

    <Head title="Edit News" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-4xl mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">Edit News</h2>
                <div class="d-flex gap-2">
                    <button @click="deletePost" class="btn btn-outline-danger d-inline-flex align-items-center gap-2">
                        <Trash2 style="width: 1rem; height: 1rem;" />
                        Delete
                    </button>
                    <Link href="/admin/news" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <ArrowLeft style="width: 1rem; height: 1rem;" />
                        Back
                    </Link>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <!-- Global Language Switcher -->
                    <FormLanguageSwitcher v-model="currentLang" :languages="availableLanguages" :has-error="checkError" :has-content="checkContent" />

                    <form @submit.prevent="submit" enctype="multipart/form-data">

                        <!-- Title Field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input v-model="(form.title as any)[currentLang]" type="text" class="form-control" :placeholder="`Enter title (${availableLanguages.find(l => l.code === currentLang)?.label})`" @blur="syncField(form, 'title')">
                            <!-- Use dynamic key access carefully with any cast if needed -->
                            <div v-if="(form.errors as any)[`title.${currentLang}`]" class="text-danger small mt-1">{{ (form.errors as any)[`title.${currentLang}`] }}</div>
                        </div>

                        <!-- Slug Field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input v-model="form.slug" type="text" class="form-control bg-light">
                            <div v-if="form.errors.slug" class="text-danger small mt-1">{{ form.errors.slug }}</div>
                        </div>

                        <!-- Summary Field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Summary</label>
                            <textarea v-model="(form.summary as any)[currentLang]" class="form-control" rows="3" :placeholder="`Enter summary (${availableLanguages.find(l => l.code === currentLang)?.label})`" @blur="syncField(form, 'summary')"></textarea>
                            <div v-if="(form.errors as any)[`summary.${currentLang}`]" class="text-danger small mt-1">{{ (form.errors as any)[`summary.${currentLang}`] }}</div>
                        </div>

                        <!-- Body Field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>

                            <div :key="currentLang">
                                <Editor v-model="(form.body as any)[currentLang]" :placeholder="`Edit your news story here (${availableLanguages.find(l => l.code === currentLang)?.label})...`" />
                            </div>

                            <div v-if="(form.errors as any)[`body.${currentLang}`]" class="text-danger small mt-1">{{ (form.errors as any)[`body.${currentLang}`] }}</div>
                            <div v-if="form.errors.body" class="text-danger small mt-1">{{ form.errors.body }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Featured Image</label>
                                <div v-if="props.post.featured_image_url" class="mb-2">
                                    <img :src="props.post.featured_image_url" alt="Current Image" class="img-thumbnail" style="height: 100px;">
                                </div>
                                <input type="file" @change="handleFileChange" class="form-control" accept="image/*">
                                <div class="form-text">Upload to replace the current image.</div>
                                <div v-if="form.errors.featured_image" class="text-danger small mt-1">{{ form.errors.featured_image }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Topic</label>
                                <select v-model="form.topic_id" class="form-select">
                                    <option value="">Select Topic</option>
                                    <option v-for="topic in topics" :key="topic.id" :value="topic.id">{{ topic.name }}</option>
                                </select>
                                <div v-if="form.errors.topic_id" class="text-danger small mt-1">{{ form.errors.topic_id }}</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Published At</label>
                            <input v-model="form.published_at" type="datetime-local" class="form-control">
                            <div class="form-text">Leave blank to save as draft.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <Link href="/admin/news" class="btn btn-light border">Cancel</Link>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" :disabled="form.processing">
                                <Save style="width: 1.25rem; height: 1.25rem;" />
                                Update News
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.nav-tabs-sm .nav-link {
    font-size: 0.85rem;
    color: #6c757d;
    cursor: pointer;
}

.nav-tabs-sm .nav-link.active {
    color: #0d6efd;
    font-weight: 500;
}
</style>
