<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-gray-50 dark:bg-neutral-900" x-data="{ sidebarOpen: false }">

        <x-layouts.header />
        <x-layouts.sidebar />

        {{-- Conteúdo: empurrado pra direita no desktop pra dar espaço à sidebar --}}
        <div class="w-full lg:ps-64">
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
