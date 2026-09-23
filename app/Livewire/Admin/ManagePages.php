<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManagePages extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $mode = 'list';
    public ?int $editingId = null;

    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $status = 'published';
    public string $menu_location = 'none';
    public int $menu_order = 0;
    public string $custom_url = '';
    public string $meta_title = '';
    public string $meta_description = '';
    public string $meta_keywords = '';
    public string $og_image = '';
    public string $canonical_url = '';

    public string $site_meta_title = '';
    public string $site_meta_description = '';
    public string $site_meta_keywords = '';
    public $site_favicon = null;
    public ?string $existing_favicon = null;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->mode = 'form';
        $this->editingId = null;
        $this->dispatch('contentChanged', content: '');
    }

    public function editSiteMeta(): void
    {
        $settings = SiteSetting::current();

        $this->site_meta_title = (string) ($settings->meta_title ?? '');
        $this->site_meta_description = (string) ($settings->meta_description ?? '');
        $this->site_meta_keywords = (string) ($settings->meta_keywords ?? '');
        $this->existing_favicon = $settings->favicon;
        $this->site_favicon = null;
        $this->mode = 'site-meta';
        $this->resetValidation();
    }

    public function saveSiteMeta(): void
    {
        $validated = $this->validate([
            'site_meta_title' => 'required|string|max:255',
            'site_meta_description' => 'required|string|max:500',
            'site_meta_keywords' => 'nullable|string|max:500',
            'site_favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg,gif,webp,svg|max:2048',
        ]);

        $settings = SiteSetting::current();
        $faviconPath = $settings->favicon;

        if ($this->site_favicon) {
            $extension = strtolower($this->site_favicon->getClientOriginalExtension() ?: 'png');
            $filename = 'favicon-' . Str::uuid() . '.' . $extension;
            $directory = public_path('uploads/site');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $source = $this->site_favicon->getRealPath();
            if ($source && is_file($source)) {
                copy($source, $directory . DIRECTORY_SEPARATOR . $filename);
                $faviconPath = 'uploads/site/' . $filename;

                if ($this->existing_favicon && Str::startsWith($this->existing_favicon, 'uploads/site/')) {
                    $old = public_path($this->existing_favicon);
                    if (is_file($old)) {
                        @unlink($old);
                    }
                }
            }
        }

        $settings->update([
            'meta_title' => $validated['site_meta_title'],
            'meta_description' => $validated['site_meta_description'],
            'meta_keywords' => $validated['site_meta_keywords'] ?: null,
            'favicon' => $faviconPath,
        ]);

        $settings->refresh();
        $settings->publishRootFavicon();
        SiteSetting::clearCache();

        session()->flash('message', 'Website meta head updated successfully.');
        $this->mode = 'list';
        $this->resetSiteMetaForm();
    }

    public function removeFavicon(): void
    {
        $settings = SiteSetting::current();

        if ($settings->favicon && Str::startsWith($settings->favicon, 'uploads/site/')) {
            $old = public_path($settings->favicon);
            if (is_file($old)) {
                @unlink($old);
            }
        }

        $settings->update(['favicon' => null]);
        $settings->refresh();
        $settings->publishRootFavicon();
        SiteSetting::clearCache();

        $this->existing_favicon = null;
        $this->site_favicon = null;

        session()->flash('message', 'Website icon removed.');
    }

    public function edit(int $id): void
    {
        $page = Page::findOrFail($id);

        $this->editingId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $this->normalizeContentImages((string) $page->content);
        $this->status = $page->status ?: 'published';
        $this->menu_location = $page->menu_location ?: 'none';
        $this->menu_order = (int) $page->menu_order;
        $this->custom_url = (string) ($page->custom_url ?? '');
        $this->meta_title = (string) ($page->meta_title ?? '');
        $this->meta_description = (string) ($page->meta_description ?? '');
        $this->meta_keywords = (string) ($page->meta_keywords ?? '');
        $this->og_image = (string) ($page->og_image ?? '');
        $this->canonical_url = (string) ($page->canonical_url ?? '');
        $this->mode = 'form';

        $this->dispatch('contentChanged', content: $this->content);
    }

    public function updatedTitle($value): void
    {
        if ($this->editingId) {
            return;
        }

        $this->slug = Str::slug((string) $value);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($this->editingId),
            ],
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'menu_location' => 'required|in:none,top,footer,both',
            'menu_order' => 'nullable|integer|min:0|max:9999',
            'custom_url' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|string|max:500',
        ]);

        $payload = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['content'] ?? '',
            'status' => $validated['status'],
            'menu_location' => $validated['menu_location'],
            'menu_order' => $validated['menu_order'] ?? 0,
            'custom_url' => $validated['custom_url'] ?: null,
            'meta_title' => $validated['meta_title'] ?: null,
            'meta_description' => $validated['meta_description'] ?: null,
            'meta_keywords' => $validated['meta_keywords'] ?: null,
            'og_image' => $validated['og_image'] ?: null,
            'canonical_url' => $validated['canonical_url'] ?: null,
        ];

        if ($this->editingId) {
            $page = Page::findOrFail($this->editingId);
            $page->update($payload);
            session()->flash('message', 'Page updated successfully.');
        } else {
            Page::create($payload);
            session()->flash('message', 'Page created successfully.');
        }

        $this->mode = 'list';
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $page = Page::findOrFail($id);

        if ($page->is_system) {
            session()->flash('error', 'System pages cannot be deleted. You can unpublish or hide them from menus instead.');
            return;
        }

        $page->delete();
        session()->flash('message', 'Page deleted successfully.');

        if ($this->editingId === $id) {
            $this->mode = 'list';
            $this->resetForm();
        }
    }

    public function cancel(): void
    {
        $this->mode = 'list';
        $this->resetForm();
        $this->resetSiteMetaForm();
    }

    protected function normalizeContentImages(string $content): string
    {
        $content = preg_replace('#https?://[^"\']+/storage/cms/#i', '/uploads/cms/', $content) ?? $content;

        return str_replace('/storage/cms/', '/uploads/cms/', $content);
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->status = 'published';
        $this->menu_location = 'none';
        $this->menu_order = 0;
        $this->custom_url = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->og_image = '';
        $this->canonical_url = '';
        $this->resetValidation();
    }

    protected function resetSiteMetaForm(): void
    {
        $this->site_meta_title = '';
        $this->site_meta_description = '';
        $this->site_meta_keywords = '';
        $this->site_favicon = null;
        $this->existing_favicon = null;
        $this->resetValidation();
    }

    public function render()
    {
        $pages = Page::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('menu_order')
            ->orderBy('title')
            ->paginate(15);

        return view('livewire.admin.manage-pages', [
            'pages' => $pages,
            'siteSettings' => SiteSetting::current(),
        ])->layout('components.layouts.admin');
    }
}
