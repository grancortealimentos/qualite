{{-- resources/views/components/layouts/sidebar.blade.php --}}
{{-- Backdrop escuro no mobile quando a sidebar abre --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-50 bg-gray-900/50 lg:hidden">
</div>

<aside x-show="sidebarOpen || window.innerWidth >= 1024" x-cloak
    class="fixed inset-y-0 start-0 z-60 w-64 bg-white dark:bg-neutral-800 border-e border-gray-200 dark:border-neutral-700 lg:block lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" style="transition: transform .3s">

    <div class="flex flex-col h-full">
        {{-- Logo --}}
        <div class="px-6 flex items-center justify-center dark:border-neutral-700">
            <a href="{{ route('dashboard') }}">
                <x-logo class="h-12 w-auto" />
            </a>
        </div>

        {{-- Navegação --}}
        <nav class="flex-1 overflow-y-auto p-3">
            <ul class="flex flex-col space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" @class([
                        'flex items-center gap-x-3 py-2 px-2.5 text-sm rounded-lg',
                        'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-200' => request()->routeIs('dashboard'),
                        'text-gray-800 hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700' => !request()->routeIs('dashboard'),
                    ])>
                        <i class="bi bi-house {{ request()->routeIs('dashboard') ? 'hidden' : '' }}"></i>
                        <i class="bi bi-house-fill {{ request()->routeIs('dashboard') ? '' : 'hidden' }}"></i>
                        Página inicial
                    </a>
                </li>

                {{-- Item com submenu (accordion em Alpine) --}}
                <li x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-800 dark:text-neutral-200 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">

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
                            <a href="#"
                                class="flex py-2 px-2.5 text-sm text-gray-800 dark:text-neutral-200 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">
                                Pessoas
                            </a>
                        </li>
                        <!-- <li>
                            <a href="#"
                                class="flex py-2 px-2.5 text-sm text-gray-800 dark:text-neutral-200 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">
                                Criar
                            </a>
                        </li> -->
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>