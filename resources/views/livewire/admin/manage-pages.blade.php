<div class="p-6 bg-white dark:bg-neutral-900 rounded-lg shadow max-w-4xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6 text-neutral-900 dark:text-white">Manage Pages</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
            role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-6">
        <label for="page-selector" class="block mb-2 text-sm font-medium text-neutral-900 dark:text-white">Select Page
            to Edit</label>
        <select id="page-selector" wire:change="selectPage($event.target.value)"
            class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white">
            @foreach($pages as $p)
                <option value="{{ $p->id }}" {{ $p->id == $selectedPageId ? 'selected' : '' }}>{{ $p->title }}
                    ({{ $p->slug }})</option>
            @endforeach
        </select>
    </div>

    <div class="mb-6">
        <label for="title" class="block mb-2 text-sm font-medium text-neutral-900 dark:text-white">Page Title</label>
        <input type="text" id="title" wire:model="title"
            class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
            required>
    </div>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <div class="mb-6" wire:ignore>
        <label for="editor" class="block mb-2 text-sm font-medium text-neutral-900 dark:text-white">Content</label>
        <div x-data="{
                content: @entangle('content'),
                quill: null
            }" x-init="
                quill = new Quill($refs.editor, {
                    theme: 'snow'
                });
                quill.root.innerHTML = content || '';
                quill.on('text-change', function () {
                    content = quill.root.innerHTML;
                });
                $watch('content', value => {
                    if (quill.root.innerHTML !== value) {
                        quill.root.innerHTML = value || '';
                    }
                });
            ">
            <div x-ref="editor" class="bg-white text-neutral-900" style="height: 400px;"></div>
        </div>
    </div>

    <button wire:click="save"
        class="text-white bg-primary hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
        Save Page
    </button>
</div>