{{-- resources/views/livewire/filiais/index.blade.php --}}
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
                    <path d="M3 21h18" />
                    <path d="M5 21V7l8-4v18" />
                    <path d="M19 21V11l-6-4" />
                    <path d="M9 9v.01" />
                    <path d="M9 12v.01" />
                    <path d="M9 15v.01" />
                    <path d="M9 18v.01" />
                </svg>
                <h2 class="text-lg font-semibold text-ink">{{ __('Lista de Filiais') }}</h2>
            </div>

            @can('filiais.criar')
                <a href="{{ route('filiais.create') }}" wire:navigate
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
                    placeholder="{{ __('Buscar por razão social, nome fantasia ou CNPJ...') }}">

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
                            <option value="">{{ __('Somente ativas (padrão)') }}</option>
                            <option value="0">{{ __('Somente inativas') }}</option>
                            <option value="todos">{{ __('Todas') }}</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-ink-muted mb-1.5">{{ __('Cadastrada a partir de') }}</label>
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
                    <span
                        class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-danger/15 text-danger">
                        {{ __('Somente inativas') }}
                    </span>
                @elseif ($filtroAtivo === 'todos')
                    <span
                        class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-accent/15 text-accent-light">
                        {{ __('Ativas e inativas') }}
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
                                        class="text-xs font-semibold uppercase text-primary-light">{{ __('Filial') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span class="text-xs font-semibold uppercase text-ink-muted">{{ __('CNPJ') }}</span>
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
                            @forelse ($filiais as $filial)
                                @php
                                    $avatarStyles = [
                                        ['bg' => 'bg-primary/20', 'text' => 'text-primary-light'],
                                        ['bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-400'],
                                        ['bg' => 'bg-accent/20', 'text' => 'text-accent-light'],
                                        ['bg' => 'bg-warn/20', 'text' => 'text-warn'],
                                        ['bg' => 'bg-rose-500/20', 'text' => 'text-rose-400'],
                                        ['bg' => 'bg-cyan-500/20', 'text' => 'text-cyan-400'],
                                    ];
                                    $avatarStyle = $avatarStyles[$filial->id % count($avatarStyles)];

                                    // Iniciais ignorando dígitos: "10101-GRAN CORTE" => "GR"
                                    $iniciais = \Illuminate\Support\Str::of($filial->razao_social)
                                        ->replaceMatches('/[^a-zA-Z]/', '')
                                        ->substr(0, 2)
                                        ->upper();
                                @endphp
                                <tr wire:key="filial-{{ $filial->id }}"
                                    class="hover:bg-surface-hover {{ $filial->eh_ativo ? '' : 'opacity-60' }}">
                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            <span
                                                class="text-sm font-medium text-primary-light">#{{ $filial->codigo }}</span>
                                        </div>
                                    </td>
                                    <td class="h-px w-72 min-w-72">
                                        <div class="px-6 py-3 flex items-center gap-x-3">
                                            <span
                                                class="inline-flex items-center justify-center size-9 rounded-full {{ $avatarStyle['bg'] }}">
                                                <span
                                                    class="text-xs font-semibold {{ $avatarStyle['text'] }}">{{ $iniciais }}</span>
                                            </span>
                                            <div class="min-w-0">
                                                <span
                                                    class="block text-sm font-medium text-ink truncate">{{ $filial->razao_social }}</span>
                                                <span class="block text-xs text-ink-muted truncate">
                                                    {{ $filial->nome_fantasia ?: __('Sem nome fantasia') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            @if ($filial->cnpj)
                                                <span
                                                    class="block text-sm text-ink">{{ $filial->cnpj_formatado }}</span>
                                                @if ($filial->ie)
                                                    <span class="block text-xs text-ink-muted">{{ __('IE') }}:
                                                        {{ $filial->ie }}</span>
                                                @endif
                                            @else
                                                <span class="text-sm text-ink-muted">&mdash;</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium {{ $filial->eh_ativo ? 'bg-success/15 text-success' : 'bg-danger/15 text-danger' }}">
                                                <span
                                                    class="size-1.5 inline-block rounded-full {{ $filial->eh_ativo ? 'bg-success' : 'bg-danger' }}"></span>
                                                {{ $filial->eh_ativo ? __('Ativa') : __('Inativa') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3 flex justify-end items-center gap-x-1.5">
                                            @can('filiais.editar')
                                                <a href="{{ route('filiais.edit', $filial) }}" wire:navigate
                                                    class="p-2 inline-flex items-center justify-center rounded-lg bg-primary hover:bg-primary-hover text-white"
                                                    title="{{ __('Editar') }}">
                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    </svg>
                                                </a>
                                            @endcan

                                            @can('filiais.status')
                                                <button type="button" wire:click="alternarStatus({{ $filial->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="p-2 inline-flex items-center justify-center rounded-lg bg-warn hover:bg-warn-hover text-canvas disabled:opacity-50"
                                                    title="{{ $filial->eh_ativo ? __('Desativar') : __('Ativar') }}">
                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M12 2v10" />
                                                        <path d="M18.4 6.6a9 9 0 1 1-12.77.04" />
                                                    </svg>
                                                </button>
                                            @endcan

                                            @can('filiais.excluir')
                                                <button type="button" wire:click="excluir({{ $filial->id }})"
                                                    wire:confirm="{{ __('Tem certeza que deseja excluir esta filial?') }}"
                                                    wire:loading.attr="disabled"
                                                    class="p-2 inline-flex items-center justify-center rounded-lg bg-danger hover:bg-danger/80 text-white disabled:opacity-50"
                                                    title="{{ __('Excluir') }}">
                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
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
                                            <path d="M3 21h18" />
                                            <path d="M5 21V7l8-4v18" />
                                            <path d="M19 21V11l-6-4" />
                                        </svg>
                                        <p class="text-sm text-ink-muted">
                                            @if ($this->temQualquerFiltro())
                                                {{ __('Nenhuma filial encontrada com esses filtros.') }}
                                            @else
                                                {{ __('Nenhuma filial cadastrada ainda.') }}
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
                                <span class="font-semibold text-ink">{{ $filiais->total() }}</span>
                                {{ trans_choice('filial encontrada|filiais encontradas', $filiais->total()) }}
                            </p>
                        </div>
                        <div>
                            {{ $filiais->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>