<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">Manage Blog</h2>
            <p class="text-sm text-neutral-500 mt-1">Create and edit blog posts with SEO settings.</p>
        </div>
        @if ($mode === 'list')
            <button type="button" wire:click="create"
                class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-red-800">
                Create Post
            </button>
        @else
            <button type="button" wire:click="cancel"
                class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:text-neutral-200">
                Back to list
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-800">{{ session('message') }}</div>
    @endif

    @if ($mode === 'list')
        <div class="mb-4">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search posts..."
                class="w-full max-w-md rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
        </div>

        <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Title</th>
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Published</th>
                        <th class="px-4 py-3 text-right font-medium text-neutral-600 dark:text-neutral-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                    @forelse ($posts as $post)
                        <tr>
                            <td class="px-4 py-3 text-neutral-900 dark:text-white">{{ $post->title }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $post->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">
                                {{ optional($post->published_at)->format('M j, Y H:i') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                @if ($post->status === 'published')
                                    <a href="{{ $post->url }}" target="_blank" class="text-primary hover:underline">View</a>
                                @endif
                                <button type="button" wire:click="edit({{ $post->id }})" class="text-neutral-700 hover:underline dark:text-neutral-200">Edit</button>
                                <button type="button" wire:click="delete({{ $post->id }})"
                                    wire:confirm="Delete this blog post?"
                                    class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-neutral-500">No blog posts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $posts->links() }}</div>
    @else
        <form wire:submit.prevent="save" class="space-y-6 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Title</label>
                    <input type="text" wire:model.live="title"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Slug</label>
                    <input type="text" wire:model="slug"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Excerpt</label>
                <textarea wire:model="excerpt" rows="2"
                    class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"></textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Status</label>
                    <select wire:model="status"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Publish date</label>
                    <input type="datetime-local" wire:model="published_at"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Featured image</label>
                <input type="file" wire:model="featured_image" accept="image/*"
                    class="w-full text-sm text-neutral-700 dark:text-neutral-200" />
                @error('featured_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @if ($featured_image)
                    <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview" class="mt-3 h-32 rounded object-cover" />
                @elseif ($existing_featured_image)
                    <img src="{{ \App\Models\BlogPost::make(['featured_image' => $existing_featured_image])->featuredImageUrl() }}" alt="Current" class="mt-3 h-32 rounded object-cover" />
                @endif
            </div>

            @include('livewire.admin.partials.quill-editor', ['label' => 'Post content'])

            <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                <h3 class="mb-3 text-lg font-semibold text-neutral-900 dark:text-white">SEO settings</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">Meta title</label>
                        <input type="text" wire:model="meta_title"
                            class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">Meta description</label>
                        <textarea wire:model="meta_description" rows="3"
                            class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Meta keywords</label>
                        <input type="text" wire:model="meta_keywords"
                            class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Canonical URL</label>
                        <input type="text" wire:model="canonical_url"
                            class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">OG image URL</label>
                        <input type="text" wire:model="og_image"
                            class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-red-800">
                    {{ $editingId ? 'Update Post' : 'Create Post' }}
                </button>
                <button type="button" wire:click="cancel"
                    class="rounded-lg border border-neutral-300 px-5 py-2.5 text-sm font-medium text-neutral-700 dark:border-neutral-600 dark:text-neutral-200">
                    Cancel
                </button>
            </div>
        </form>
    @endif
</div>
