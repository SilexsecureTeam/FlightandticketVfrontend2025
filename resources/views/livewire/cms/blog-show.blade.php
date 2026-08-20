<div>
    <article class="container mx-auto px-4 py-8 mt-20 mb-16 text-black">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            @if ($post->featuredImageUrl())
                <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="w-full max-h-[420px] object-cover">
            @endif
            <div class="p-6 lg:p-10">
                <p class="text-sm text-neutral-500 mb-3">
                    {{ optional($post->published_at)->format('F j, Y') }}
                    @if ($post->author)
                        · {{ $post->author->name }}
                    @endif
                </p>
                <h1 class="text-3xl md:text-4xl font-bold text-neutral-900 mb-6">{{ $post->title }}</h1>
                <div class="prose max-w-none text-neutral-800 cms-content">
                    {!! $post->content !!}
                </div>
                <div class="mt-10 pt-6 border-t border-neutral-200">
                    <a href="{{ route('blog.index') }}" class="text-primary hover:underline">← Back to blog</a>
                </div>
            </div>
        </div>
    </article>

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
