{{-- resources/views/components/layouts/sidebar.blade.php --}}
{{-- Backdrop escuro no mobile quando a sidebar abre --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-50 bg-black/60 lg:hidden">
</div>

{{-- Botão de menu (só mobile) --}}
<button type="button" @click="sidebarOpen = true"
    class="lg:hidden fixed top-3 start-3 z-40 size-9 flex justify-center items-center rounded-lg bg-surface border border-border text-ink shadow-2xs hover:bg-surface-hover">
    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" x2="21" y1="6" y2="6" />
        <line x1="3" x2="21" y1="12" y2="12" />
        <line x1="3" x2="21" y1="18" y2="18" />
    </svg>
</button>

<aside x-show="sidebarOpen || window.innerWidth >= 1024" x-cloak
    class="fixed inset-y-0 start-0 z-60 w-64 bg-sidebar border-e border-border lg:block lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" style="transition: transform .3s">

    <div class="flex flex-col h-full">
        {{-- Logo --}}
        <div class="px-6 pt-3 pb-1 flex items-center justify-center border-b border-border">
            <a href="{{ route('dashboard') }}">
                <x-logo class="h-24 w-auto" />
            </a>
        </div>

        {{-- Navegação --}}
        <nav class="flex-1 overflow-y-auto p-3">
            <ul class="flex flex-col space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" @class([
                        'flex items-center gap-x-3 py-2 px-2.5 text-sm rounded-lg',
                        'bg-primary/15 text-primary-light font-medium' => request()->routeIs('dashboard'),
                        'text-ink-muted hover:bg-surface hover:text-ink' => !request()->routeIs('dashboard'),
                    ])>
                        <i class="bi bi-house {{ request()->routeIs('dashboard') ? 'hidden' : '' }}"></i>
                        <i class="bi bi-house-fill {{ request()->routeIs('dashboard') ? '' : 'hidden' }}"></i>
                        Página inicial
                    </a>
                </li>

                {{-- Item com submenu (accordion em Alpine) --}}
                <li x-data="{ open: {{ request()->routeIs('pessoas.*') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-ink-muted rounded-lg hover:bg-surface hover:text-ink">

                        <i class="bi bi-collection" :class="open && 'hidden'"></i>
                        <i class="bi bi-collection-fill" :class="!open && 'hidden'"></i>

                        Cadastros
                        <svg class="ms-auto size-4 transition-transform" :class="open && 'rotate-180'"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <ul x-show="open" x-collapse class="ps-8 pt-1 space-y-1">
                        <li>
                            <a href="{{ route('pessoas.index') }}" @class([
                                'flex py-2 px-2.5 text-sm rounded-lg',
                                'bg-primary/15 text-primary-light font-medium' => request()->routeIs('pessoas.*'),
                                'text-ink-muted hover:bg-surface hover:text-ink' => !request()->routeIs('pessoas.*'),
                            ])>
                                Pessoas
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        {{-- Usuário (fixo na base da sidebar) --}}
        <div class="mt-auto border-t border-border p-3" x-data="{ open: false }">
            <button @click="open = !open" type="button"
                class="w-full flex items-center gap-x-3 py-2 px-2.5 rounded-lg hover:bg-surface">
                <span
                    class="size-9 shrink-0 inline-flex justify-center items-center rounded-full bg-primary text-sm font-medium text-white">
                    {{ Str::of(auth()->user()?->name)->substr(0, 1)->upper() }}
                </span>
                <span class="min-w-0 text-start">
                    <span class="block text-sm font-medium text-ink truncate">
                        {{ auth()->user()?->name }}
                    </span>
                    <span class="block text-xs text-ink-muted truncate">
                        {{ auth()->user()?->email }}
                    </span>
                </span>
                <svg class="ms-auto shrink-0 size-4 text-ink-muted transition-transform" :class="open && 'rotate-180'"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m18 15-6-6-6 6" />
                </svg>
            </button>

            <div x-show="open" x-cloak @click.outside="open = false"
                class="absolute start-3 end-3 bottom-full mb-2 bg-surface border border-border shadow-xl rounded-lg">
                <div class="py-3 px-5 bg-surface-hover rounded-t-lg">
                    <p class="text-sm text-ink-muted">Conectado como</p>
                    <p class="text-sm font-medium text-ink">{{ auth()->user()?->email }}</p>
                </div>
                <div class="p-1.5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-start flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-ink hover:bg-surface-hover">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
        {{-- Fim usuário --}}
    </div>
</aside>