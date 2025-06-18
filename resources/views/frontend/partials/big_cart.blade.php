@if (count(session('shoppingCart', [])) > 0)
@php
    $total = 0;
    $subtotal = 0;
    $discount = 0;
@endphp
<div class="row">

    <div class="col-lg-8 col-md-7">
        <div class="py-3">
            <!-- alert -->
            @if ($minimum_cart_amount_offer)
                <div class="alert alert-danger p-2" role="alert">
                    Shop more worth
                    {{ getCurrency() }}{{ $minimum_cart_amount_offer['target_amount'] }} to get
                    {{ $minimum_cart_amount_offer['target_amount'] }} % discount

                </div>
            @endif
            @if (!empty($applied_coupons))
                <div class="d-flex justify-content-start alert alert-success" style="height:55px">
                    <i class="bi bi-check-circle-fill " style="color:green"></i>&nbsp;&nbsp;&nbsp;
                    <p class="text-success d-inline-block" data-bs-toggle="modal"
                        data-bs-target="#applied_offers_modal">Offers Applied &nbsp;&nbsp;&nbsp;<b
                            style="font-size:12px;color:black">Show All</b> </p>

                    <x-frontend.applied_offers :offers="$applied_coupons" />&nbsp;&nbsp;&nbsp;
                    <a class="text-danger"> <i class="bi bi-chevron-down" href="javascript:void(0)"
                            data-bs-toggle="modal" data-bs-target="#applied_offers_modal"></i></a>
                </div>
            @endif
            <ul class="list-group list-group-flush">
                @foreach (session('shoppingCart') as $cart_id => $item)
                    <x-frontend.cart_additional_info_modal :cartId="$cart_id" :item="$item" />
                    @php
                        $additional_amount = 0;
                        if ($item['addon_items'] != null) {
                            $items = json_decode($item['addon_items'], true);
                            foreach ($items as $g) {
                                $additional_amount += $g['qty'] * $g['price'];
                            }
                        }
                        if ($item['addon_products'] != null) {
                            $items = json_decode($item['addon_products'], true);
                            foreach ($items as $g) {
                                $additional_amount += $g['qty'] * $g['amount'];
                            }
                        }
                        $cart_discount=session('cart_discount');
                        $total +=($item['price']+ $additional_amount)* $item['qty'];
                        $current_item_net_amount=$item['net_amount'];
                        $subtotal += $current_item_net_amount;
                        $discount += ($item['price'] - $item['sale_price']) * $item['qty'];
                    @endphp
                    <li class="list-group-item py-3 ps-0 border-top"
                        id="{{ $item['productId'] . $item['variant_id'] }}">
                        <!-- row -->
                        <div class="row align-items-center">
                            <div class="col-6 col-md-6 col-lg-7">
                                <div class="d-flex">
                                    <img src="{{ $item['image'] }}" alt="Ecommerce"
                                        class="icon-shape icon-xxl" />
                                    <div class="ms-3">
                                        <!-- title -->
                                        <div class="d-flex flex-column">
                                            <a href="pages/shop-single.html" class="text-inherit">
                                                <h6 class="mb-0">{{ $item['name'] }}</h6>
                                            </a>
                                            <span><small
                                                    class="text-muted">{{ str_replace('_', ' ', $item['variant_name']) }}</small></span>
                                            @if (!empty($item['addon_items']) || !empty($item['addon_products']))
                                                <div class="d-flex"
                                                    style="min-width:80px!important">
                                                    <a href="javascript:void(0)"
                                                        style="font-size:12px!important"
                                                        class="link-danger d-none d-md-block"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cartItemModal{{ $cart_id }}">View
                                                        Detail
                                                        <i class="bi bi-chevron-down"></i></a>
                                                    <a href="javascript:void(0)"
                                                        style="font-size:12px!important"
                                                        class="link-danger d-md-none"
                                                        data-bs-toggle="offcanvas"
                                                        data-bs-target="#cartItemOffcanvas{{ $cart_id }}">View
                                                        Detail
                                                        <i class="bi bi-chevron-down"></i></a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-2 small lh-1">
                                            <a href="javascript:deleteCartItem('{!! $item['productId'] !!}',true,'{!! $item['variant_id'] !!}')"
                                                class="text-decoration-none text-inherit">
                                                <span class="me-1 align-text-bottom">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="feather feather-trash-2 text-success">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path
                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                        </path>
                                                        <line x1="10" y1="11"
                                                            x2="10" y2="17"></line>
                                                        <line x1="14" y1="11"
                                                            x2="14" y2="17"></line>
                                                    </svg>
                                                </span>
                                                <span class="text-muted">Remove</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- input group -->
                            <div class="col-4 col-md-3 col-lg-3">
                                <!-- input -->
                                <!-- input -->
                                <div class="input-group input-spinner">
                                    <input type="button" value="-"
                                        class="button-minus btn btn-sm" data-field="quantity"
                                        onClick="decrementCounter('{!! $item['productId'] !!}','{!! $item['variant_id'] !!}',false)" />
                                    <input type="number" step="1" max="10"
                                        id="qty-{{ $item['productId'] }}{{ $item['variant_id'] }}"
                                        value="{{ $item['qty'] }}" name="quantity"
                                        class="quantity-field form-control-sm form-input"
                                        readonly />
                                    <input type="button" value="+"
                                        class="button-plus btn btn-sm" data-field="quantity"
                                        onClick="incrementCounter('{!! $item['productId'] !!}','{!! $item['variant_id'] !!}',false)" />
                                </div>
                            </div>
                            <!-- price -->
                            <div class="col-2 text-lg-end text-start text-md-end col-md-2">
                                <span
                                    class="fw-bold">{{ getCurrency() }}{{ $current_item_net_amount }}</span>
                            </div>
                            <br>
                            @if($item['is_combo_offer']=='Yes')
                            <div class="col-md-12 ms-5 p-2 d-flex"
                                style="height:40px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;background:rgb(235, 52, 159);color:white;font-weight:bold">
                                <i class="bi bi-gift-fill" style="color:white"></i>
                                &nbsp;&nbsp;&nbsp;&nbsp;<p>Product Added As combo Offer</p>
                            </div>
                            @elseif(!empty($item['product_discount_offer_text']))
                            <div class="col-md-12 ms-5 p-2 d-flex bg-success"
                                style="height:40px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;color:green;font-weight:bold">
                                <i class="bi bi-gift-fill" style="color:green"></i>
                                &nbsp;&nbsp;&nbsp;&nbsp;<p>{{$item['product_discount_offer_text']}}</p>
                            </div>
                            @endif
                        </div>

                    </li>
                @endforeach
            </ul>
            <!-- btn -->
            <div class="d-flex justify-content-between mt-4">
                <a href="/" class="btn btn-primary">Continue Shopping</a>
                <a href="/cart" class="btn btn-dark">Update Cart</a>

            </div>
        </div>
    </div>

    <!-- sidebar -->
    <div class="col-12 col-lg-4 col-md-5">
        <!-- card -->
        <div class="mb-5 card mt-6">
            <div class="card-body p-6">
                <!-- heading -->
                <h2 class="h5 mb-4">Summary</h2>
                <div class="card mb-2">
                    <!-- list group -->
                    <ul class="list-group list-group-flush">
                        <!-- list group item -->
                        <li
                            class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="me-auto">
                                <div>Item Subtotal</div>
                            </div>
                            <span>{{ getCurrency() }} {{ $total }}</span>
                        </li>

                        <!-- list group item -->
                        <li
                            class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="me-auto">
                                <div>Offer Discount</div>
                            </div>
                            <span>{{ getCurrency() }} {{ $discount }}</span>
                        </li>
                        <!-- list group item -->
                        <li
                            class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="me-auto">
                                <div class="fw-bold">Subtotal</div>
                            </div>
                            <span class="fw-bold">{{ getCurrency() }} {{ $subtotal-$cart_discount }}</span>
                        </li>
                    </ul>
                </div>

                <!-- text -->

                <!-- heading -->
                <div class="mt-8">
                    @if (!empty($applicable_offers))
                        <div class="d-flex justify-content-between">
                            <p class="text-danger" data-bs-toggle="modal"
                                data-bs-target="#available_offers_modal">Some Offers are available
                            </p>
                            <x-frontend.available_offers :offers="$applicable_offers" />
                            <a class="text-danger"> <i class="bi bi-chevron-down"
                                    href="javascript:void(0)" data-bs-toggle="modal"
                                    data-bs-target="#available_offers_modal"></i></a>
                        </div>
                    @endif
                    <h2 class="h5 mb-3">Apply Coupon Code</h2>
                    <form>
                        <div class="mb-2">
                            <input type="text" class="form-control" id="coupon_code"
                                placeholder="Coupon Code" />
                        </div>
                        <!-- btn -->
                        <div class="d-grid"><button type="button"
                                class="btn btn-outline-dark mb-1"
                                onClick="applyCouponCode('{!! $item['cart_session_id'] !!}')">Apply</button>
                        </div>
                        <p class="text-muted mb-0"><small>Terms & Conditions apply</small></p>
                    </form>
                </div>
                <p>
                    <small>
                        By placing your order, you agree to be bound by the Freshcart
                        <a href="#!">Terms of Service</a>
                        and
                        <a href="#!">Privacy Policy.</a>
                    </small>
                </p>

                <div class="d-grid mb-1 mt-4">
                    <!-- btn -->
                    <a href="/checkout"
                        class="btn btn-primary btn-lg d-flex justify-content-between align-items-center"
                        type="submit">
                        Go to Checkout
                        <span class="fw-bold">{{ getCurrency() }} {{ $subtotal-$cart_discount }}</span>
                </a>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<center>
    <h3>Cart is Empty</h3>
</center>
@endif