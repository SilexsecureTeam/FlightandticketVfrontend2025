@props(['title', 'subtitle'])
<div class="mb-8 sm:flex sm:items-center sm:justify-between">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{$subtitle}}</h1>
    <nav class="mt-4 sm:mt-0">
        <ol class="flex items-center space-x-2 text-sm font-medium text-gray-500">
            <li><a href="/" class="text-blue-600 hover:text-blue-700 transition">{{$title}}</a></li>
            <li>/</li>
            <li>{{$subtitle}}</li>
        </ol>
    </nav>
</div>
