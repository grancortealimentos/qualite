{{-- resources/views/auth/change-password.blade.php --}}
<x-layouts.guest :title="__('Alterar senha')">

    {{-- Logo --}}
    <div class="flex justify-center mb-6">
        <a href="{{ route('dashboard') }}">
            <x-logo class="h-20 w-auto" />
        </a>
    </div>

    {{-- Card --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xl overflow-hidden">

        {{-- Cabeçalho do card (mesmo padrão do login: título + divisória) --}}
        <div class="px-8 pt-8 pb-6 text-center border-b border-border">
            <h1 class="text-2xl font-bold text-ink">{{ __('Alterar senha') }}</h1>
            <p class="text-sm text-ink-muted mt-1">
                {{ __('Por segurança, defina uma nova senha para continuar.') }}
            </p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('password.change') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-password-fields label="{{ __('Nova senha') }}"
                    confirm-label="{{ __('Confirmar nova senha') }}">
                    {{ __('Alterar senha') }}
                </x-password-fields>
            </form>
        </div>
    </div>

    {{-- Rodapé --}}
    <p class="text-center text-xs text-ink-muted mt-6">
        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Todos os direitos reservados.') }}
    </p>

</x-layouts.guest>