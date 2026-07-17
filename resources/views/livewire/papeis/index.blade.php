{{-- resources/views/livewire/papeis/index.blade.php --}}
<div class="flex flex-col gap-4" x-data="{
        modalAberto: false,
        papelId: null,
        papelNome: '',
        excluindo: false,

        confirmarExclusao(id, nome) {
            this.papelId = id;
            this.papelNome = nome;
            this.modalAberto = true;
        },

        async excluir() {
            this.excluindo = true;
            // $wire.excluir() devolve uma Promise: o await garante que o modal
            // só feche depois da resposta do servidor. Fechar antes deixaria a
            // linha na tela por um instante após o toast de sucesso.
            await $wire.excluir(this.papelId);
            this.excluindo = false;
            this.modalAberto = false;
        },
}">

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

    {{-- Bloco 1: Título + Busca --}}
    <div class="bg-surface border border-border rounded-xl overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 flex justify-between items-center border-b border-border">
            <div class="flex items-center gap-x-2">
                <svg class="shrink-0 size-5 text-primary-light" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <h2 class="text-lg font-semibold text-ink">{{ __('Permissões de acesso') }}</h2>
            </div>

            <a href="{{ route('papeis.create') }}" wire:navigate
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary border border-transparent text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
                {{ __('Novo papel') }}
            </a>
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
                    placeholder="{{ __('Buscar por nome do papel...') }}">

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
        </div>

        {{-- Chips do filtro ativo --}}
        @if ($this->temQualquerFiltro())
            <div class="px-6 py-3 border-t border-border flex flex-wrap items-center gap-2">
                <span class="text-xs text-ink-muted">{{ __('Filtros ativos:') }}</span>

                <span
                    class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-primary/15 text-primary-light">
                    {{ __('Busca') }}: "{{ $busca }}"
                </span>

                <button type="button" wire:click="limparFiltros"
                    class="ms-auto py-1.5 px-3 inline-flex items-center text-xs font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink">
                    {{ __('Limpar filtros') }}
                </button>
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
                                        class="text-xs font-semibold uppercase text-primary-light">{{ __('Papel') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span
                                        class="text-xs font-semibold uppercase text-ink-muted">{{ __('Permissões') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span
                                        class="text-xs font-semibold uppercase text-ink-muted">{{ __('Usuários atribuídos') }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-end">
                                    <span
                                        class="text-xs font-semibold uppercase text-ink-muted">{{ __('Ações') }}</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-border">
                            @forelse ($papeis as $papel)
                                @php
                                    // O papel de sistema (Admin) é listado, mas não pode ser
                                    // editado nem excluído: renomeá-lo quebraria o Gate::before,
                                    // que compara pelo nome vindo do config.
                                    $ehSistema = $papel->ehPapelSistema();
                                @endphp
                                <tr wire:key="papel-{{ $papel->id }}" class="hover:bg-surface-hover">
                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            <span class="text-sm font-medium text-primary-light">#{{ $papel->id }}</span>
                                        </div>
                                    </td>

                                    <td class="h-px w-72 min-w-72">
                                        <div class="px-6 py-3 flex items-center gap-x-3">
                                            <span
                                                class="inline-flex items-center justify-center size-9 rounded-full {{ $ehSistema ? 'bg-warn/20' : 'bg-accent/20' }}">
                                                <svg class="size-4 {{ $ehSistema ? 'text-warn' : 'text-accent-light' }}"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                                </svg>
                                            </span>
                                            <div class="min-w-0">
                                                <span class="block text-sm font-medium text-ink truncate">
                                                    {{ $papel->name }}
                                                </span>
                                                @if ($ehSistema)
                                                    <span class="block text-xs text-warn">{{ __('Papel de sistema') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            @if ($ehSistema)
                                                {{-- O Admin não carrega permissões na tabela: o acesso
                                                total vem do bypass no Gate::before. Contagem 0 aqui
                                                seria enganosa. --}}
                                                <span
                                                    class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium bg-warn/15 text-warn">
                                                    {{ __('Acesso total') }}
                                                </span>
                                            @else
                                                <span class="block text-sm text-ink">
                                                    {{ $papel->permissions_count }}
                                                </span>
                                                <span class="block text-xs text-ink-muted">
                                                    {{ trans_choice('permissão|permissões', $papel->permissions_count) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium {{ $papel->users_count > 0 ? 'bg-success/15 text-success' : 'bg-surface-hover text-ink-muted' }}">
                                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                    <circle cx="9" cy="7" r="4" />
                                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                </svg>
                                                {{ $papel->users_count }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="size-px whitespace-nowrap">
                                        <div class="px-6 py-3 flex justify-end items-center gap-x-1.5">
                                            @if ($ehSistema)
                                                <span class="text-xs text-ink-muted">{{ __('Protegido') }}</span>
                                            @else
                                                <a href="{{ route('papeis.edit', $papel) }}" wire:navigate
                                                    class="p-2 inline-flex items-center justify-center rounded-lg bg-primary hover:bg-primary-hover text-white"
                                                    title="{{ __('Editar') }}">
                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    </svg>
                                                </a>

                                                <button type="button"
                                                    @click="confirmarExclusao({{ $papel->id }}, @js($papel->name))"
                                                    class="p-2 inline-flex items-center justify-center rounded-lg bg-danger hover:bg-danger/80 text-white"
                                                    title="{{ __('Excluir') }}">
                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 6h18" />
                                                        <path
                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="size-10 mx-auto text-border mb-3" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                        </svg>
                                        <p class="text-sm text-ink-muted">
                                            @if ($this->temQualquerFiltro())
                                                {{ __('Nenhum papel encontrado com essa busca.') }}
                                            @else
                                                {{ __('Nenhum papel cadastrado ainda.') }}
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
                                <span class="font-semibold text-ink">{{ $papeis->total() }}</span>
                                {{ trans_choice('papel encontrado|papéis encontrados', $papeis->total()) }}
                            </p>
                        </div>
                        <div>
                            {{ $papeis->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmação de exclusão --}}
    <div x-show="modalAberto" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4"
        @keydown.escape.window="if (!excluindo) modalAberto = false" role="dialog" aria-modal="true">

        {{-- Backdrop: clicar fora fecha, mas não durante a exclusão --}}
        <div x-show="modalAberto" x-transition.opacity class="absolute inset-0 bg-black/60"
            @click="if (!excluindo) modalAberto = false"></div>

        <div x-show="modalAberto" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-md bg-surface border border-border rounded-xl shadow-2xl">

            <div class="p-6 flex flex-col gap-4">
                <div class="flex items-start gap-4">
                    <span class="shrink-0 inline-flex items-center justify-center size-11 rounded-full bg-danger/15">
                        <svg class="size-5 text-danger" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18" />
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-ink">{{ __('Excluir papel') }}</h3>
                        <p class="mt-1 text-sm text-ink-muted">
                            {{ __('Esta ação não pode ser desfeita. O papel') }}
                            <span class="font-medium text-ink" x-text="papelNome"></span>
                            {{ __('e suas permissões serão removidos permanentemente.') }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="modalAberto = false" :disabled="excluindo"
                        class="py-2 px-4 inline-flex items-center text-sm font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink disabled:opacity-50">
                        {{ __('Cancelar') }}
                    </button>

                    <button type="button" @click="excluir()" :disabled="excluindo"
                        class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-danger border border-transparent text-white hover:bg-danger/80 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="excluindo" x-cloak class="shrink-0 size-4 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z">
                            </path>
                        </svg>
                        <span x-text="excluindo ? '{{ __('Excluindo...') }}' : '{{ __('Excluir papel') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>