<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                @if ($mode === 'site-meta')
                    Website Meta Head
                @else
                    Manage Pages
                @endif
            </h2>
            <p class="text-sm text-neutral-500 mt-1">
                @if ($mode === 'site-meta')
                    Update the default site title, favicon, keywords, and description used in the website &lt;head&gt;.
                @else
                    Create pages, set menu placement, and configure SEO.
                @endif
            </p>
        </div>
        @if ($mode === 'list')
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="editSiteMeta"
                    class="inline-flex items-center justify-center rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-800 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                    Update Meta Head
                </button>
                <button type="button" wire:click="create"
                    class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-red-800">
                    Create Page
                </button>
            </div>
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
    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    @if ($mode === 'list')
        <div class="mb-4 rounded-lg border border-neutral-200 bg-neutral-50 p-4 text-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-medium text-neutral-900 dark:text-white">Current site meta</p>
                    <p class="text-neutral-600 dark:text-neutral-300">{{ $siteSettings->title() }}</p>
                </div>
                <button type="button" wire:click="editSiteMeta" class="text-primary hover:underline">Edit meta head</button>
            </div>
        </div>

        <div class="mb-4">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search pages..."
                class="w-full max-w-md rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
        </div>

        <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Title</th>
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Slug</th>
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Menu</th>
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-neutral-600 dark:text-neutral-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                    @forelse ($pages as $page)
                        <tr>
                            <td class="px-4 py-3 text-neutral-900 dark:text-white">
                                {{ $page->title }}
                                @if ($page->is_system)
                                    <span class="ml-1 rounded bg-neutral-100 px-1.5 py-0.5 text-[10px] uppercase text-neutral-500">system</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">{{ $page->slug }}</td>
                            <td class="px-4 py-3 capitalize text-neutral-600 dark:text-neutral-300">{{ $page->menu_location }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $page->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $page->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ $page->url }}" target="_blank" class="text-primary hover:underline">View</a>
                                <button type="button" wire:click="edit({{ $page->id }})" class="text-neutral-700 hover:underline dark:text-neutral-200">Edit</button>
                                @unless ($page->is_system)
                                    <button type="button" wire:click="delete({{ $page->id }})"
                                        wire:confirm="Delete this page?"
                                        class="text-red-600 hover:underline">Delete</button>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-neutral-500">No pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pages->links() }}</div>
    @elseif ($mode === 'site-meta')
        <form wire:submit.prevent="saveSiteMeta" class="space-y-6 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Site title</label>
                <input type="text" wire:model="site_meta_title"
                    class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"
                    placeholder="Website title shown in browser tabs and search results" />
                @error('site_meta_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Meta description</label>
                <textarea wire:model="site_meta_description" rows="3"
                    class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"
                    placeholder="Default description used when a page does not set its own SEO description"></textarea>
                @error('site_meta_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Meta keywords</label>
                <input type="text" wire:model="site_meta_keywords"
                    class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"
                    placeholder="cheap flights, flight booking, airline tickets" />
                @error('site_meta_keywords') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Website icon (favicon)</label>
                <input type="file" wire:model="site_favicon" accept=".ico,image/png,image/jpeg,image/gif,image/webp,image/svg+xml"
                    class="w-full text-sm text-neutral-700 dark:text-neutral-200" />
                <div wire:loading wire:target="site_favicon" class="mt-2 text-xs text-neutral-500">Uploading icon...</div>
                @error('site_favicon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                @if ($site_favicon)
                    <img src="{{ $site_favicon->temporaryUrl() }}" alt="New favicon preview" class="mt-3 h-12 w-12 rounded object-contain border border-neutral-200 bg-white p-1" />
                @elseif ($existing_favicon)
                    <div class="mt-3 flex items-center gap-3">
                        <img src="{{ '/' . ltrim($existing_favicon, '/') }}" alt="Current favicon" class="h-12 w-12 rounded object-contain border border-neutral-200 bg-white p-1" />
                        <button type="button" wire:click="removeFavicon" wire:confirm="Remove the current website icon?"
                            class="text-sm text-red-600 hover:underline">Remove icon</button>
                    </div>
                @endif
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-red-800">
                    Save Meta Head
                </button>
                <button type="button" wire:click="cancel"
                    class="rounded-lg border border-neutral-300 px-5 py-2.5 text-sm font-medium text-neutral-700 dark:border-neutral-600 dark:text-neutral-200">
                    Cancel
                </button>
            </div>
        </form>
    @else
        <form wire:submit.prevent="save" class="space-y-6 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Title</label>
                    <input type="text" wire:model.live="title"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Slug</label>
                    <input type="text" wire:model="slug"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Status</label>
                    <select wire:model="status"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Add to Menu</label>
                    <select wire:model="menu_location"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="none">None</option>
                        <option value="top">Top menu</option>
                        <option value="footer">Footer</option>
                        <option value="both">Top &amp; Footer</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Menu order</label>
                    <input type="number" min="0" wire:model="menu_order"
                        class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-800 dark:text-neutral-200">Custom URL (optional)</label>
                <input type="text" wire:model="custom_url" placeholder="/ or https://example.com"
                    class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                <p class="mt-1 text-xs text-neutral-500">If set, menu links use this URL instead of the CMS page URL.</p>
            </div>

            @include('livewire.admin.partials.quill-editor', ['label' => 'Page content'])

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
                        <input type="text" wire:model="og_image" placeholder="https://... or /storage/..."
                            class="w-full rounded-lg border border-neutral-300 bg-neutral-50 px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-red-800">
                    {{ $editingId ? 'Update Page' : 'Create Page' }}
                </button>
                <button type="button" wire:click="cancel"
                    class="rounded-lg border border-neutral-300 px-5 py-2.5 text-sm font-medium text-neutral-700 dark:border-neutral-600 dark:text-neutral-200">
                    Cancel
                </button>
            </div>
        </form>
    @endif
</div>
