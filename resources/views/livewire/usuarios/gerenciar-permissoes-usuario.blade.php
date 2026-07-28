{{-- resources/views/livewire/usuarios/gerenciar-permissoes-usuario.blade.php --}}
{{-- Componente Livewire full-page: raiz é UM único elemento. --}}
<div class="flex flex-col gap-4">

    {{-- Toasts do Livewire: as ações não recarregam a página, então este
    listener precisa viver AQUI (evento de janela não sobrevive à navegação). --}}
    <div x-data="{
            toasts: [],
            add(tipo, mensagem) {
                const id = Date.now() + Math.random();
                this.toasts.push({ id, tipo, mensagem });
                setTimeout(() => this.remove(id), 5000);
            },
            remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
         }" @toast.window="add($event.detail.tipo, $event.detail.mensagem)"
        class="fixed top-5 end-5 z-[100] flex flex-col gap-y-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                class="pointer-events-auto max-w-sm w-full border shadow-lg text-sm rounded-xl bg-surface text-ink"
                :class="toast.tipo === 'error' ? 'border-danger/30' : 'border-success/30'" role="alert">
                <div class="flex items-start gap-3 p-4">
                    <svg class="shrink-0 size-5 mt-0.5" :class="toast.tipo === 'error' ? 'text-danger' : 'text-success'"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span class="flex-1" x-text="toast.mensagem"></span>
                    <button type="button" @click="remove(toast.id)"
                        class="shrink-0 size-5 text-ink-muted hover:text-ink">
                        <span class="sr-only">Fechar</span>
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Cabeçalho --}}
    <div class="bg-surface border border-border rounded-xl px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-x-2">
            <svg class="shrink-0 size-5 text-primary-light" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
            </svg>
            <div>
                <h2 class="text-lg font-semibold text-ink">{{ __('Permissões do usuário') }}</h2>
                <p class="text-xs text-ink-muted">
                    {{ $usuario->name }}
                    @if ($usuario->getRoleNames()->isNotEmpty())
                        &middot; {{ __('Papéis:') }} {{ $usuario->getRoleNames()->join(', ') }}
                    @endif
                </p>
            </div>
        </div>

        <a href="{{ route('pessoas.index') }}" wire:navigate
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7" />
                <path d="M19 12H5" />
            </svg>
            {{ __('Voltar') }}
        </a>
    </div>

    {{-- Aviso: permissões do papel são exibidas travadas --}}
    <div class="bg-primary/10 border border-primary/30 rounded-xl px-6 py-3 flex items-start gap-x-3">
        <svg class="shrink-0 size-4 mt-0.5 text-primary-light" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 16v-4" />
            <path d="M12 8h.01" />
        </svg>
        <p class="text-xs text-primary-light">
            {{ __('As permissões marcadas como "via perfil" são herdadas dos papéis do usuário e não podem ser alteradas aqui. Abaixo você concede permissões diretamente a este usuário, somadas às do perfil.') }}
        </p>
    </div>

    @php
        // Universo global editável = catálogo inteiro MENOS o que já vem do papel.
        // Alimenta o "selecionar tudo".
        $editaveisGlobais = collect($grupos)
            ->flatMap(fn($grupo) => array_keys($grupo['permissoes']))
            ->reject(fn($nome) => in_array($nome, $permissoesDoPapel, true))
            ->values()
            ->all();
    @endphp

    {{-- x-data agora envolve o card E as ações, para o Salvar enxergar `selecionadas`.
    Estado Alpine puro (iniciais), sem entangle — o Salvar passa o array direto. --}}
    <div x-data="gridPermissoesUsuario({
            editaveis: @js($editaveisGlobais),
            iniciais: @js($permissoesDiretas),
         })">

        {{-- Card do grid --}}
        <div class="bg-surface border border-border rounded-xl p-6">
            <div class="flex flex-col">

                {{-- Cabeçalho das colunas --}}
                <div
                    class="grid grid-cols-1 md:grid-cols-[minmax(0,18rem)_1fr] gap-x-6 px-1 pb-2 border-b border-dashed border-border">
                    <span class="text-xs font-semibold uppercase text-ink-muted">{{ __('Módulo') }}</span>
                    <span class="text-xs font-semibold uppercase text-ink-muted">{{ __('Permissões') }}</span>
                </div>

                {{-- "Selecionar tudo": só aparece se sobrar algo editável --}}
                @if (count($editaveisGlobais) > 0)
                    <div
                        class="grid grid-cols-1 md:grid-cols-[minmax(0,18rem)_1fr] gap-x-6 py-3 border-b border-dashed border-border">
                        <div></div>
                        <label class="inline-flex items-center gap-x-2.5 cursor-pointer group">
                            <input type="checkbox" x-model="tudoMarcado" x-effect="$el.indeterminate = tudoParcial"
                                class="shrink-0 size-4 rounded bg-canvas border-border text-primary focus:ring-primary focus:ring-offset-0">
                            <span class="text-sm font-medium text-ink group-hover:text-primary-light">
                                {{ __('Selecionar todas as diretas') }}
                            </span>
                        </label>
                    </div>
                @endif

                {{-- Um bloco por módulo --}}
                @foreach ($grupos as $chave => $grupo)
                    @php
                        $nomesModulo = array_keys($grupo['permissoes']);
                        // Só a fatia editável do módulo (tira o que já é do papel).
                        $editaveisModulo = array_values(array_diff($nomesModulo, $permissoesDoPapel));
                    @endphp

                    <div wire:key="modulo-{{ $chave }}"
                        class="grid grid-cols-1 md:grid-cols-[minmax(0,18rem)_1fr] gap-x-6 gap-y-3 py-4 border-b border-dashed border-border">

                        {{-- Coluna MÓDULO --}}
                        <div>
                            @if (count($editaveisModulo) > 0)
                                <label class="inline-flex items-center gap-x-2.5 cursor-pointer group">
                                    <input type="checkbox"
                                        @change="alternarModulo(@js($editaveisModulo), $event.target.checked)"
                                        :checked="moduloMarcado(@js($editaveisModulo))"
                                        x-effect="$el.indeterminate = moduloParcial(@js($editaveisModulo))"
                                        class="shrink-0 size-4 rounded bg-canvas border-border text-primary focus:ring-primary focus:ring-offset-0">
                                    <span class="text-sm font-medium text-primary-light group-hover:text-primary">
                                        [{{ $grupo['codigo'] }}] - {{ $grupo['label'] }}
                                    </span>
                                </label>
                            @else
                                {{-- Módulo inteiro herdado do papel: nada a editar --}}
                                <span class="inline-flex items-center gap-x-2 text-sm font-medium text-ink-muted">
                                    [{{ $grupo['codigo'] }}] - {{ $grupo['label'] }}
                                    <span
                                        class="inline-flex items-center py-0.5 px-1.5 rounded-full text-[10px] font-medium bg-border/40 text-ink-muted">
                                        {{ __('todas via perfil') }}
                                    </span>
                                </span>
                            @endif
                        </div>

                        {{-- Coluna PERMISSÕES --}}
                        <div class="flex flex-col gap-y-2.5">
                            @foreach ($grupo['permissoes'] as $nome => $rotulo)
                                @if ($this->estaNoPapel($nome))
                                    {{-- TRAVADA: herdada do papel. Cor accent (distinta do primary das
                                    editáveis) + borda/fundo suaves para dar presença mesmo disabled.
                                    Sem opacity: era ela que apagava a linha. --}}
                                    <label class="inline-flex items-center gap-x-2.5 cursor-not-allowed">
                                        <input type="checkbox" checked disabled
                                            class="shrink-0 size-4 rounded border-accent/40 bg-accent/10 text-accent">
                                        <span class="text-sm text-accent-light">{{ $rotulo }}</span>
                                        <span
                                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-[10px] font-medium bg-accent/15 text-accent-light">
                                            {{ __('via perfil') }}
                                        </span>
                                    </label>
                                @else
                                    {{-- EDITÁVEL: permissão direta. Cor primary, x-model no array Alpine. --}}
                                    <label class="inline-flex items-center gap-x-2.5 cursor-pointer group">
                                        <input type="checkbox" value="{{ $nome }}" x-model="selecionadas"
                                            class="shrink-0 size-4 rounded bg-canvas border-border text-primary focus:ring-primary focus:ring-offset-0">
                                        <span class="text-sm text-ink-muted group-hover:text-ink">{{ $rotulo }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Contador de diretas --}}
                <div class="pt-4">
                    <p class="text-xs text-ink-muted">
                        <span class="font-semibold text-ink" x-text="selecionadas.length"></span>
                        <span x-text="selecionadas.length === 1
                            ? '{{ __('permissão direta selecionada') }}'
                            : '{{ __('permissões diretas selecionadas') }}'"></span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Ações: agora DENTRO do x-data, para o Salvar enxergar `selecionadas` --}}
        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('pessoas.index') }}" wire:navigate
                class="py-2 px-4 inline-flex items-center text-sm font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink">
                {{ __('Cancelar') }}
            </a>

            {{-- Passa o array do Alpine direto pro método. Sem entangle no meio. --}}
            <button type="button" @click="$wire.salvar(selecionadas)" wire:loading.attr="disabled" wire:target="salvar"
                class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary border border-transparent text-white hover:bg-primary-hover disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading wire:target="salvar" class="shrink-0 size-4 animate-spin" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"></path>
                </svg>
                {{ __('Salvar permissões') }}
            </button>
        </div>
    </div>
</div>