{{-- resources/views/vendor/mail/html/header.blade.php --}}
@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="{{ asset('images/logo.png') }}" class="logo" alt="Gran Corte Alimentos"
                    style="height: 60px; max-height: 60px; width: auto;">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>