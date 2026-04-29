@props([
    'model' => null,
    'placeholder' => 'Tulis konten…',
    'value' => '',
])
@php
    $id = 'editor_' . uniqid();
    $val = $value;
@endphp
<div data-tiptap-root class="rounded-xl border border-slate-200 bg-white focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-200 transition" wire:ignore>
    <div class="flex flex-wrap gap-1 border-b border-slate-100 p-1.5 bg-slate-50/60 rounded-t-xl text-xs">
        @php
            $btn = 'rounded-md px-2 py-1 hover:bg-slate-200 text-slate-600 hover:text-slate-900';
        @endphp
        <button type="button" data-tiptap-action="bold" class="{{ $btn }} font-bold" title="Bold">B</button>
        <button type="button" data-tiptap-action="italic" class="{{ $btn }} italic" title="Italic">I</button>
        <button type="button" data-tiptap-action="strike" class="{{ $btn }} line-through" title="Strike">S</button>
        <span class="w-px bg-slate-200 mx-0.5"></span>
        <button type="button" data-tiptap-action="paragraph" class="{{ $btn }}">P</button>
        <button type="button" data-tiptap-action="h1" class="{{ $btn }} font-bold">H1</button>
        <button type="button" data-tiptap-action="h2" class="{{ $btn }} font-bold">H2</button>
        <button type="button" data-tiptap-action="h3" class="{{ $btn }} font-bold">H3</button>
        <span class="w-px bg-slate-200 mx-0.5"></span>
        <button type="button" data-tiptap-action="bullet" class="{{ $btn }}" title="Bullet list">•</button>
        <button type="button" data-tiptap-action="ordered" class="{{ $btn }}" title="Numbered list">1.</button>
        <button type="button" data-tiptap-action="blockquote" class="{{ $btn }}" title="Quote">❝</button>
        <button type="button" data-tiptap-action="code" class="{{ $btn }} font-mono" title="Inline code">‹›</button>
        <span class="w-px bg-slate-200 mx-0.5"></span>
        <button type="button" data-tiptap-action="link" class="{{ $btn }}" title="Link">🔗</button>
        <button type="button" data-tiptap-action="image" class="{{ $btn }}" title="Image">🖼</button>
        <button type="button" data-tiptap-action="hr" class="{{ $btn }}" title="Garis">―</button>
        <span class="w-px bg-slate-200 mx-0.5"></span>
        <button type="button" data-tiptap-action="undo" class="{{ $btn }}" title="Undo">↶</button>
        <button type="button" data-tiptap-action="redo" class="{{ $btn }}" title="Redo">↷</button>
    </div>
    <div data-tiptap-editor data-placeholder="{{ $placeholder }}"></div>
    <input
        type="hidden"
        data-tiptap-input
        @if($model) wire:model="{{ $model }}" @endif
        value="{{ $val }}"
    />
</div>
