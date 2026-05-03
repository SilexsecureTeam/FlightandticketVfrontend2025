<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Page;

class ManagePages extends Component
{
    public $pages;
    public $selectedPageId;
    public $content;
    public $title;

    public function mount()
    {
        // Ensure default pages exist
        Page::firstOrCreate(['slug' => 'about-us'], ['title' => 'About Us', 'content' => '']);
        Page::firstOrCreate(['slug' => 'terms-and-conditions'], ['title' => 'Terms and Conditions', 'content' => '']);

        $this->pages = Page::all();
        if ($this->pages->count() > 0) {
            $this->selectPage($this->pages->first()->id);
        }
    }

    public function selectPage($id)
    {
        $this->selectedPageId = $id;
        $page = Page::find($id);
        $this->title = $page->title;
        $this->content = $page->content;
        
        // Dispatch event to update Quill editor
        $this->dispatch('contentChanged', content: $this->content);
    }

    public function save()
    {
        $page = Page::find($this->selectedPageId);
        if ($page) {
            $page->title = $this->title;
            $page->content = $this->content;
            $page->save();

            session()->flash('message', 'Page updated successfully.');
            return redirect('/admin/pages');
        }
    }

    public function render()
    {
        return view('livewire.admin.manage-pages')->layout('components.layouts.admin');
    }
}
