<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';
import EditorJS, { OutputData } from '@editorjs/editorjs';
// @ts-ignore
import Header from '@editorjs/header';
// @ts-ignore
import List from '@editorjs/list';
// @ts-ignore
import ImageTool from '@editorjs/image';
// @ts-ignore
import Quote from '@editorjs/quote';
// @ts-ignore
import CodeTool from '@editorjs/code';
// @ts-ignore
import Marker from '@editorjs/marker';
// @ts-ignore
import InlineCode from '@editorjs/inline-code';
// @ts-ignore
import Underline from '@editorjs/underline';

const props = defineProps({
    modelValue: {
        type: [Object, String],
        default: () => ({}),
    },
    placeholder: {
        type: String,
        default: 'Let`s write an awesome story!',
    },
    readOnly: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String,
        default: () => `editorjs-${Math.random().toString(36).substr(2, 9)}`,
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = ref<EditorJS | null>(null);

const initEditor = () => {
    let initialData = props.modelValue;
    if (typeof initialData === 'string' && initialData.length > 0) {
        try {
            initialData = JSON.parse(initialData);
        } catch (e) {
            console.warn('Initial data is not valid JSON, creating empty block or raw html block if needed', e);
            // Fallback for existing HTML content?
            // For now, we might just start empty if it's not valid JSON blocks
            initialData = undefined;
        }
    }

    editor.value = new EditorJS({
        holder: props.id,
        placeholder: props.placeholder,
        readOnly: props.readOnly,
        data: initialData as OutputData,
        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: 'Enter a header',
                    levels: [2, 3, 4],
                    defaultLevel: 2,
                },
            },
            list: {
                class: List,
                inlineToolbar: true,
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: '/admin/upload/image', // Your backend file upload endpoint
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content,
                    },
                },
            },
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Enter a quote',
                    captionPlaceholder: 'Quote\'s author',
                },
            },
            marker: {
                class: Marker,
                shortcut: 'CMD+SHIFT+M',
            },
            code: CodeTool,
            inlineCode: {
                class: InlineCode,
                shortcut: 'CMD+SHIFT+C',
            },
            underline: Underline,
        },
        onChange: async () => {
            const data = await editor.value?.save();
            emit('update:modelValue', JSON.stringify(data)); // Emit as string to store in DB
        },
    });
};

onMounted(() => {
    initEditor();
});

onBeforeUnmount(() => {
    if (editor.value && typeof editor.value.destroy === 'function') {
        editor.value.destroy();
        editor.value = null;
    }
});
</script>

<template>
    <div :id="id" ref="editorContainer" class="editor-js-container border rounded p-3 bg-white"></div>
</template>

<style>
/* Scoped doesn't work well with Editor.js plugins injected at root, so using global style in component */
.editor-js-container {
    min-height: 300px;
}

.codex-editor__redactor {
    padding-bottom: 50px !important;
}
</style>
