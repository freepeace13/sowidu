@props(['items', 'header', 'footer'])

<table {{ $attributes->merge(['class' => 'pdf-table']) }}>
    @isset($header)
        <thead>
            <tr>
                @if (is_array($header))
                    @foreach ($header as $label)
                        <th>{{ $label }}</th>
                    @endforeach
                @else
                    {{ $header }}
                @endif
            </tr>
        </thead>
    @endisset
    <tbody>
        {{ $slot }}
    </tbody>
    @isset ($footer)
        <tfoot>
            {{ $footer }}
        </tfoot>
    @endisset
</table>
