@props(['product'])
@php
    $p = $product;

    $attributes = $p->attributes != null ? array_column($p->attributes, 'name') : [];
@endphp
<div class="modal fade" id="variantModal{{ $p->id }}" tabindex="-1" aria-labelledby="quickViewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Select {{ $p->name }} Variants</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">


                <ul class="list-group list-group-flush">
                    @foreach ($p->variants as $item)
                        @php
                            $cart = session('shoppingCart', []);
                            $unique_cart_item_id = $p->id . $item->id;
                            $unique_cart_item_id = trim($unique_cart_item_id);
                            // dd($unique_cart_item_id);
                            $variant_qty = !empty($cart) && isset($cart[$unique_cart_item_id]) ? $cart[$unique_cart_item_id]['qty'] : 0;
                        @endphp
                        <li class="list-group-item py-5">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    <!-- img -->
                                    <img src="{{ asset('storage/products/' . $p->id . '/thumbnail/' . $p->thumbnail['tiny']) }}"
                                        alt="">
                                    <!-- text -->
                                    <div class="ms-4">
                                        <h5 class="mb-0 h6 h6">{{ str_replace('_', ' ', $item->name) }}</h5>
                                        <div>
                                            <span class="small">{{ getCurrency() }}{{ $item->sale_price }}</span>
                                            <span
                                                class="text-decoration-line-through text-muted">{{ getCurrency() }}{{ $item->price }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <!-- button -->
                                    <div id="btn-container-variant-{{ $item->id }}" style="width:134px;">
                                        @if ($variant_qty < 1)
                                            <div style="width:72px;margin-left:10px;">
                                                <a href='javascript:void(0)' class="btn btn-primary btn-sm"
                                                    id="variant-add_{{ $item->id }}"
                                                    style="display:{{ $variant_qty < 1 ? 'block' : 'none' }}"
                                                    onclick="addToCart('{!! $p->name !!}','{!! $p->id !!}','{!! $p->price !!}','{!! $p->sale_price !!}','{!! $p->sgst !!}','{!! $p->cgst !!}','{!! $p->igst !!}','{!! $p->unit !!}','{!! $item->id !!}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="16" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="feather feather-plus">
                                                        <line x1="12" y1="5" x2="12"
                                                            y2="19">
                                                        </line>
                                                        <line x1="5" y1="12" x2="19"
                                                            y2="12">
                                                        </line>
                                                    </svg>
                                                    Add
                                                </a>
                                            </div>
                                        @else
                                            <div class="container" id="variant-counter-{{ $item->id }}">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <button class="btn btn-primary btn-sm" id="minus-btn"
                                                            onclick="decrementCounter('{!! $p->id !!}','{!! $item->id !!}')">&minus;</button>
                                                    </div>
                                                    <input type="number" id="qty-{{ $p->id }}"
                                                        class="text-center form-control form-control-sm"
                                                        value="{{ $variant_qty }}" min="1">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary btn-sm" id="plus-btn"
                                                            onclick="incrementCounter('{!! $p->id !!}','{!! $item->id !!}')">&plus;</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach



            </div>

        </div>
    </div>
</div>

