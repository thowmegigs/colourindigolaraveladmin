@props(['cartId', 'item'])
<div class="modal fade" id="cartItemModal{{ $cartId }}" tabindex="-1" aria-labelledby="quickViewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Additional Added</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    @php
                        $addon_items = !empty($item['addon_items']) ? json_decode($item['addon_items'], true) : [];
                    @endphp
                    @if (!empty($addon_items))
                        <p class="p-0 m-0"><b class="text-danger">Addon
                                Items</b></p>
                        <table class="table">
                            <tbody>

                                <tr>
                                    <th class="">Item Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                </tr>
                                @foreach ($addon_items as $t)
                                    <tr>
                                        <td>{{ $t['name'] }}</td>
                                        <td>{{ $t['qty'] }}</td>
                                        <td>{{ getCurrency() }}{{ $t['price'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    @php
                        $addon_products = !empty($item['addon_products']) ? json_decode($item['addon_products'], true) : [];
                    @endphp
                    @if (!empty($addon_products))
                        <p class="p-0 m-0"><b class="text-danger">Addon
                                Products</b></p>
                        <table class="table">
                            <tbody>

                                <tr>
                                    <th class="">Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                </tr>
                                @foreach ($addon_items as $t)
                                    <tr>
                                        <td>{{ $t['name'] }}</td>
                                        <td>{{ $t['qty'] }}</td>
                                        <td>{{ getCurrency() }}{{ $t['price'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" id="cartItemOffcanvas{{ $cartId }}">
    <div class="offcanvas-header">
        <h1 class="offcanvas-title">Additional Detail</h1>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="">
            @php
                $addon_items = !empty($item['addon_items']) ? json_decode($item['addon_items'], true) : [];
            @endphp
            @if (!empty($addon_items))
                <p class="p-0 m-0"><b class="text-danger">Addon
                        Items</b></p>
                <table class="table">
                    <tbody>

                        <tr>
                            <th class="">Item Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                        @foreach ($addon_items as $t)
                            <tr>
                                <td>{{ $t['name'] }}</td>
                                <td>{{ $t['qty'] }}</td>
                                <td>{{ getCurrency() }}{{ $t['price'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @php
                $addon_products = !empty($item['addon_products']) ? json_decode($item['addon_products'], true) : [];
            @endphp
            @if (!empty($addon_products))
                <p class="p-0 m-0"><b class="text-danger">Addon
                        Products</b></p>
                <table class="table">
                    <tbody>

                        <tr>
                            <th class="">Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                        @foreach ($addon_products as $t)
                           
                            <tr>
                                <td>{{ $t['addon_name'] }}<br>
                                    @if (!empty($t['variant_name']))
                                       <b> ({{ $t['variant_name'] }})</b>
                                    @endif
                                </td>
                                <td>{{ $t['qty'] }}</td>
                                <td>{{ getCurrency() }}{{ $t['amount'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
