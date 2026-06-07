<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased m-0 p-0 bg-gradient-to-r from-[#e2e8f0] to-[#dbeafe]">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6">
        {{ $slot }}
    </div>
</body>
</html>