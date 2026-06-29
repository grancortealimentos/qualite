{{-- resources/views/components/toast.blade.php --}}
@props([
    'type' => 'success',  // success | error | info
])

@php
    $styles = [
        'success' => 'bg-green-50 border-green-200 text-green-700 dark:text-green-400 dark:bg-green-800/10 dark:border-green-900',
        'error'   => 'bg-red-100 border-red-200 text-red-800 dark:bg-red-500/20 dark:border-red-900 dark:text-red-400',
        'info'    => 'bg-blue-100 border-blue-200 text-blue-800 dark:bg-blue-500/20 dark:border-blue-900 dark:text-blue-400',
    ][$type];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 5000)"
     x-transition.opacity.duration.300ms
     class="max-w-xs w-full border text-sm rounded-lg {{ $styles }}"
     role="alert">
    <div class="flex p-4">
        {{ $slot }}

        <div class="ms-auto">
            <button type="button" @click="show = false"
                    class="shrink-0 flex justify-center items-center size-5 opacity-50 hover:opacity-100 focus:outline-hidden">
                <span class="sr-only">Fechar</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    </div>
</div>