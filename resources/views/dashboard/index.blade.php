<x-layouts.app :title="__('Página inicial')">

    <div class="space-y-6">

        {{-- Boas-vindas --}}
        <div class="bg-surface border border-border rounded-xl p-6">
            <h1 class="text-2xl font-semibold text-ink">
                Bem-vindo(a), {{ auth()->user()?->name }} 👋
            </h1>
            <p class="mt-1 text-sm text-ink-muted">
                Aqui está um resumo do sistema hoje.
            </p>
        </div>

        {{-- Cards de estatísticas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-surface border border-border rounded-xl p-5">
                <div class="flex items-center gap-x-3">
                    <span
                        class="inline-flex justify-center items-center size-10 rounded-lg bg-primary/15 text-primary-light">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink-muted">Pessoas</p>
                        <p class="text-xl font-semibold text-ink">128</p>
                    </div>
                </div>
            </div>

            <div class="bg-surface border border-border rounded-xl p-5">
                <div class="flex items-center gap-x-3">
                    <span
                        class="inline-flex justify-center items-center size-10 rounded-lg bg-accent/15 text-accent-light">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2" />
                            <path d="M18 14h-8" />
                            <path d="M15 18h-5" />
                            <path d="M10 6h8v4h-8V6Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink-muted">Notas fiscais</p>
                        <p class="text-xl font-semibold text-ink">342</p>
                    </div>
                </div>
            </div>

            <div class="bg-surface border border-border rounded-xl p-5">
                <div class="flex items-center gap-x-3">
                    <span
                        class="inline-flex justify-center items-center size-10 rounded-lg bg-emerald-500/15 text-emerald-400">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink-muted">Ativos</p>
                        <p class="text-xl font-semibold text-ink">97</p>
                    </div>
                </div>
            </div>

            <div class="bg-surface border border-border rounded-xl p-5">
                <div class="flex items-center gap-x-3">
                    <span class="inline-flex justify-center items-center size-10 rounded-lg bg-warn/15 text-warn">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink-muted">Cadastros hoje</p>
                        <p class="text-xl font-semibold text-ink">6</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloco de ações rápidas --}}
        <div class="bg-surface border border-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-ink mb-4">Ações rápidas</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('pessoas.create') }}"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Nova pessoa
                </a>
                <button type="button"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-accent text-white hover:bg-accent-hover focus:outline-hidden">
                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M7 12h10" />
                        <path d="M10 18h4" />
                    </svg>
                    Filtros Avançados
                </button>
                <button type="button"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-warn text-canvas hover:bg-warn-hover focus:outline-hidden">
                    Relatórios
                </button>
                <a href="{{ route('pessoas.index') }}"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-transparent border border-border text-ink hover:bg-surface-hover focus:outline-hidden">
                    Ver pessoas
                </a>
            </div>
        </div>

    </div>

</x-layouts.app>