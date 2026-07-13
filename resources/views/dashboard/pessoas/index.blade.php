<x-layouts.app :title="__('Pessoas')">

    @php
        // 'ativo' controla o escopo da listagem:
        //   ''      => padrão: somente ativos
        //   '1'     => somente ativos (explícito)
        //   '0'     => somente inativos
        //   'todos' => sem filtro de status
        $filtroAtivo = request('ativo', '');
        $temFiltroAvancado = $filtroAtivo !== '' || request()->filled('created_from');
        $temQualquerFiltro = $temFiltroAvancado || request()->filled('search');
    @endphp

    <div class="flex flex-col gap-4" x-data="{ filtrosAbertos: {{ $temFiltroAvancado ? 'true' : 'false' }} }">

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

                <a href="{{ route('pessoas.create') }}"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary border border-transparent text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    {{ __('Novo') }}
                </a>
            </div>
            {{-- End Header --}}

            {{-- Um único <form> envolve busca E filtros avançados --}}
                <form method="GET" action="{{ route('pessoas.index') }}">

                    {{-- Linha de busca --}}
                    <div class="px-6 py-3 flex flex-wrap items-center gap-2">
                        <div class="relative flex-1 min-w-56">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3">
                                <svg class="shrink-0 size-4 text-ink-muted" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="py-2 ps-9 pe-3 block w-full bg-canvas border-border rounded-lg text-sm text-ink placeholder:text-ink-muted focus:border-primary focus:ring-primary"
                                placeholder="{{ __('Buscar por nome, documento ou e-mail...') }}">
                        </div>

                        <button type="submit"
                            class="p-2.5 inline-flex justify-center items-center rounded-lg bg-primary border border-transparent text-white hover:bg-primary-hover focus:outline-hidden"
                            aria-label="{{ __('Buscar') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </button>

                        {{-- Filtros Avançados: no FIM da linha --}}
                        <button type="button" @click="filtrosAbertos = !filtrosAbertos"
                            class="ms-auto py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-accent border border-transparent text-white hover:bg-accent-hover focus:outline-hidden">
                            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18" />
                                <path d="M7 12h10" />
                                <path d="M10 18h4" />
                            </svg>
                            {{ __('Filtros Avançados') }}
                            @if ($temFiltroAvancado)
                                <span
                                    class="inline-flex items-center justify-center size-5 rounded-full bg-white/20 text-[10px] font-semibold">
                                    {{ collect([$filtroAtivo, request('created_from')])->filter(fn($v) => filled($v))->count() }}
                                </span>
                            @endif
                            <svg class="shrink-0 size-3.5 transition-transform" :class="filtrosAbertos && 'rotate-180'"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                    </div>

                    {{-- Card de filtros avançados: abre ABAIXO, dentro do mesmo form --}}
                    <div x-show="filtrosAbertos" x-collapse x-cloak>
                        <div class="px-6 py-5 border-t border-border bg-canvas/40">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                                <div>
                                    <label
                                        class="block text-xs font-medium text-ink-muted mb-1.5">{{ __('Status') }}</label>
                                    <select name="ativo"
                                        class="py-2 px-3 block w-full bg-canvas border-border rounded-lg text-sm text-ink focus:border-primary focus:ring-primary">
                                        <option value="" @selected($filtroAtivo === '')>
                                            {{ __('Somente ativos (padrão)') }}
                                        </option>
                                        <option value="0" @selected($filtroAtivo === '0')>{{ __('Somente inativos') }}
                                        </option>
                                        <option value="todos" @selected($filtroAtivo === 'todos')>{{ __('Todos') }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-medium text-ink-muted mb-1.5">{{ __('Cadastrado a partir de') }}</label>
                                    <input type="date" name="created_from" value="{{ request('created_from') }}"
                                        class="py-2 px-3 block w-full bg-canvas border-border rounded-lg text-sm text-ink focus:border-primary focus:ring-primary">
                                </div>

                                <div class="flex items-end gap-2">
                                    <button type="submit"
                                        class="flex-1 py-2 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-accent text-white hover:bg-accent-hover focus:outline-hidden">
                                        {{ __('Aplicar filtros') }}
                                    </button>

                                    @if ($temQualquerFiltro)
                                        <a href="{{ route('pessoas.index') }}"
                                            class="py-2 px-4 inline-flex justify-center items-center text-sm font-medium rounded-lg bg-surface-hover border border-border text-ink-muted hover:text-ink">
                                            {{ __('Limpar') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Chips dos filtros ativos --}}
                @if ($temQualquerFiltro)
                    <div class="px-6 py-3 border-t border-border flex flex-wrap items-center gap-2">
                        <span class="text-xs text-ink-muted">{{ __('Filtros ativos:') }}</span>

                        @if (request()->filled('search'))
                            <span
                                class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-primary/15 text-primary-light">
                                {{ __('Busca') }}: "{{ request('search') }}"
                            </span>
                        @endif

                        @if ($filtroAtivo === '0')
                            <span
                                class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-danger/15 text-danger">
                                {{ __('Somente inativos') }}
                            </span>
                        @elseif ($filtroAtivo === 'todos')
                            <span
                                class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-accent/15 text-accent-light">
                                {{ __('Ativos e inativos') }}
                            </span>
                        @endif

                        @if (request()->filled('created_from'))
                            <span
                                class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs bg-warn/15 text-warn">
                                {{ __('Desde') }} {{ \Carbon\Carbon::parse(request('created_from'))->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                @endif
        </div>
        {{-- Fim Bloco 1 --}}

        {{-- Bloco 2: Somente a tabela --}}
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="bg-surface border border-border rounded-xl overflow-hidden">

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
                                    <tr class="hover:bg-surface-hover {{ $pessoa->eh_ativo ? '' : 'opacity-60' }}">
                                        <td class="size-px whitespace-nowrap">
                                            <div class="px-6 py-3">
                                                <span
                                                    class="text-sm font-medium text-primary-light">#{{ $pessoa->id }}</span>
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
                                                    <span
                                                        class="block text-sm text-ink">{{ $pessoa->documento_formatado }}</span>
                                                    <span
                                                        class="block text-xs text-ink-muted">{{ $pessoa->tipo_documento }}</span>
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
                                                <a href="{{ route('pessoas.edit', $pessoa) }}"
                                                    class="p-2 inline-flex items-center justify-center rounded-lg bg-primary hover:bg-primary-hover text-white"
                                                    title="{{ __('Editar') }}">
                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    </svg>
                                                </a>

                                                <form action="{{ route('pessoas.toggle-status', $pessoa) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-2 inline-flex items-center justify-center rounded-lg bg-warn hover:bg-warn-hover text-canvas"
                                                        title="{{ $pessoa->eh_ativo ? __('Desativar') : __('Ativar') }}">
                                                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg"
                                                            width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M12 2v10" />
                                                            <path d="M18.4 6.6a9 9 0 1 1-12.77.04" />
                                                        </svg>
                                                    </button>
                                                </form>

                                                <form action="{{ route('pessoas.destroy', $pessoa) }}" method="POST"
                                                    onsubmit="return confirm('{{ __('Tem certeza que deseja excluir esta pessoa?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 inline-flex items-center justify-center rounded-lg bg-danger hover:bg-danger/80 text-white"
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
                                                </form>
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
                                                @if ($temQualquerFiltro)
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
                        <div
                            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-border">
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
        {{-- Fim Bloco 2 --}}
    </div>

</x-layouts.app>