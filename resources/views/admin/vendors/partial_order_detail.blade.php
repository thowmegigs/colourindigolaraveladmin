<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th>Variant</th>
                <th class="text-end">Price</th>
                <th class="text-end">Discount</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order_items as $item)
                @php
                    // {"Size":"L","Color":"Red"}   etc.
                    $attrs = json_decode($item->atributes_json, true) ?? [];
                    $size  = count($attrs) > 0 ? ($attrs['Size'] ?? '—') : '—';
                    $color = count($attrs) > 0 ? ($attrs['Color'] ?? '—') : '—';
                    $lineTotal = ($item->sale_price - $item->discount_share) * $item->qty;
                @endphp

                <tr>
                    {{-- Item & image --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('storage/products/'.$item->product_id.'/' . $item->image) }}"
                                 alt="{{ $item->name }}" width="50" height="50" class="rounded">
                            <span>{{ $item->name }}</span>
                        </div>
                    </td>

                    {{-- Variant badges --}}
                    <td>
                        <span class="badge bg-light text-dark">Size: {{ $size }}</span>
                        <span class="badge bg-light text-dark">Color: {{ $color }}</span>
                    </td>

                    {{-- Price / Discount / Qty / Total --}}
                    <td class="text-end">{{ number_format($item->sale_price, 2) }}</td>
                    <td class="text-end">
                        {{ $item->discount_share > 0 ? '-' . number_format($item->discount_share, 2) : '0.00' }}
                    </td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
