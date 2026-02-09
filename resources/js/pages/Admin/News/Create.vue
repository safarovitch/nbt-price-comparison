<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Editor from '@/components/Editor.vue';
import FormLanguageSwitcher from '@/components/FormLanguageSwitcher.vue';
import { useTranslatableForm } from '@/composables/useTranslatableForm';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { watch } from 'vue';

interface Topic {
    id: string;
    name: string;
}

const props = defineProps<{ topics: Topic[]; }>();

const { currentLang, availableLanguages, syncField, hasContent } = useTranslatableForm('ru');

const form = useForm({
    title: { ru: '', en: '', tj: '' },
    slug: '',
    summary: { ru: '', en: '', tj: '' },
    body: { ru: '', en: '', tj: '' },
    featured_image: null as File | null,
    topic_id: '',
    published_at: '',
});

// Auto-generate slug from title (prefer English, fallback to Russian) - Watch specific language changes if needed, 
// or just watch the whole title object.
watch(() => [form.title.en, form.title.ru], ([newEn, newRu]) => {
    const source = newEn || newRu;
    if ((!form.slug || form.slug === '') && source) {
        form.slug = source
            .toLowerCase()
            .replace(/ /g, '-')
            .replace(/[^\w-]+/g, '');
    }
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.featured_image = target.files[0];
    }
};

const submit = () => {
    form.post('/admin/news', {
        forceFormData: true,
    });
};

const breadcrumbs = [
    { title: 'News', href: '/admin/news' },
    { title: 'Add News', href: '/admin/news/create' },
];

const checkError = (langCode: string) => {
    return !!((form.errors as any)[`title.${langCode}`] ||
        (form.errors as any)[`summary.${langCode}`] ||
        (form.errors as any)[`body.${langCode}`]);
};

const checkContent = (langCode: string) => {
    return hasContent(form, 'title', langCode) ||
        hasContent(form, 'summary', langCode) ||
        hasContent(form, 'body', langCode);
};
</script>

<template>

    <Head title="Add News" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-4xl mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">Add News</h2>
                <Link href="/admin/news" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    <ArrowLeft style="width: 1rem; height: 1rem;" />
                    Back
                </Link>
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
                            <div v-if="(form.errors as any)['title.' + currentLang]" class="text-danger small mt-1">{{ (form.errors as any)['title.' + currentLang] }}</div>
                        </div>

                        <!-- Slug Field (Language Neutral) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input v-model="form.slug" type="text" class="form-control bg-light" placeholder="auto-generated-slug">
                            <div class="form-text small">Leave empty to auto-generate from title.</div>
                            <div v-if="form.errors.slug" class="text-danger small mt-1">{{ form.errors.slug }}</div>
                        </div>

                        <!-- Summary Field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Summary</label>
                            <textarea v-model="(form.summary as any)[currentLang]" class="form-control" rows="3" :placeholder="`Enter summary (${availableLanguages.find(l => l.code === currentLang)?.label})`" @blur="syncField(form, 'summary')"></textarea>
                            <div v-if="(form.errors as any)['summary.' + currentLang]" class="text-danger small mt-1">{{ (form.errors as any)['summary.' + currentLang] }}</div>
                        </div>

                        <!-- Body Field (Rich Editor) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>

                            <!-- 
                                We render the Editor for the current language. 
                                Using key=currentLang forces the component to re-init when language changes, 
                                ensuring the editor loads the correct content.
                            -->
                            <div :key="currentLang">
                                <Editor v-model="(form.body as any)[currentLang]" :placeholder="`Write your news story here (${availableLanguages.find(l => l.code === currentLang)?.label})...`" />
                            </div>

                            <div v-if="(form.errors as any)['body.' + currentLang]" class="text-danger small mt-1">{{ (form.errors as any)['body.' + currentLang] }}</div>
                            <div v-if="form.errors.body" class="text-danger small mt-1">{{ form.errors.body }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" @change="handleFileChange" class="form-control" accept="image/*">
                                <div class="form-text">Upload an image for the post header/thumbnail.</div>
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
                                Save News
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
