@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed dark:bg-gray-800 dark:border-gray-600">
                    {!! __('← Prev') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    {!! __('← Prev') !!}
                </a>
            @endif

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 ml-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    {!! __('Next →') !!}
                </a>
            @else
                <span class="px-3 py-1 ml-2 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed dark:bg-gray-800 dark:border-gray-600">
                    {!! __('Next →') !!}
                </span>
            @endif
        </div>

        {{-- Full Pagination (for larger screens) --}}
        <div class="hidden sm:flex sm:items-center sm:justify-center">
            <div class="flex items-center gap-1">
                {{-- Previous Page --}}
                @if ($paginator->onFirstPage())
                    <span class="px-2 py-1 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed dark:bg-gray-800 dark:border-gray-600">
                        ←
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-2 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                        ←
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 py-1 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed dark:bg-gray-800 dark:border-gray-600">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-1 text-xs font-bold text-white bg-primary hover:bg-red-300 border  rounded-md">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-2 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                        →
                    </a>
                @else
                    <span class="px-2 py-1 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed dark:bg-gray-800 dark:border-gray-600">
                        →
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
