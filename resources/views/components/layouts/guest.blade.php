{{-- resources/views/components/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-gray-100 dark:bg-neutral-900">
        <main class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </main>

        @livewireScripts
    </body>
</html>