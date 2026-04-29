import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';

window.initTiptap = function (root) {
    const el = root.querySelector('[data-tiptap-editor]');
    if (!el || el._editor) return;

    const stateInput = root.querySelector('[data-tiptap-input]');
    const placeholder = el.dataset.placeholder || 'Tulis konten…';
    const initial = stateInput?.value || '';

    const editor = new Editor({
        element: el,
        extensions: [
            StarterKit.configure({
                heading: { levels: [1, 2, 3] },
            }),
            Link.configure({ openOnClick: false, HTMLAttributes: { class: 'text-brand-600 underline' } }),
            Image,
            Placeholder.configure({ placeholder }),
        ],
        content: initial,
        editorProps: {
            attributes: {
                class: 'prose prose-sm max-w-none min-h-[180px] px-4 py-3 focus:outline-none',
            },
        },
        onUpdate({ editor }) {
            const html = editor.getHTML();
            if (stateInput) {
                stateInput.value = html;
                stateInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },
    });

    el._editor = editor;

    // Toolbar
    root.querySelectorAll('[data-tiptap-action]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const action = btn.dataset.tiptapAction;
            const chain = editor.chain().focus();
            switch (action) {
                case 'bold': chain.toggleBold().run(); break;
                case 'italic': chain.toggleItalic().run(); break;
                case 'strike': chain.toggleStrike().run(); break;
                case 'h1': chain.toggleHeading({ level: 1 }).run(); break;
                case 'h2': chain.toggleHeading({ level: 2 }).run(); break;
                case 'h3': chain.toggleHeading({ level: 3 }).run(); break;
                case 'paragraph': chain.setParagraph().run(); break;
                case 'bullet': chain.toggleBulletList().run(); break;
                case 'ordered': chain.toggleOrderedList().run(); break;
                case 'blockquote': chain.toggleBlockquote().run(); break;
                case 'code': chain.toggleCode().run(); break;
                case 'codeblock': chain.toggleCodeBlock().run(); break;
                case 'hr': chain.setHorizontalRule().run(); break;
                case 'link': {
                    const prev = editor.getAttributes('link').href;
                    const url = window.prompt('URL', prev || 'https://');
                    if (url === null) break;
                    if (url === '') { chain.unsetLink().run(); break; }
                    chain.extendMarkRange('link').setLink({ href: url }).run();
                    break;
                }
                case 'image': {
                    const url = window.prompt('URL gambar');
                    if (url) chain.setImage({ src: url }).run();
                    break;
                }
                case 'undo': chain.undo().run(); break;
                case 'redo': chain.redo().run(); break;
            }
        });
    });

    // Sync FROM Livewire when state changes externally (e.g. resetForm)
    if (stateInput) {
        const observer = new MutationObserver(() => {
            const v = stateInput.value || '';
            if (v !== editor.getHTML()) editor.commands.setContent(v, false);
        });
        observer.observe(stateInput, { attributes: true, attributeFilter: ['value'] });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tiptap-root]').forEach((r) => window.initTiptap(r));
});

document.addEventListener('livewire:initialized', () => {
    document.querySelectorAll('[data-tiptap-root]').forEach((r) => window.initTiptap(r));
    // Re-init on Livewire DOM updates
    Livewire.hook('morph.added', ({ el }) => {
        if (el.matches?.('[data-tiptap-root]')) window.initTiptap(el);
        el.querySelectorAll?.('[data-tiptap-root]').forEach((r) => window.initTiptap(r));
    });
});
