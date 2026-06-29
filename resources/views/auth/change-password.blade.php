{{-- resources/views/auth/change-password.blade.php --}}
<x-layouts.guest :title="'Alterar senha'">
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-2xs">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h3 class="block text-2xl font-bold text-gray-800 dark:text-neutral-200">Alterar senha</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-neutral-300">
                    Por segurança, defina uma nova senha para continuar.
                </p>
            </div>

            <div class="mt-5">
                <form method="POST" action="{{ route('password.change') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-y-4">
                        <x-password-fields label="Nova senha" confirm-label="Confirmar nova senha">
                            Alterar senha
                        </x-password-fields>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.guest>