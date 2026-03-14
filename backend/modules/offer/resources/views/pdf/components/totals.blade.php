<tr>
    <td colspan="6" class="blank"></td>
</tr>
@foreach ($totals as $groups)
    <tr class="invoice-totals-group">
        <td colspan="3"></td>
        <td colspan="3">
            <table>
                <tbody>
                    @foreach ($groups as $total)
                        @php
                            $is_bold = $total['is_bold'] ?? false;
                            $hasPrefix = filled($total['prefix'] ?? '');
                        @endphp
                        <tr class="summary-row" style="width: 100%">
                            @if ($hasPrefix)
                                <td class="prefix">
                                    {{ $total['prefix'] ?? '' }}
                                </td>
                            @else
                                <td class="prefix blank"></td>
                            @endif

                            @if ($total['new_line'] ?? false)
                                <td class="blank"></td>

                            @else
                                <td width="30%" class="{{ $is_bold ? 'bold' : '' }}">
                                    <div class="label">
                                        <div class="primary" style="text-align: right; margin-right: 16px;">
                                            {{ $total['label'] }}:
                                        </div>
                                    </div>
                                </td>
                                <td width="20%" class="amount-cell">
                                    <div class="tw-text-right amount {{ $is_bold ? 'bold' : '' }}">
                                        {{ $total['amount_prefix'] ?? '' }}
                                        {{ $total['amount_formatted'] }}
                                    </div>
                                </td>
                            @endif
                        </tr>
                        {{-- Border bottoms --}}
                        @if ($total['border_bottom_single'] ?? false)
                            <tr>
                                <td></td>
                                <td colspan="2" class="single-border"></td>
                            </tr>
                        @endif
                        @if ($total['border_bottom_double'] ?? false)
                            <tr>
                                <td></td>
                                <td colspan="2" class="double-underline"></td>
                            </tr>
                        @endif
                        {{-- Border bottoms --}}
                    @endforeach
                    <tr>
                        <td colspan="3" class="blank"></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
@endforeach