<div class="container mx-auto px-4 py-8 mt-20 mb-16 text-black">
    <div class="mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-neutral-900 mb-3">Travel Blog</h1>
        <p class="text-neutral-600 max-w-2xl">Tips, deals, and updates from Flightandticketing.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($posts as $post)
            <a href="{{ $post->url }}" class="group rounded-xl border border-neutral-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition">
                @if ($post->featured_image)
                    <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="h-48 w-full object-cover">
                @else
                    <div class="h-48 w-full bg-neutral-100"></div>
                @endif
                <div class="p-5">
                    <p class="text-xs text-neutral-500 mb-2">
                        {{ optional($post->published_at)->format('M j, Y') }}
                    </p>
                    <h2 class="text-lg font-semibold text-neutral-900 group-hover:text-primary mb-2">{{ $post->title }}</h2>
                    <p class="text-sm text-neutral-600 line-clamp-3">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
                </div>
            </a>
        @empty
            <div class="md:col-span-2 lg:col-span-3 rounded-lg border border-dashed border-neutral-300 p-10 text-center text-neutral-500">
                No blog posts published yet.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
