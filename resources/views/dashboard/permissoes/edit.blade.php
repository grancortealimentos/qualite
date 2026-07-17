{{-- resources/views/papeis/edit.blade.php --}}
<x-layouts.app>
    <div class="flex flex-col gap-4">

        {{-- Cabeçalho --}}
        <div class="bg-surface border border-border rounded-xl px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-x-2">
                <svg class="shrink-0 size-5 text-primary-light" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <div>
                    <h2 class="text-lg font-semibold text-ink">{{ __('Editar papel') }}</h2>
                    <p class="text-xs text-ink-muted">#{{ $papel->id }} &middot; {{ $papel->name }}</p>
                </div>
            </div>

            <a href="{{ route('papeis.index') }}"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                {{ __('Voltar') }}
            </a>
        </div>

        {{-- Aviso de impacto: alterar permissões afeta todos os usuários do papel.
             Só aparece se houver alguém vinculado — senão é ruído. --}}
        @if ($papel->users_count > 0)
            <div class="bg-warn/10 border border-warn/30 rounded-xl px-6 py-3 flex items-start gap-x-3">
                <svg class="shrink-0 size-4 mt-0.5 text-warn" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
                    <path d="M12 9v4" />
                    <path d="M12 17h.01" />
                </svg>
                <p class="text-xs text-warn">
                    {{ trans_choice(
                        'Este papel está em uso por :count usuário. A alteração passa a valer para ele imediatamente.|Este papel está em uso por :count usuários. A alteração passa a valer para todos eles imediatamente.',
                        $papel->users_count,
                        ['count' => $papel->users_count],
                    ) }}
                </p>
            </div>
        @endif

        {{-- x-data no form: desabilita o botão no submit para evitar duplo envio --}}
        <form method="POST" action="{{ route('papeis.update', $papel) }}" x-data="{ salvando: false }"
            @submit="salvando = true">
            @csrf
            {{-- Formulário HTML só envia GET/POST; o @method injeta o _method
                 que o Laravel usa para rotear como PUT --}}
            @method('PUT')

            <div class="bg-surface border border-border rounded-xl p-6 flex flex-col gap-6">

                {{-- Nome --}}
                <div class="max-w-lg">
                    <label for="nome" class="block text-sm font-medium text-ink mb-1.5">
                        {{ __('Nome') }} <span class="text-danger">*</span>
                    </label>
                    {{-- old() na frente: se a validação recusar, o admin recupera
                         o que ele digitou, não o valor que está no banco --}}
                    <input type="text" id="nome" name="nome" value="{{ old('nome', $papel->name) }}" autofocus
                        class="py-2 px-3 block w-full bg-canvas border rounded-lg text-sm text-ink placeholder:text-ink-muted focus:border-primary focus:ring-primary @error('nome') border-danger @else border-border @enderror"
                        placeholder="{{ __('Ex.: Gerente de produção') }}">
                    @error('nome')
                        <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Grid de permissões --}}
                <div class="pt-2 border-t border-border">
                    <h3 class="text-sm font-semibold uppercase text-ink mb-4 mt-4">{{ __('Acesso') }}</h3>

                    {{-- $permissoesMarcadas vem do controller (nomes técnicos das
                         permissões já vinculadas). O partial aplica old() por
                         cima, então um erro de validação preserva o que estava
                         marcado na tela em vez de recarregar do banco. --}}
                    @include('dashboard.permissoes.partials.grid-permissoes', [
                        'grupos'   => $grupos,
                        'marcadas' => $permissoesMarcadas,
                    ])
                </div>
            </div>

            {{-- Ações --}}
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('papeis.index') }}"
                    class="py-2 px-4 inline-flex items-center text-sm font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink">
                    {{ __('Cancelar') }}
                </a>

                <button type="submit" :disabled="salvando"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary border border-transparent text-white hover:bg-primary-hover disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="salvando" x-cloak class="shrink-0 size-4 animate-spin" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z">
                        </path>
                    </svg>
                    <span x-text="salvando ? '{{ __('Salvando...') }}' : '{{ __('Salvar alterações') }}'"></span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>