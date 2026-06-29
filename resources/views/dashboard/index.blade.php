{{-- resources/views/dashboard/index.blade.php --}}
<x-layouts.app :title="'Dashboard'">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-neutral-200">
        Bem-vindo, {{ auth()->user()->name }}
    </h1>

    <p class="mt-2 text-gray-600 dark:text-neutral-400">
        Login efetuado com sucesso. O conteúdo do dashboard entra aqui depois.
    </p>
</x-layouts.app>