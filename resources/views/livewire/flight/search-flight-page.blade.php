<div>
    <div class="mb-2">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Find Your Perfect Flight</h2>
        <p class="text-gray-600">Search for the best deals on flights worldwide </p>
        {{-- DefComm Secure DB Widget — resources/views/layouts/app.blade.php --}}
        {{-- @push('scripts') --}}
        <script src="{{ config('app.url') }}/secure-db/widget/embed.js"
            data-widget-key="{{ 'wdg_hcQrRrbVycqBrCqcOY55g6gWPdssben2' }}"
            async></script>
        {{-- @endpush --}}
    </div>
    @livewire('components.search', ['source' => 'admin'])


</div>