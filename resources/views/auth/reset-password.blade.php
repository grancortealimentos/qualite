{{-- resources/views/auth/reset-password.blade.php --}}
<x-layouts.guest :title="'Redefinir senha'">
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-2xs">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h3 class="block text-2xl font-bold text-gray-800 dark:text-neutral-200">Redefinir senha</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-neutral-300">Escolha uma nova senha para sua conta.</p>
            </div>

            <div class="mt-5">
                <x-password-fields label="Nova senha" confirm-label="Confirmar nova senha">
                    Redefinir senha
                </x-password-fields>
            </div>
        </div>
    </div>
</x-layouts.guest>