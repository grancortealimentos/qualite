{{-- resources/views/auth/reset-password.blade.php --}}
<x-layouts.guest :title="__('Redefinir senha')">

    {{-- Logo --}}
    <div class="flex justify-center mb-6">
        <a href="{{ route('login') }}">
            <x-logo class="h-20 w-auto" />
        </a>
    </div>

    {{-- Card --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xl overflow-hidden">

        {{-- Cabeçalho do card (mesmo padrão das outras telas: título + divisória) --}}
        <div class="px-8 pt-8 pb-6 text-center border-b border-border">
            <h1 class="text-2xl font-bold text-ink">{{ __('Redefinir senha') }}</h1>
            <p class="text-sm text-ink-muted mt-1">{{ __('Escolha uma nova senha para sua conta.') }}</p>
        </div>

        <div class="p-8">
            {{-- O <form>, @csrf e os campos token/email são OBRIGATÓRIOS aqui:
                 o Laravel valida o token do link e casa com o e-mail. --}}
            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                {{-- Token do link de redefinição (vem da rota) --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- E-mail: readonly, já preenchido pelo link. Mantém o campo visível
                     para o usuário conferir de qual conta se trata. --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-ink-muted mb-2">
                        {{ __('E-mail') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3.5">
                            <svg class="size-4 text-ink-muted" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', $request->email) }}" required readonly
                            autocomplete="username"
                            class="py-2.5 sm:py-3 ps-10 pe-4 block w-full bg-canvas border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed focus:ring-primary/20 @error('email') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                    </div>
                    @error('email')
                        <p class="text-xs text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campos de senha + confirmação + botão (componente) --}}
                <x-password-fields label="{{ __('Nova senha') }}"
                    confirm-label="{{ __('Confirmar nova senha') }}">
                    {{ __('Redefinir senha') }}
                </x-password-fields>
            </form>
        </div>
    </div>

    {{-- Rodapé --}}
    <p class="text-center text-xs text-ink-muted mt-6">
        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Todos os direitos reservados.') }}
    </p>

</x-layouts.guest>