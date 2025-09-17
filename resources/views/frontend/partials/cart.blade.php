<!---**********************========================This is content for Mini cart================ **************************==============****/--->
<div class="dropdown-box">
    <div class="cart-header">
        <span>Shopping Cart</span>
        <a href="#" class="btn-close">Close<i class="w-icon-long-arrow-right"></i></a>
    </div>
    @if (!empty(session('shoppingCart')))
        @php
            $sub_total = 0;

        @endphp
        <div class="products">
            @foreach (session('shoppingCart') as $cart_id => $item)
                @php

                    $current_item_net_amount = $item['net_amount'];
                    $sub_total += $current_item_net_amount;

                @endphp
                <div class="product product-cart">
                    <div class="product-detail">
                        <a href="{{ $item['slug'] }}" class="product-name"> {{ $item['name'] }}</a>
                        <span><small
                                class="text-muted">{{ str_replace('_', ' ', $item['variant_name']) }}</small></span>
                        <div class="price-box">
                            <span class="product-quantity">{{ $item['qty'] }}</span>
                            <span
                                class="product-price">{{ getCurrency() }}{{ ceil($item['net_amount'] / $item['qty']) }}</span>
                        </div>
                    </div>
                    <figure class="product-media">
                        <a href="product-default.html">
                            <img src="{{ $item['image'] }}" alt="product" height="84" width="94" />
                        </a>
                    </figure>
                    <button class="btn btn-link btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="cart-total">
            <label>Subtotal:</label>
            <span class="price">{{ getCurrency() }}{{ $sub_total }}</span>
        </div>

        <div class="cart-action">
            <a href="/cart" class="btn btn-dark btn-outline btn-rounded">View Cart</a>
            <a href="/checkoutcheckotml" class="btn btn-primary  btn-rounded">Checkout</a>
        </div>
        @else
        <center><h4>Cart is empty</h4></center>
    @endif
</div>
