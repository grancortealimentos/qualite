{{-- resources/views/components/toast-container.blade.php --}}
<div class="fixed top-5 end-5 z-90 flex flex-col gap-y-2">
    @if (session('status'))
        <x-toast type="success">{{ session('status') }}</x-toast>
    @endif

    @if (session('error'))
        <x-toast type="error">{{ session('error') }}</x-toast>
    @endif
</div>