<div class="container mx-auto px-4 py-8 mt-20">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-md p-6 lg:p-12">
        <h1 class="text-3xl font-bold mb-6 text-neutral-900 dark:text-white">{{ $page->title }}</h1>
        <div class="prose dark:prose-invert max-w-none text-neutral-700 dark:text-neutral-300 text-black">
            {!! $page->content !!}
        </div>
    </div>
</div>