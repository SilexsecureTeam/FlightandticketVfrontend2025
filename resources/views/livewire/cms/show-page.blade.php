<div>
    <div class="container mx-auto px-4 py-8 mt-20 mb-16">
        <div class="bg-white rounded-lg shadow-md p-6 lg:p-12">
            <h1 class="text-3xl font-bold mb-6 text-neutral-900">{{ $page->title }}</h1>
            <div class="prose max-w-none text-neutral-800 cms-content">
                {!! $pageContent !!}
            </div>
        </div>
    </div>

    <style>
        .cms-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }
        .cms-content a {
            color: #97060B;
            text-decoration: underline;
        }
    </style>
</div>
