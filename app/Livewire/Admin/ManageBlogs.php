<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageBlogs extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $mode = 'list';
    public ?int $editingId = null;

    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';
    public string $status = 'draft';
    public ?string $published_at = null;
    public $featured_image;
    public ?string $existing_featured_image = null;
    public string $meta_title = '';
    public string $meta_description = '';
    public string $meta_keywords = '';
    public string $og_image = '';
    public string $canonical_url = '';

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

    public function edit(int $id): void
    {
        $post = BlogPost::findOrFail($id);

        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = (string) ($post->excerpt ?? '');
        $this->content = (string) $post->content;
        $this->status = $post->status ?: 'draft';
        $this->published_at = optional($post->published_at)?->format('Y-m-d\TH:i');
        $this->existing_featured_image = $post->featured_image;
        $this->featured_image = null;
        $this->meta_title = (string) ($post->meta_title ?? '');
        $this->meta_description = (string) ($post->meta_description ?? '');
        $this->meta_keywords = (string) ($post->meta_keywords ?? '');
        $this->og_image = (string) ($post->og_image ?? '');
        $this->canonical_url = (string) ($post->canonical_url ?? '');
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
                Rule::unique('blog_posts', 'slug')->ignore($this->editingId),
            ],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|string|max:500',
        ]);

        $payload = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?: null,
            'content' => $validated['content'] ?? '',
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? now())
                : ($validated['published_at'] ?? null),
            'meta_title' => $validated['meta_title'] ?: null,
            'meta_description' => $validated['meta_description'] ?: null,
            'meta_keywords' => $validated['meta_keywords'] ?: null,
            'og_image' => $validated['og_image'] ?: null,
            'canonical_url' => $validated['canonical_url'] ?: null,
            'user_id' => Auth::id(),
        ];

        if ($this->featured_image) {
            if ($this->existing_featured_image) {
                $this->deleteFeaturedImageFile($this->existing_featured_image);
            }

            $extension = strtolower($this->featured_image->getClientOriginalExtension() ?: 'jpg');
            $filename = Str::uuid() . '.' . $extension;
            $directory = public_path('uploads/blog');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $source = $this->featured_image->getRealPath();

            if (!$source || !is_file($source)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'featured_image' => 'The featured image could not be processed. Please try again.',
                ]);
            }

            if (!copy($source, $directory . DIRECTORY_SEPARATOR . $filename)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'featured_image' => 'The featured image could not be saved. Please try again.',
                ]);
            }

            $payload['featured_image'] = 'uploads/blog/' . $filename;
        }

        if ($this->editingId) {
            $post = BlogPost::findOrFail($this->editingId);
            $post->update($payload);
            session()->flash('message', 'Blog post updated successfully.');
        } else {
            BlogPost::create($payload);
            session()->flash('message', 'Blog post created successfully.');
        }

        $this->mode = 'list';
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $post = BlogPost::findOrFail($id);

        if ($post->featured_image) {
            $this->deleteFeaturedImageFile($post->featured_image);
        }

        $post->delete();
        session()->flash('message', 'Blog post deleted successfully.');

        if ($this->editingId === $id) {
            $this->mode = 'list';
            $this->resetForm();
        }
    }

    public function cancel(): void
    {
        $this->mode = 'list';
        $this->resetForm();
    }

    protected function deleteFeaturedImageFile(string $path): void
    {
        $path = ltrim($path, '/');

        if (Str::startsWith($path, 'uploads/')) {
            $fullPath = public_path($path);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
            return;
        }

        // Legacy storage/app/public path
        Storage::disk('public')->delete($path);

        $legacyPublic = public_path('uploads/blog/' . basename($path));
        if (is_file($legacyPublic)) {
            @unlink($legacyPublic);
        }
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->slug = '';
        $this->excerpt = '';
        $this->content = '';
        $this->status = 'draft';
        $this->published_at = null;
        $this->featured_image = null;
        $this->existing_featured_image = null;
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->og_image = '';
        $this->canonical_url = '';
        $this->resetValidation();
    }

    public function render()
    {
        $posts = BlogPost::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.manage-blogs', [
            'posts' => $posts,
        ])->layout('components.layouts.admin');
    }
}
