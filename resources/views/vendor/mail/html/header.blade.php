{{-- original--}}
{{-- <tr>
<td class="header">
<a href="{{ $url }}">
{{ $slot }}
</a>
</td>
</tr> --}}
<tr>
<td class="header" style="padding: 20px 0 10px 0; text-align:center;">
    @if(trim($url) !== '')
        <a href="{{ $url }}" style="display:inline-block;">
            {{ $slot }}
        </a>
    @else
        {{ $slot }}
    @endif
</td>
</tr>