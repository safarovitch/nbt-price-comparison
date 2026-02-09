<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { watch, onBeforeUnmount } from 'vue'
import {
    Bold, Italic, Strikethrough, Heading1, Heading2, Heading3,
    List, ListOrdered, Quote, Code, Undo, Redo
} from 'lucide-vue-next'

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
    ],
    editorProps: {
        attributes: {
            class: 'form-control prose max-w-none min-h-[200px] p-3 focus:outline-none',
            style: 'min-height: 200px;',
        },
    },
    onUpdate: () => {
        emit('update:modelValue', editor.value?.getHTML())
    },
})

watch(() => props.modelValue, (newValue) => {
    // Only update content if it's different to prevent cursor jumping
    const isSame = editor.value?.getHTML() === newValue
    if (!isSame) {
        editor.value?.commands.setContent(newValue, false)
    }
})

onBeforeUnmount(() => {
    editor.value?.destroy()
})
</script>

<template>
    <div class="rich-editor border rounded">
        <div v-if="editor" class="toolbar bg-light border-bottom p-2 d-flex flex-wrap gap-1">
            <button @click="editor.chain().focus().toggleBold().run()" :class="{ 'active': editor.isActive('bold') }" class="btn btn-sm btn-light border" type="button" title="Bold">
                <Bold class="w-4 h-4" />
            </button>
            <button @click="editor.chain().focus().toggleItalic().run()" :class="{ 'active': editor.isActive('italic') }" class="btn btn-sm btn-light border" type="button" title="Italic">
                <Italic class="w-4 h-4" />
            </button>
            <button @click="editor.chain().focus().toggleStrike().run()" :class="{ 'active': editor.isActive('strike') }" class="btn btn-sm btn-light border" type="button" title="Strike">
                <Strikethrough class="w-4 h-4" />
            </button>
            <div class="vr mx-1"></div>
            <button @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'active': editor.isActive('heading', { level: 2 }) }" class="btn btn-sm btn-light border" type="button" title="Heading 2">
                <Heading2 class="w-4 h-4" />
            </button>
            <button @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'active': editor.isActive('heading', { level: 3 }) }" class="btn btn-sm btn-light border" type="button" title="Heading 3">
                <Heading3 class="w-4 h-4" />
            </button>
            <div class="vr mx-1"></div>
            <button @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'active': editor.isActive('bulletList') }" class="btn btn-sm btn-light border" type="button" title="Bullet List">
                <List class="w-4 h-4" />
            </button>
            <button @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'active': editor.isActive('orderedList') }" class="btn btn-sm btn-light border" type="button" title="Ordered List">
                <ListOrdered class="w-4 h-4" />
            </button>
            <button @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'active': editor.isActive('blockquote') }" class="btn btn-sm btn-light border" type="button" title="Blockquote">
                <Quote class="w-4 h-4" />
            </button>
            <div class="vr mx-1"></div>
            <button @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" class="btn btn-sm btn-light border" type="button" title="Undo">
                <Undo class="w-4 h-4" />
            </button>
            <button @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" class="btn btn-sm btn-light border" type="button" title="Redo">
                <Redo class="w-4 h-4" />
            </button>
        </div>
        <editor-content :editor="editor" class="p-0" />
    </div>
</template>

<style scoped>
.rich-editor :deep(.ProseMirror) {
    min-height: 200px;
    padding: 1rem;
    outline: none;
}

.rich-editor :deep(.ProseMirror ul),
.rich-editor :deep(.ProseMirror ol) {
    padding-left: 1.5rem;
}

.rich-editor :deep(.ProseMirror blockquote) {
    border-left: 3px solid #dee2e6;
    padding-left: 1rem;
    color: #6c757d;
}

button.active {
    background-color: #e9ecef;
    border-color: #ced4da;
}
</style>
