{{-- resources/views/livewire/pessoas/index.blade.php --}}
<div class="flex flex-col gap-4">

    {{-- Toasts do Livewire (as ações não recarregam a página, então session() não serve) --}}
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

    {{-- Bloco 1: Título + Busca + Filtros --}}
    <div class="bg-surface border border-border rounded-xl overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 flex justify-between items-center border-b border-border">
            <div class="flex items-center gap-x-2">
                <svg class="shrink-0 size-5 text-primary-light" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <h2 class="text-lg font-semibold text-ink">{{ __('Lista de Pessoas') }}</h2>
            </div>

            @can('pessoas.criar')
            <a href="{{ route('pessoas.create') }}" wire:navigate
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary border border-transparent text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
                {{ __('Novo') }}
            </a>
            @endcan
        </div>

        {{-- Linha de busca --}}
        <div class="px-6 py-3 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-56">
                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3">
                    <svg class="shrink-0 size-4 text-ink-muted" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </div>

                {{-- .live.debounce: filtra enquanto digita, sem botão --}}
                <input type="text" wire:model.live.debounce.400ms="busca"
                    class="py-2 ps-9 pe-9 block w-full bg-canvas border-border rounded-lg text-sm text-ink placeholder:text-ink-muted focus:border-primary focus:ring-primary"
                    placeholder="{{ __('Buscar por nome, documento ou e-mail...') }}">

                {{-- Spinner enquanto a busca roda --}}
                <div wire:loading wire:target="busca"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none">
                    <svg class="size-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z">
                        </path>
                    </svg>
                </div>
            </div>

            {{-- Filtros Avançados: no FIM da linha --}}
            <button type="button" wire:click="$toggle('filtrosAbertos')"
                class="ms-auto py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-accent border border-transparent text-white hover:bg-accent-hover focus:outline-hidden">
                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M3 6h18" />
                    <path d="M7 12h10" />
                    <path d="M10 18h4" />
                </svg>
                {{ __('Filtros Avançados') }}
                @if ($this->temFiltroAvancado())
                    <span
                        class="inline-flex items-center justify-center size-5 rounded-full bg-white/20 text-[10px] font-semibold">
                        {{ collect([$filtroAtivo, $cadastradoDe])->filter(fn($v) => $v !== '')->count() }}
                    </span>
                @endif
                <svg class="shrink-0 size-3.5 transition-transform {{ $filtrosAbertos ? 'rotate-180' : '' }}"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>
        </div>

        {{-- Card de filtros avançados: abre ABAIXO --}}
        @if ($filtrosAbertos)
            <div class="px-6 py-5 border-t border-border bg-canvas/40">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-xs font-medium text-ink-muted mb-1.5">{{ __('Status') }}</label>
                        <select wire:model.live="filtroAtivo"
                            class="py-2 px-3 block w-full bg-canvas border-border rounded-lg text-sm text-ink focus:border-primary focus:ring-primary">
                            <option value="">{{ __('Somente ativos (padrão)') }}</option>
                            <option value="0">{{ __('Somente inativos') }}</option>
                            <option value="todos">{{ __('Todos') }}</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-ink-muted mb-1.5">{{ __('Cadastrado a partir de') }}</label>
                        <input type="date" wire:model.live="cadastradoDe"
                            class="py-2 px-3 block w-full bg-canvas border-border rounded-lg text-sm text-ink focus:border-primary focus:ring-primary">
                    </div>

                    <div class="flex items-end">
                        @if ($this->temQualquerFiltro())
                            <button type="button" wire:click="limparFiltros"
                                class="w-full py-2 px-4 inline-flex justify-center items-center text-sm font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink">
                                {{ __('Limpar filtros') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Chips dos filtros ativos --}}
        @if ($this->temQualquerFiltro())
            <div class="px-6 py-3 border-t border-border flex flex-wrap items-center gap-2">
                <span class="text-xs text-ink-muted">{{ __('Filtros ativos:') }}</span>

                @if ($busca !== '')
                    <span
                        class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-primary/15 text-primary-light">
                        {{ __('Busca') }}: "{{ $busca }}"
                    </span>
                @endif

                @if ($filtroAtivo === '0')
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-danger/15 text-danger">
                        {{ __('Somente inativos') }}
                    </span>
                @elseif ($filtroAtivo === 'todos')
                    <span
                        class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-accent/15 text-accent-light">
                        {{ __('Ativos e inativos') }}
                    </span>
                @endif

                @if ($cadastradoDe !== '')
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-warn/15 text-warn">
                        {{ __('Desde') }} {{ \Carbon\Carbon::parse($cadastradoDe)->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Bloco 2: Somente a tabela --}}
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="bg-surface border border-border rounded-xl overflow-hidden relative">

                    {{-- Overlay de loading sobre a tabela --}}
                    <div wire:loading.delay class="absolute inset-0 bg-canvas/50 z-10"></div>

                    <table class="min-w-full divide-y divide-border">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span
                                        class="text-xs font-semibold uppercase text-primary-light">{{ __('Cód') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span
                                        class="text-xs font-semibold uppercase text-primary-light">{{ __('Usuário') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span
                                        class="text-xs font-semibold uppercase text-ink-muted">{{ __('Documento') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span
                                        class="text-xs font-semibold uppercase text-ink-muted">{{ __('Status') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-end">
                                    <span
                                        class="text-xs font-semibold uppercase text-ink-muted">{{ __('Ações') }}</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-border">
                            @forelse ($pessoas as $pessoa)
                                @php
                                    $avatarStyles = [
                                        ['bg' => 'bg-primary/20', 'text' => 'text-primary-light'],
                                        ['bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-400'],
                                        ['bg' => 'bg-accent/20', 'text' => 'text-accent-light'],
                                        ['bg' => 'bg-warn/20', 'text' => 'text-warn'],
                                        ['bg' => 'bg-rose-500/20', 'text' => 'text-rose-400'],
                                        ['bg' => 'bg-cyan-500/20', 'text' => 'text-cyan-400'],
                                    ];
                                    $avatarStyle = $avatarStyles[$pessoa->id % count($avatarStyles)];
                                @endphp
                                <tr wire:key="pessoa-{{ $pessoa->id }}"
                                    class="hover:bg-surface-hover {{ $pessoa->eh_ativo ? '' : 'opacity-60' }}">
                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            <span class="text-sm font-medium text-primary-light">#{{ $pessoa->id }}</span>
                                        </div>
                                    </td>
                                    <td class="h-px w-72 min-w-72">
                                        <div class="px-6 py-3 flex items-center gap-x-3">
                                            @if ($pessoa->foto_url)
                                                <img class="inline-block size-9 rounded-full object-cover ring-2 ring-border"
                                                    src="{{ $pessoa->foto_url }}" alt="{{ $pessoa->nome_completo }}">
                                            @else
                                                <span
                                                    class="inline-flex items-center justify-center size-9 rounded-full {{ $avatarStyle['bg'] }}">
                                                    <span
                                                        class="text-xs font-semibold {{ $avatarStyle['text'] }}">{{ Str::of($pessoa->nome_completo)->substr(0, 2)->upper() }}</span>
                                                </span>
                                            @endif
                                            <div class="min-w-0">
                                                <span
                                                    class="block text-sm font-medium text-ink truncate">{{ $pessoa->nome_completo }}</span>
                                                <span class="block text-xs text-ink-muted truncate">
                                                    {{ $pessoa->email ?: $pessoa->tipo_cadastro }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            @if ($pessoa->documento)
                                                <span class="block text-sm text-ink">{{ $pessoa->documento_formatado }}</span>
                                                <span class="block text-xs text-ink-muted">{{ $pessoa->tipo_documento }}</span>
                                            @else
                                                <span class="text-sm text-ink-muted">&mdash;</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium {{ $pessoa->eh_ativo ? 'bg-success/15 text-success' : 'bg-danger/15 text-danger' }}">
                                                <span
                                                    class="size-1.5 inline-block rounded-full {{ $pessoa->eh_ativo ? 'bg-success' : 'bg-danger' }}"></span>
                                                {{ $pessoa->eh_ativo ? __('Ativo') : __('Inativo') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3 flex justify-end items-center gap-x-1.5">
                                            @can('pessoas.editar')
                                            <a href="{{ route('pessoas.edit', $pessoa) }}" wire:navigate
                                                class="p-2 inline-flex items-center justify-center rounded-lg bg-primary hover:bg-primary-hover text-white"
                                                title="{{ __('Editar') }}">
                                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                </svg>
                                            </a>
                                            @endcan

                                            @can('pessoas.status')
                                            <button type="button" wire:click="alternarStatus({{ $pessoa->id }})"
                                                wire:loading.attr="disabled"
                                                class="p-2 inline-flex items-center justify-center rounded-lg bg-warn hover:bg-warn-hover text-canvas disabled:opacity-50"
                                                title="{{ $pessoa->eh_ativo ? __('Desativar') : __('Ativar') }}">
                                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 2v10" />
                                                    <path d="M18.4 6.6a9 9 0 1 1-12.77.04" />
                                                </svg>
                                            </button>
                                            @endcan

                                            @can('pessoas.excluir')
                                            <button type="button" wire:click="excluir({{ $pessoa->id }})"
                                                wire:confirm="{{ __('Tem certeza que deseja excluir esta pessoa?') }}"
                                                wire:loading.attr="disabled"
                                                class="p-2 inline-flex items-center justify-center rounded-lg bg-danger hover:bg-danger/80 text-white disabled:opacity-50"
                                                title="{{ __('Excluir') }}">
                                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18" />
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="size-10 mx-auto text-border mb-3" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                        <p class="text-sm text-ink-muted">
                                            @if ($this->temQualquerFiltro())
                                                {{ __('Nenhuma pessoa encontrada com esses filtros.') }}
                                            @else
                                                {{ __('Nenhuma pessoa cadastrada ainda.') }}
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Footer --}}
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-border">
                        <div>
                            <p class="text-sm text-ink-muted">
                                <span class="font-semibold text-ink">{{ $pessoas->total() }}</span>
                                {{ trans_choice('pessoa encontrada|pessoas encontradas', $pessoas->total()) }}
                            </p>
                        </div>
                        <div>
                            {{ $pessoas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>