<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Mymore - Login' }}</title>
    <!-- Primary SEO -->
    <meta name="description" content="Flightandticketing by Mymore — Find cheap flights, compare fares, book instantly, and manage reservations. Fast search, secure payments, flexible cancellations." />
    <meta name="keywords" content="cheap flights, flight booking, flight tickets, compare airfare, airline tickets, Flightandticketing, Mymore" />
    <link rel="canonical" href="https://flightandticketing.com" />

    <title>{{ $title ?? 'Mymore' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>

    {{ $slot }}

    @livewireScripts
</body>

</html>