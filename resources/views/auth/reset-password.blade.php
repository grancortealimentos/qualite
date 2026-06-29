{{-- resources/views/auth/reset-password.blade.php --}}
<x-layouts.guest :title="'Redefinir senha'">
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-2xs">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h3 class="block text-2xl font-bold text-gray-800 dark:text-neutral-200">Redefinir senha</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-neutral-300">
                    Escolha uma nova senha para sua conta.
                </p>
            </div>

            <div class="mt-5">
                <!-- Form -->
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    {{-- Token do link do e-mail (não visível ao usuário) --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="grid gap-y-4">

                        <!-- E-mail -->
                        <div>
                            <label for="email" class="block text-sm mb-2 text-gray-800 dark:text-neutral-200">E-mail</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                                    class="py-2.5 sm:py-3 px-4 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 focus:border-blue-700 dark:focus:border-blue-600 focus:ring-blue-700 dark:focus:ring-blue-600 disabled:opacity-50 disabled:pointer-events-none @error('email') !border-red-500 @enderror"
                                    required aria-describedby="email-error">
                            </div>
                            @error('email') <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- Nova senha -->
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm mb-2 text-gray-800 dark:text-neutral-200">Nova senha</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" id="password" name="password"
                                    class="py-2.5 sm:py-3 ps-4 pe-10 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 focus:border-blue-700 dark:focus:border-blue-600 focus:ring-blue-700 dark:focus:ring-blue-600 disabled:opacity-50 disabled:pointer-events-none @error('password') !border-red-500 @enderror"
                                    required aria-describedby="password-error">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 inset-e-0 flex items-center z-20 px-3 cursor-pointer text-gray-400 dark:text-neutral-500 rounded-e-md focus:outline-hidden focus:text-blue-700 dark:focus:text-blue-600">
                                    <svg class="shrink-0 size-3.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <g x-show="!show">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </g>
                                        <g x-show="show" x-cloak>
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </g>
                                    </svg>
                                </button>
                            </div>
                            @error('password') <p class="text-xs text-red-600 mt-2" id="password-error">{{ $message }}</p> @enderror
                        </div>

                        <!-- Confirmar nova senha -->
                        <div x-data="{ show: false }">
                            <label for="password_confirmation" class="block text-sm mb-2 text-gray-800 dark:text-neutral-200">Confirmar nova senha</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                                    class="py-2.5 sm:py-3 ps-4 pe-10 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 focus:border-blue-700 dark:focus:border-blue-600 focus:ring-blue-700 dark:focus:ring-blue-600 disabled:opacity-50 disabled:pointer-events-none"
                                    required>
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 inset-e-0 flex items-center z-20 px-3 cursor-pointer text-gray-400 dark:text-neutral-500 rounded-e-md focus:outline-hidden focus:text-blue-700 dark:focus:text-blue-600">
                                    <svg class="shrink-0 size-3.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <g x-show="!show">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </g>
                                        <g x-show="show" x-cloak>
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </g>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 dark:bg-blue-500 border border-transparent text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-hidden focus:bg-blue-700 dark:focus:bg-blue-600 disabled:opacity-50 disabled:pointer-events-none">Redefinir senha</button>
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
    </div>
</x-layouts.guest>