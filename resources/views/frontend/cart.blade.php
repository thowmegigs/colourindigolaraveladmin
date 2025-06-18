@extends('layouts.frontend.app')
@section('content')
    <style>
        .cart-summary label {
            font-weight: 500;
            font-size: 14px;
        }
    </style>
    <main class="main cart">
        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav container">
            <ul class="breadcrumb bb-no">
                <li><a href="demo1.html">Home</a></li>
                <li>Cart</li>
            </ul>

        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of PageContent -->
        <div class="page-content">
            <div class="container" x-data="{
                show_applicable_offers: false,
                show_applied_offers: false,
                init() {
            
                    if (this.$store.is_logged_in == 'No') {
                        $('.auth_modal').magnificPopup('open');
                    }
                    let me = this;
                    this.$watch('$store.cart.applicable_offers.length', function(v) {
                        me.show_applicable_offers = $store.cart.applicable_offers.length > 0 ? true : false;
                        me.show_applied_offers = $store.cart.applied_offers.length > 0 ? true : false;
                        setTimeout(function() {
            
                            $('#available_offer').magnificPopup({
            
                                midClick: true,
                                mainClass: 'mfp-fade'
                            });
                            $('#applied_offer').magnificPopup({
            
                                midClick: true,
                                mainClass: 'mfp-fade'
                            });
            
            
                        }, 1000)
            
                    })
                }
            }" style="min-height:200px;">
                <template x-if="show_applicable_offers">
                    <div >
                        @include('frontend.partials.available_offer_modal')
                        <div class="d-flex justify-content-between"
                            style="margin:10px 0px;padding: 5px 10px;
                                        background: #dcffdc;align-items:center;max-width:300px;">
                            <div class="d-flex"><img src="/front_assets/images/discount.png"
                                    style="width:25px;height:25px" />
                                <p
                                    style="color: green;
                                       margin: 0;
                                       padding: 0;
                                       margin-top: 5px;
                                       margin-left: 10px;
                                       font-weight: bold;
                                       font-size: 11px;">
                                    Check Available offers </p>
                            </div>
                            <a href="#available_offer_modal" id="available_offer">
                                <i class="w-icon-angle-down " style="font-weight:bold;color:green;font-size:12px;"></i>
                            </a>
                        </div>
                    </div>
                </template>

                <template x-if="$store.cart.items.length>0">
                    <div class="row gutter-lg mb-10">
                        <div class="col-lg-8 pr-lg-4 mb-6">
                            <div>

                                <template x-if="$store.cart.minimum_cart_amount_offer!=null">
                                    <div class="alert alert-danger p-2" role="alert">
                                        Shop more worth
                                        <span x-text="$store.cart.currency"></span>
                                        <span x-text="$store.cart.minimum_cart_amount_offer.target_amount"></span>
                                        to get
                                        <span x-text="$store.cart.minimum_cart_amount_offer.discount"></span> % discount

                                    </div>
                                </template>
                              
                            </div>
                            <table class="shop-table cart-table">
                                <thead>
                                    <tr>
                                        <th class="product-name"><span>Product</span></th>
                                        <th></th>
                                        <th class="product-price" style="text-align: left"><span>Price</span></th>
                                        <th class="product-quantity" style="text-align: left"><span>Quantity</span></th>
                                        <th class="product-subtotal" style="text-align: left"><span>Subtotal</span></th>
                                    </tr>
                                </thead>
                                <tbody x-data>
                                    <template x-if="$store.cart.items.length>0">
                                        <template x-for="item in $store.cart.items" :key="item.id">
                                            <tr x-data="{
                                                clicked: false,
                                                is_adding: false,
                                                is_added: false,
                                            
                                                local_item: {
                                                    product_id: item.product_id,
                                                    qty: item.qty,
                                                    name: item.name,
                                                    sale_price: item.sale_price,
                                                    price: item.price,
                                                    sgst: item.sgst,
                                                    cgst: item.cgst,
                                                    igst: item.igst,
                                                    unit: item.unit,
                                            
                                                },
                                            
                                            
                                            
                                                inc() {
                                            
                                                    this.local_item.qty = parseInt(this.local_item.qty) + 1;
                                                    this.addToCart();
                                                    if (this.$store.cart.is_logged_in == 'No') {
                                                        $('.auth_modal').magnificPopup('open');
                                                    }
                                                },
                                                dec() {
                                                    if (this.local_item.qty > 1) {
                                                        this.local_item.qty = parseInt(this.local_item.qty) - 1;
                                                        this.addToCart();
                                                    } else {
                                                        $store.cart.deleteItem(this.local_item.product_id, false)
                                            
                                                    }
                                                },
                                                addToCart() {
                                            
                                                    this.is_adding = true;
                                                    let cart_session_id = localStorage.getItem('cart_session_id');
                                                    this.local_item['cart_session_id'] = cart_session_id;
                                            
                                                    if (this.local_item.qty < 1)
                                                        this.local_item.qty = 1
                                                    fetch('/addToCart', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        },
                                                        body: JSON.stringify(this.local_item)
                                                    }).then(response => response.json()).then(res => {
                                                        this.is_adding = false;
                                                        if (res['success']) {
                                                            this.cart_session_id = res['cart_session_id'];
                                                            this.$store.cart.updateStore(res)
                                                            vNotify.success({ text: ' Cart Updated successfully', title: 'Suceess' });
                                            
                                                        } else {
                                                            vNotify.error({ text: res['message'], title: 'Error' });
                                                        }
                                            
                                                    })
                                                },
                                            
                                            
                                            
                                            
                                            }">
                                                <td class="product-thumbnail">
                                                    <div class="p-relative">
                                                        <a :href="$store.cart.product_url(item.name)">
                                                            <figure>
                                                                <img :src="item.image" alt="product"
                                                                    style="margin:auto;height:100px!important;width:200px!important;object-fit:contain">
                                                            </figure>
                                                        </a>
                                                        <button type="button"
                                                            @click.prevent="$store.cart.deleteItem(item.product_id)"
                                                            class="btn btn-close"><i class="fas fa-times"></i></button>
                                                    </div>
                                                    <br>

                                                </td>
                                                <td class="product-name">
                                                    <a :href="$store.cart.product_url(item.name)"
                                                        x-text="$store.cart.truncate(item.name)">

                                                    </a>
                                                    <br>
                                                    <template x-if="item.is_combo_offer=='Yes'">
                                                        <div class="col-md-12 ms-5 p-2 d-flex"
                                                            style="height:40px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;background:rgb(235, 52, 159);color:white;font-weight:bold">
                                                            <i class="bi bi-gift-fill" style="color:white"></i>
                                                            <p>Product Added As combo Offer</p>
                                                        </div>
                                                    </template>
                                                    <template x-if="item.product_discount_offer_text!=null">
                                                        <div class="col-md-12 ms-5 p-2 d-flex bg-success"
                                                            style="height:40px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;color:green;font-weight:bold">
                                                            <i class="bi bi-gift-fill" style="color:rgb(6, 133, 6)"></i>
                                                            <p x-text="item.product_discount_offer_text"></p>
                                                        </div>
                                                    </template>
                                                </td>
                                                <td class="product-price"><span class="amount"
                                                        x-text="$store.cart.amount_in_cur(item.sale_price)"></span></td>
                                                <td class="product-quantity">
                                                    <div class="input-group">
                                                        <input class="quantity form-control" type="number" min="1"
                                                            max="10" x-model="local_item.qty" />
                                                        <button class=" w-icon-plus" @click="inc()"></button>
                                                        <button class="w-icon-minus" @click="dec()"></button>
                                                    </div>
                                                </td>
                                                <td class="product-subtotal">
                                                    <span class="amount"
                                                        x-text="$store.cart.amount_in_cur(item.net_amount)"></span>
                                                </td>
                                            </tr>

                                        </template>
                                        <template x-if="$store.cart.items.length===0">
                                            <tr>
                                                <td colspan="6" style="text-align:center;padding:20px;">Cart is empty
                                                </td>
                                            </tr>
                                        </template>
                                    </template>

                                </tbody>
                            </table>

                            <div class="cart-action mb-6" x-data>
                                <a href="/" class="btn btn-dark btn-rounded btn-icon-left btn-shopping mr-auto"><i
                                        class="w-icon-long-arrow-left"></i>Continue Shopping</a>
                                <button type="button" class="btn btn-rounded btn-default btn-clear"
                                    @click="$store.cart.clearCart()" name="clear_cart" value="Clear Cart">Clear
                                    Cart</button>

                            </div>

                            <form class="coupon" x-data>
                                <h5 class="title coupon-title font-weight-bold text-uppercase">Coupon Discount</h5>
                                <input type="text" class="form-control mb-4" placeholder="Enter coupon code here..."
                                    required x-model="$store.cart.coupon_code" />
                                <button type="button" @click.prevent="$store.cart.applyCouponCode()"
                                    class="btn btn-dark btn-outline btn-rounded">
                                    <span
                                        x-text="$store.cart.is_applying_coupon ? 'Applying ...' : 'Apply Coupon'"></span></button>
                            </form>
                        </div>
                        <div class="col-lg-4 sticky-sidebar-wrapper" x-data>
                            <div class="sticky-sidebar">
                                <div class="cart-summary mb-4">
                                    <h3 class="cart-title text-uppercase">Cart Totals</h3>
                                    <div class="cart-subtotal d-flex align-items-center justify-content-between">
                                        <label class="ls-25">Total Amount</label>
                                        <span x-text="$store.cart.amount_in_cur($store.cart.total)"></span>
                                    </div>

                                    <hr class="divider">
                                    <div class="cart-subtotal d-flex align-items-center justify-content-between">
                                        <label class="ls-25">MRP Discount</label>
                                        <span x-text="$store.cart.amount_in_cur($store.cart.total_discount)"></span>
                                    </div>
                                    <hr class="divider">
                                    <div x-show="$store.cart.cart_discount>0"
                                        class="cart-subtotal d-flex align-items-center justify-content-between">

                                        <div class="d-flex flex-column">
                                            <label class="ls-25">Cart Discount</label>
                                            <template x-if="$store.cart.cart_discount>0">
                                                <div
                                                    style="color: #299629;
                                                    background: rgb(230 250 230);
                                            padding: 5px;
                                            /* display: inline-block; */
                                            font-size: 11px;;margin-top:4px">
                                                    Cart Discount Applied
                                                </div>
                                            </template>
                                        </div>

                                        <span x-text="$store.cart.amount_in_cur($store.cart.cart_discount)"></span>
                                    </div>
                                    <hr class="divider">
                                    <div x-show="$store.cart.cart_discount>0"
                                        class="cart-subtotal d-flex  align-items-center justify-content-between">
                                        <div class="d-flex flex-column">
                                            <label class="ls-25">Shipping Charge</label>
                                            <template x-if="$store.cart.shipping_discount>0">
                                                <div
                                                    style="color: #299629;
                                                background: rgb(230 250 230);
                                            padding: 5px;
                                          
                                            font-size: 11px;margin-top:4px">
                                                    Shipping Discount of
                                                    <span
                                                        x-text="$store.cart.amount_in_cur($store.cart.shipping_discount)"></span>
                                                    Applied
                                                </div>
                                            </template>
                                        </div>
                                        <span
                                            x-text="$store.cart.amount_in_cur($store.cart.shipping_charge-$store.cart.shipping_discount)"></span>
                                    </div>


                                    <hr class="divider">






                                    <template x-if="show_applied_offers">
                                        <div class="mb-3">
                                            @include('frontend.partials.applied_offer_modal')
                                            <div class="d-flex justify-content-between">
                                                <label style="font-weight:bold;color:#e00f33">Applied Offers</label>
                                                <span> <a href="#applied_offer_modal" id="applied_offer">
                                                        <i class="w-icon-angle-down "
                                                            style="font-weight:bold;color:green;font-size:12px;"></i>
                                                    </a></span>

                                            </div>
                                        </div>
                                    </template>
                                    <div class="order-total d-flex justify-content-between align-items-center">
                                        <label>Total Payable</label>
                                        <span class="ls-50"
                                            x-text="$store.cart.cartNetAmountAfterOfferDiscountAndShipping"></span>
                                    </div>
                                    <a href="/checkout"
                                        class="btn btn-block btn-dark btn-icon-right btn-rounded  btn-checkout">
                                        Proceed to checkout<i class="w-icon-long-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="$store.cart.items.length===0">
                    <div class="row">
                        <div class="col-lg-12 text-center ">
                            <h4>Cart is empty</h4>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <!-- End of PageContent -->
    </main>
@endsection
