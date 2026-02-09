<script setup lang="ts">
import { computed } from 'vue';

interface Block {
    id: string;
    type: string;
    data: any;
}

const props = defineProps<{
    content: string | object | null;
}>();

const blocks = computed<Block[]>(() => {
    if (!props.content) return [];

    let data;
    if (typeof props.content === 'string') {
        try {
            const parsed = JSON.parse(props.content);
            data = parsed.blocks || [];
        } catch (e) {
            // If parsing fails, it might be legacy HTML content. 
            // We'll handle it delicately or just return empty.
            // For now, let's treat it as a single HTML block if we wanted, 
            // but Editor.js renderer expects blocks.
            console.warn('BlockRenderer: Content is not valid JSON blocks.', e);
            return [];
        }
    } else {
        data = (props.content as any).blocks || [];
    }
    return data;
});
</script>

<template>
    <div class="prose prose-lg max-w-none">
        <template v-for="block in blocks" :key="block.id">

            <!-- Paragraph -->
            <p v-if="block.type === 'paragraph'" v-html="block.data.text" class="mb-4"></p>

            <!-- Header -->
            <h2 v-else-if="block.type === 'header' && block.data.level === 2" class="h2 mt-4 mb-2 font-bold">{{ block.data.text }}</h2>
            <h3 v-else-if="block.type === 'header' && block.data.level === 3" class="h3 mt-3 mb-2 font-bold">{{ block.data.text }}</h3>
            <h4 v-else-if="block.type === 'header' && block.data.level === 4" class="h4 mt-3 mb-2 font-bold">{{ block.data.text }}</h4>
            <h5 v-else-if="block.type === 'header'" class="h5 mt-2 mb-1">{{ block.data.text }}</h5>

            <!-- List -->
            <ul v-else-if="block.type === 'list' && block.data.style === 'unordered'" class="list-disc pl-5 mb-4">
                <li v-for="(item, index) in block.data.items" :key="index" v-html="item"></li>
            </ul>
            <ol v-else-if="block.type === 'list' && block.data.style === 'ordered'" class="list-decimal pl-5 mb-4">
                <li v-for="(item, index) in block.data.items" :key="index" v-html="item"></li>
            </ol>

            <!-- Image -->
            <figure v-else-if="block.type === 'image'" class="my-5">
                <img :src="block.data.file.url" :alt="block.data.caption || 'Image'" class="img-fluid rounded shadow-sm w-100">
                <figcaption v-if="block.data.caption" class="text-center text-muted small mt-2">{{ block.data.caption }}</figcaption>
            </figure>

            <!-- Quote -->
            <blockquote v-else-if="block.type === 'quote'" class="border-start border-4 border-primary ps-3 my-4 fst-italic">
                <p class="mb-1" v-html="block.data.text"></p>
                <footer v-if="block.data.caption" class="small text-muted">— {{ block.data.caption }}</footer>
            </blockquote>

            <!-- Code -->
            <pre v-else-if="block.type === 'code'" class="bg-light p-3 rounded mb-4 overflow-auto"><code>{{ block.data.code }}</code></pre>

            <!-- Delimiter -->
            <hr v-else-if="block.type === 'delimiter'" class="my-5 opacity-25" />

            <!-- Fallback for unsupported blocks -->
            <div v-else class="text-danger small border border-danger p-2 mb-2">
                Unsupported block type: {{ block.type }}
            </div>

        </template>

        <!-- Legacy/Fallback content if blocks are empty but content exists (e.g. old HTML) -->
        <div v-if="blocks.length === 0 && typeof content === 'string' && content.length > 0" v-html="content"></div>
    </div>
</template>
