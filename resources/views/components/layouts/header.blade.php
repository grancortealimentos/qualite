{{-- resources/views/components/layouts/header.blade.php --}}
<header
    class="sticky top-0 inset-x-0 z-40 w-full bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 py-2.5 lg:ps-64">
    <nav class="px-4 sm:px-6 flex items-center gap-x-3">

        {{-- Botão de menu (só mobile) --}}
        <button type="button" @click="sidebarOpen = true"
            class="lg:hidden size-9 flex justify-center items-center rounded-lg text-gray-800 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-700">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" x2="21" y1="6" y2="6" />
                <line x1="3" x2="21" y1="12" y2="12" />
                <line x1="3" x2="21" y1="18" y2="18" />
            </svg>
        </button>

        {{-- Busca --}}
        <!-- <div class="hidden md:block">
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </div>
                <input type="text" placeholder="Buscar"
                    class="py-2 ps-10 pe-4 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 rounded-lg text-sm text-gray-800 dark:text-neutral-200 focus:border-blue-700 focus:ring-blue-700">
            </div>
        </div> -->

        {{-- Avatar + dropdown --}}
        <div class="ms-auto" x-data="{ open: false }">
            <button @click="open = !open" type="button"
                class="size-9.5 inline-flex justify-center items-center rounded-full bg-gray-100 dark:bg-neutral-700 text-sm font-medium text-gray-800 dark:text-neutral-200">
                {{ Str::of(auth()->user()?->name)->substr(0, 1)->upper() }}
            </button>

            <div x-show="open" x-cloak @click.outside="open = false"
                class="absolute end-4 mt-2 min-w-60 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-md rounded-lg">
                <div class="py-3 px-5 bg-gray-100 dark:bg-neutral-700 rounded-t-lg">
                    <p class="text-sm text-gray-500 dark:text-neutral-400">Conectado como</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">{{ auth()->user()?->email }}</p>
                </div>
                <div class="p-1.5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-start flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-800">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</header>