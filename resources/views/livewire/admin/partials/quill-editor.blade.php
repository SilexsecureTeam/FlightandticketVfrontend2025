<div class="mb-2" wire:key="quill-editor-{{ $editingId ?? 'new' }}-{{ $label ?? 'content' }}">
    <label class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">{{ $label ?? 'Content' }}</label>

    <div
        wire:ignore
        x-data="cmsQuillEditor({
            content: @entangle('content'),
            uploadUrl: @js(route('admin.cms.upload')),
            csrf: @js(csrf_token())
        })"
        class="rounded-lg overflow-hidden border border-neutral-300 bg-white"
    >
        <div x-ref="toolbar" class="bg-neutral-50 border-b border-neutral-300">
            <span class="ql-formats">
                <select class="ql-header">
                    <option selected></option>
                    <option value="1"></option>
                    <option value="2"></option>
                    <option value="3"></option>
                    <option value="4"></option>
                </select>
            </span>
            <span class="ql-formats">
                <button class="ql-bold" type="button"></button>
                <button class="ql-italic" type="button"></button>
                <button class="ql-underline" type="button"></button>
                <button class="ql-strike" type="button"></button>
            </span>
            <span class="ql-formats">
                <select class="ql-color"></select>
                <select class="ql-background"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-list" value="ordered" type="button"></button>
                <button class="ql-list" value="bullet" type="button"></button>
            </span>
            <span class="ql-formats">
                <select class="ql-align"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-blockquote" type="button"></button>
                <button class="ql-code-block" type="button"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-link" type="button"></button>
                <button class="ql-image" type="button"></button>
                <button class="ql-video" type="button"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-clean" type="button"></button>
            </span>
        </div>
        <div x-ref="editor" class="bg-white text-neutral-900 min-h-[360px]"></div>
    </div>

    @error('content')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
