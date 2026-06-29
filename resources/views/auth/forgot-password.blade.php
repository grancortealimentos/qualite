{{-- resources/views/auth/forgot-password.blade.php --}}
<x-layouts.guest :title="'Esqueci minha senha'">
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-2xs">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h3 class="block text-2xl font-bold text-gray-800 dark:text-neutral-200">Esqueci minha senha</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-neutral-300">
                    Lembrou a senha?
                    <a class="text-blue-600 dark:text-blue-500 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium" href="{{ route('login') }}">
                        Voltar ao login
                    </a>
                </p>
            </div>

            <div class="mt-5">

                <p class="mb-4 text-sm text-gray-600 dark:text-neutral-300">
                    Digite seu e-mail e enviaremos um link para você redefinir sua senha.
                </p>

                <!-- Form -->
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="grid gap-y-4">

                        <!-- E-mail -->
                        <div>
                            <label for="email" class="block text-sm mb-2 text-gray-800 dark:text-neutral-200">E-mail</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="py-2.5 sm:py-3 px-4 block w-full bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm text-gray-800 dark:text-neutral-200 placeholder:text-gray-500 dark:placeholder:text-neutral-400 focus:border-blue-700 dark:focus:border-blue-600 focus:ring-blue-700 dark:focus:ring-blue-600 disabled:opacity-50 disabled:pointer-events-none @error('email') !border-red-500 @enderror"
                                    required aria-describedby="email-error">
                            </div>
                            @error('email') <p class="text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 dark:bg-blue-500 border border-transparent text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-hidden focus:bg-blue-700 dark:focus:bg-blue-600 disabled:opacity-50 disabled:pointer-events-none">Enviar link de redefinição</button>
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
    </div>
</x-layouts.guest>