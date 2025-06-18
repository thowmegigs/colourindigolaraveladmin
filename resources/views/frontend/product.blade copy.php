@extends('layouts.frontend.app')
@section('content')
    <style>
        .active_addon {

            border: 1px solid #ef2121;

            background: #ffe8e5;
            box-shadow: 1px 1px 4px 0px #e8e6e6;
        }



        .addon_product {
            cursor: pointer;
            padding: 7px;
            min-width: 160px;

            margin-right: 5px;
            box-shadow: 1px 1px 4px 0px #e8e6e6;

        }
    </style>
    @php

        $category_based_features =
            $product->category_based_features != null ? json_decode($product->category_based_features, true) : null;
        $attributes = $product->attributes != null ? $product->attributes : [];
    @endphp
    <main class="main mb-10 pb-1">
        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav container">
            <ul class="breadcrumb bb-no">
                <li><a href="/">Home</a></li>
                <li>{{ $product->name }}</li>
            </ul>

        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of Page Content -->
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="main-content" style="max-width:100%!important">
                            <div class="product product-single row mb-2">
                                <div class="col-md-6 mb-4 mb-md-8">
                                    <div class="product-gallery product-gallery-sticky">
                                        <div class="swiper-container product-single-swiper swiper-theme nav-inner"
                                            data-swiper-options="{
                                           'navigation': {
                                               'nextEl': '.swiper-button-next',
                                               'prevEl': '.swiper-button-prev'
                                           }
                                       }">
                                            <div class="swiper-wrapper row cols-1 gutter-no">
                                                @if (count($product->images) > 0)
                                                    @foreach ($product->images as $img)
                                                        <div class="swiper-slide swiper-slide-active" role="group"
                                                            aria-label="1 / 4" style="width: 395px;">
                                                            <figure class="product-image"
                                                                style="position: relative; overflow: hidden; cursor: pointer;max-height:200px;max-width:300px;">
                                                                <img src="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['large']) }}"
                                                                    data-zoom-image="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['small']) }}"
                                                                    alt="Water Boil Black Utensil"
                                                                    style="object-fit:contain;margin:auto;max-height:300px;max-width:300px;">

                                                            </figure>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="swiper-slide swiper-slide-active" role="group"
                                                        aria-label="1 / 4" style="width: 395px;">
                                                        <figure class="product-image"
                                                            style="position: relative; overflow: hidden; cursor: pointer;">
                                                            <img src="{{ asset('storage/products/' . $product->id . '/' . $product->image) }}"
                                                                data-zoom-image="{{ asset('storage/products/' . $product->id . '/' . $product->image) }}"
                                                                alt="Water Boil Black Utensil"
                                                                style="object-fit:contain;margin:auto;max-height:300px;max-width:300px;">
                                                        </figure>
                                                    </div>
                                                @endif

                                            </div>
                                            <button class="swiper-button-next"></button>
                                            <button class="swiper-button-prev"></button>
                                            <a href="#" class="product-gallery-btn product-image-full"><i
                                                    class="w-icon-zoom"></i></a>
                                        </div>
                                        <div class="product-thumbs-wrap swiper-container"
                                            data-swiper-options="{
                                           'navigation': {
                                               'nextEl': '.swiper-button-next',
                                               'prevEl': '.swiper-button-prev'
                                           }
                                       }">
                                            <div class="product-thumbs swiper-wrapper row cols-4 gutter-sm">

                                                @if (count($product->images) > 0)
                                                    @foreach ($product->images as $img)
                                                        <div class="product-thumb swiper-slide" role="group"
                                                            aria-label="1 / 4" style="width: 91.25px; margin-right: 10px;">
                                                            <img src="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['medium']) }}"
                                                                alt="Product Thumb"
                                                                style="max-width:103px;max-height:103px;object-fit:contain">
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="product-thumb swiper-slide" role="group"
                                                        aria-label="1 / 4" style="width: 91.25px; margin-right: 10px;">
                                                        <img src="{{ asset('storage/products/' . $product->id . '/' . $product->image) }}"
                                                            alt="Product Thumb"
                                                            style="max-width:103px;max-height:103px;object-fit:contain">
                                                    </div>
                                                @endif
                                            </div>
                                            <button class="swiper-button-next"></button>
                                            <button class="swiper-button-prev"></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-6 mb-md-8" x-data="{
                                    clicked: false,
                                    current_single_product_sale_price: {{ count($product->variants) > 0 ? $product->variants[0]->sale_price : $product->sale_price }},
                                    current_single_product_price: {{ count($product->variants) > 0 ? $product->variants[0]->price : $product->price }},
                                
                                    total_payable: {{ count($product->variants) > 0 ? $product->variants[0]->sale_price : $product->sale_price }},
                                
                                    is_adding: false,
                                    is_added: false,
                                
                                    item: {
                                        product_id: {{ $product->id }},
                                        qty: 1,
                                        variant_id: '',
                                        name: '{{ $product->name }}',
                                        sale_price: {{ $product->sale_price }},
                                        price: {{ $product->price }},
                                        sgst: {{ $product->sgst }},
                                        cgst: {{ $product->cgst }},
                                        igst: {{ $product->igst }},
                                        unit: '{{ empty($product->unit) ? 'pcs' : $product->unit }}',
                                        addon_items: [],
                                        addon_products: [],
                                        variant_attributes_val: []
                                
                                    },
                                
                                    init() {
                                
                                
                                        this.$watch('$store.cart.items', () => {
                                            this.item.qty = $store.cart.itemQty({{ $product->id }});
                                            if (this.item.qty == 0)
                                                this.item.qty = 1;
                                        })
                                    },
                                
                                    inc() {
                                
                                        this.item.qty = parseInt(this.item.qty) + 1;
                                
                                    },
                                    dec() {
                                        if (this.item.qty > 1)
                                            this.item.qty = parseInt(this.item.qty) - 1;
                                        else {
                                            $store.cart.deleteItem(this.item.product_id, false)
                                
                                        }
                                    },
                                    addToCart() {
                                        this.is_adding = true;
                                        let cart_session_id = localStorage.getItem('cart_session_id');
                                        this.item['cart_session_id'] = cart_session_id;
                                        if (!this.clicked)
                                            this.clicked = true;
                                        if (this.item.qty < 1)
                                            this.item.qty = 1
                                        fetch('/addToCart', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            },
                                            body: JSON.stringify(this.item)
                                        }).then(response => response.json()).then(res => {
                                            this.is_adding = false;
                                            if (res['success']) {
                                                this.cart_session_id = res['cart_session_id'];
                                                this.$store.cart.updateStore(res)
                                                vNotify.success({ text: 'Successfully Added to cart', title: 'Suceess' });
                                                this.is_added = true;
                                            } else {
                                                vNotify.error({ text: res['message'], title: 'Error' });
                                            }
                                
                                        })
                                    },
                                
                                    showAddonVariantModal(id) {
                                        let target = this.$event.target;
                                        let el = jQuery(target).closest('.addon_product')
                                        el.toggleClass('active_addon');
                                        if (el.hasClass('active_addon')) {
                                            $.magnificPopup.open({
                                                items: {
                                                    src: '#addonModal' + id
                                                },
                                                type: 'inline',
                                
                                            });
                                        } else {
                                            document.querySelectorAll('#addonModal' + id + ' input[type=checkbox]').forEach(el => el.checked = false);
                                            document.querySelectorAll('#addonModal' + id + ' input[type=number]').forEach(el => el.value = 1);
                                            let f = this.item.addon_products.filter(function(arr_item) {
                                                if (arr_item['addon_id'] == id) {
                                                    return false;
                                                } else return true;
                                            });
                                            this.item.addon_products = f;
                                            this.calculateTotalPayable();
                                        }
                                
                                    },
                                
                                
                                    addProductAddon(addon_id, addon_name, amount, is_qty_changed = false) {
                                
                                        let target = this.$event.target;
                                
                                        let el = jQuery(target).closest('.addon_product')
                                        if (!is_qty_changed)
                                            el.toggleClass('active_addon');
                                        if (el.hasClass('active_addon')) {
                                            $.magnificPopup.open({
                                                items: {
                                                    src: '#addonQtyModal' + addon_id
                                                },
                                                type: 'inline'
                                            });
                                        }
                                
                                
                                        let item_to_add = {
                                            'addon_name': addon_name,
                                            'addon_id': addon_id,
                                            'addon_variant_name': '',
                                            'addon_variant_id': '',
                                            'amount': amount,
                                            'qty': is_qty_changed ? $(target).val() : 1
                                
                                        };
                                
                                        let f = this.item.addon_products.filter(function(arr_item) {
                                            if (arr_item['addon_id'] == addon_id) {
                                                return false;
                                            } else return true;
                                        });
                                        console.log('dekho')
                                        console.log(el.hasClass('active_addon'))
                                        if (is_qty_changed || el.hasClass('active_addon'))
                                            f.push(item_to_add);
                                
                                        this.item.addon_products = f;
                                
                                        this.calculateTotalPayable();
                                    },
                                    addProductAddonWithVariant(addon_id, addon_name, amount, addon_variant_id, addon_variant_name, is_qty_changed = false) {
                                        let target = this.$event.target;
                                
                                
                                        let item_to_add = {
                                            'addon_name': addon_name,
                                            'addon_id': addon_id,
                                            'addon_variant_name': addon_variant_name,
                                            'addon_variant_id': addon_variant_id,
                                            'amount': amount,
                                            'qty': is_qty_changed ? $(target).val() : 1
                                
                                        };
                                        console.log(item_to_add)
                                
                                
                                        let f = this.item.addon_products.filter(function(arr_item) {
                                            if (arr_item['addon_id'] == addon_id && arr_item['addon_variant_id'] == addon_variant_id) {
                                                return false;
                                            } else return true;
                                        });
                                
                                        if (jQuery(target).is(':checked') || is_qty_changed) {
                                            f.push(item_to_add);
                                        }
                                        this.item.addon_products = f;
                                
                                        this.calculateTotalPayable();
                                    },
                                    calculateTotalPayable() {
                                        let product_qty = this.item.qty;
                                
                                        let amt = parseFloat(this.current_single_product_sale_price);
                                        this.total_payable = amt;
                                        if (this.item.addon_products.length > 0) {
                                            this.item.addon_products.forEach(function(v) {
                                                amt += parseFloat(v['qty'] * v['amount']);
                                            });
                                        }
                                        if (this.item.addon_items.length > 0) {
                                            this.item.addon_items.forEach(function(v) {
                                
                                                amt += parseFloat(v['qty'] * v['price']);
                                            });
                                        }
                                        this.total_payable = product_qty * amt;
                                
                                
                                    },
                                    addAddonItem(name, amount, is_qty_changed = false) {
                                        let target = this.$event.target;
                                
                                
                                        let item_to_add = {
                                            'name': name,
                                            'price': amount,
                                            'qty': is_qty_changed ? $(target).val() : 1,
                                
                                
                                        };
                                        // let f = [];
                                
                                        let f = this.item.addon_items.filter(function(arr_item) {
                                            if (arr_item['name'] == name) {
                                                return false;
                                            } else return true;
                                        });
                                        console.log(f);
                                
                                        if ($(target).is(':checked') || is_qty_changed) {
                                            f.push(item_to_add);
                                        }
                                        this.item.addon_items = f;
                                
                                        console.log(this.item.addon_items);
                                
                                        this.calculateTotalPayable()
                                
                                
                                
                                    },
                                    addVariantOption(option_val) {
                                        let target = this.$event.target;
                                
                                        let f = this.item.variant_attributes_val.filter(function(item) {
                                            if (item == option_val) {
                                                return false;
                                            } else return true;
                                        });
                                
                                        if (!$(target).hasClass('active')) {
                                            f.push(option_val);
                                        }
                                        console.log(f)
                                        this.item.variant_attributes_val = f;
                                        fetch('/get_variant_price', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            },
                                            body: JSON.stringify({ product_id: {!! $product->id !!}, attributes: this.item.variant_attributes_val })
                                        }).then(response => response.json()).then(res => {
                                            this.is_adding = false;
                                            if (res['success']) {
                                                this.current_single_product_sale_price = res['sale_price']
                                                this.current_single_product_price = res['price']
                                                this.calculateTotalPayable();
                                
                                            }
                                
                                        })
                                
                                
                                
                                
                                    }
                                
                                
                                
                                
                                }">
                                    <div class="product-details" data-sticky-options="{'minWidth': 767}">
                                        <h1 class="product-title">{{ ucwords($product->name) }}</h1>
                                        <div class="product-bm-wrapper">

                                            <div class="product-meta mb-0">
                                                <div class="product-categories">
                                                    Category:
                                                    <span class="product-category"><a
                                                            href=/products/{{ \Str::slug($product->category_name) }}">{{ ucwords($product->category_name) }}</a></span>
                                                </div>
                                                @if ($product->quantity < 1)
                                                    <p style="color:red;margin-bottom:0px!important">Out of stock</p>
                                                @endif
                                                <!-- <div class="product-sku">
                                                                                               SKU: <span>MS46891390</span>
                                                                                           </div> -->
                                            </div>
                                        </div>

                                        <hr class="product-divider" style='margin:5px!important'>
                                        <div class="product-pa-wrapper">
                                            <div class="product-price">
                                                {{ getCurrency() }}<ins class="new-price"
                                                    x-text="current_single_product_sale_price.toFixed(2)">
                                                    {{ getCurrency() }}{{ $product->sale_price }}</ins>
                                                {{ getCurrency() }} <del class="old-price"
                                                    x-text="current_single_product_price.toFixed(2)">{{ getCurrency() }}{{ $product->price }}</del>
                                            </div>

                                        </div>


                                        <!-- <div class="ratings-container">
                                                                                       <div class="ratings-full">
                                                                                           <span class="ratings" style="width: 80%;"></span>
                                                                                           <span class="tooltiptext tooltip-top"></span>
                                                                                       </div>
                                                                                       <a href="#product-tab-reviews" class="rating-reviews">(3 Reviews)</a>
                                                                                   </div> -->

                                        <div class="product-short-desc">
                                            <!-- <ul class="list-type-check list-style-none">
                                                                                           <li>Ultrices eros in cursus turpis massa cursus mattis.</li>
                                                                                           <li>Volutpat ac tincidunt vitae semper quis lectus.</li>
                                                                                           <li>Aliquam id diam maecenas ultricies mi eget mauris.</li>
                                                                                       </ul> -->
                                            {{ $product->short_description }}

                                        </div>

                                        @php
                                            $attrs = $product->attributes;
                                        @endphp
                                        @if (!is_null($attrs))
                                            <hr class="product-divider">
                                            @foreach ($attrs as $y)
                                                @php
                                                    $vals = explode(',', $y['value']);

                                                @endphp
                                                <div class="product-form product-variation-form product-size-swatch">
                                                    <label class="mb-1">{{ $y['name'] }}:</label>
                                                    <div
                                                        class="flex-wrap d-flex align-items-center product-variations ml-3">
                                                        @foreach ($vals as $v)
                                                            <a href="#"
                                                                @click="addVariantOption('{!! $v !!}')"
                                                                class="size {{ $loop->first ? 'active' : '' }}">{{ $v }}</a>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            @endforeach
                                        @endif
                                        @if (!empty($category_based_features))
                                            <hr class="product-divider">
                                            <div style="width:200px">
                                                @foreach ($category_based_features as $g)
                                                    <div class="d-flex justify-content-between"
                                                        style="font-size: 1.4rem;
                                                        color: #666;">
                                                        <!-- table -->


                                                        <b>{{ ucwords($g['name']) }}</b>
                                                        <p>{{ ucwords($g['value']) }}</p>

                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($product->addon_items != null && !empty($product->addon_items[0]['name']))
                                            <hr class="product-divider">
                                            <div>
                                                <p> <b>You can also add</b></p>
                                                <div class="m-2">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            @foreach (json_decode($product->addon_items, true) as $item)
                                                                <tr>
                                                                    <td style="vertical-align:middle">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            style="width:27px;height:27px"
                                                                            @change="addAddonItem('{!! $item['name'] !!}', '{!! $item['price'] !!}')"
                                                                            id="{{ $item['name'] }}" name="addon_items"
                                                                            value="{{ $item['price'] }}" />
                                                                    </td>
                                                                    <td style="vertical-align:middle">
                                                                        <p class="p-0 m-0" style="margin:0">
                                                                            {{ $item['name'] }}</p>
                                                                    </td>
                                                                    <td style="vertical-align:middle" class="text-danger"
                                                                        style="font-size:12px;font-weight:bold">
                                                                        <p style="margin:0">
                                                                            {{ getCurrency() }}{{ $item['price'] }}</p>
                                                                    </td>
                                                                    @if ($product->show_qty_option_for_addon != 'Yes')
                                                                        <td style="vertical-align:middle">

                                                                            <input
                                                                                class="form-control bg-white text-center"
                                                                                style="border-radius:0px;width:85px;min-height:20px!important;height:30px!important"
                                                                                type="number" value="1"
                                                                                @input="addAddonItem('{!! $item['name'] !!}', '{!! $item['price'] !!}',true)">

                                                                        </td>
                                                                    @endif

                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        @endif

                                        @if ($product->addon_products->count() > 0)
                                            <hr class="product-divider">
                                            <div>
                                                <p><b>Product Addons</b></p>
                                                <div class="m-2 d-flex">
                                                    @foreach ($product->addon_products as $item)
                                                        @if ($item->variants->count() > 0)
                                                            <x-frontend.addon_modal :addon="$item" :showQtyOption="$product->show_qty_option_for_addon" />
                                                        @else
                                                            <x-frontend.addon_qty_choose_modal :addon="$item" />
                                                        @endif
                                                        <div class="addon_product p-2 m-1 text-center pl card"
                                                            @if ($item->variants->count() > 0) @click="showAddonVariantModal('{!! $item->id !!}')"
    
                                                         @else
                                                            @click="addProductAddon('{!! $item->id !!}','{!! $item->name !!}', '{!! $item->sale_price !!}')" @endif>
                                                            <img src="{{ asset('storage/products/' . $item->id . '/thumbnail/' . $item->image) }}"
                                                                style="object-fit:contain;height:60px" />
                                                            <h6 style="margin-top:10px;font-size:13px;">
                                                                {{ $item->name }}
                                                            </h6>
                                                            <div class="d-flex text-center justify-content-center">
                                                                <p class="mb-0">
                                                                    {{ getCurrency() }}{{ $item->sale_price }}
                                                                </p>
                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                        @endif

                                        @if (count($product->offers) > 0)
                                            <x-frontend.modals.offer_modal :offers="$product->offers" />
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
                                                        Offers Available ({{ count($product->offers) }})</p>
                                                </div>
                                                <a href="#offer_modal" id="offer_dialog">
                                                    <i class="w-icon-angle-down "
                                                        style="font-weight:bold;color:green;font-size:12px;"></i>
                                                </a>
                                            </div>
                                        @endif
                                        <div class="product-sticky-content sticky-content mt-3">
                                            <div class="product-form container">
                                                <div class="product-qty-form">
                                                    <div class="input-group">
                                                        <input class="quantity form-control" type="number"
                                                            min="1" x-model="item.qty" max="10">

                                                        <button class=" w-icon-plus" @click="inc()"></button>
                                                        <button class=" w-icon-minus" @click="dec()"></button>
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary"
                                                    :class="is_adding ? 'load-more-overlay loading' : ''"
                                                    @click="addToCart()"
                                                    @if ($product->quantity < 1) disabled @endif>
                                                    <i class="w-icon-cart"></i>
                                                    <span>Add to Cart</span>
                                                    (<span x-text="$store.cart.amount_in_cur(total_payable)"></span>)
                                                </button>
                                            </div>
                                        </div>

                                        <!-- <div class="social-links-wrapper">
                                                                                       <div class="social-links">
                                                                                           <div class="social-icons social-no-color border-thin">
                                                                                               <a href="#" class="social-icon social-facebook w-icon-facebook"></a>
                                                                                               <a href="#" class="social-icon social-twitter w-icon-twitter"></a>
                                                                                               <a href="#" class="social-icon social-pinterest fab fa-pinterest-p"></a>
                                                                                               <a href="#" class="social-icon social-whatsapp fab fa-whatsapp"></a>
                                                                                               <a href="#" class="social-icon social-youtube fab fa-linkedin-in"></a>
                                                                                           </div>
                                                                                       </div>
                                                                                       <span class="divider d-xs-show"></span>
                                                                                       <div class="product-link-wrapper d-flex">
                                                                                           <a href="#" class="btn-product-icon btn-wishlist w-icon-heart"><span></span></a>
                                                                                           <a href="#"
                                                                                               class="btn-product-icon btn-compare btn-icon-left w-icon-compare"><span></span></a>
                                                                                       </div>
                                                                                   </div> -->
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="container-fluid">
                                <div class="row cols-md-3">
                                    <div class="mb-3" style="border-left:1px solid #ccc;border-right:1px solid #ccc">
                                        <h5 class="sub-title font-weight-bold"><span class="mr-3">1.</span>Free
                                            Shipping &amp; Return</h5>
                                        <p class="detail pl-5">We offer free shipping for products on orders
                                            above 50$ and offer free delivery for all orders in US.</p>
                                    </div>
                                    <div class="mb-3" style="border-right:1px solid #ccc">
                                        <h5 class="sub-title font-weight-bold"><span>2.</span>&nbsp;&nbsp;&nbsp;&nbsp;Free and
                                            Easy
                                            Returns</h5>
                                        <p class="detail pl-5">We guarantee our products and you could get back
                                            all of your money anytime you want in 30 days.</p>
                                    </div>
                                    <div class="mb-3" style="border-right:1px solid #ccc">
                                        <h5 class="sub-title font-weight-bold"><span>3.</span>&nbsp;&nbsp;&nbsp;&nbsp;Special
                                            Financing
                                        </h5>
                                        <p class="detail pl-5">Get 20%-50% off items over 50$ for a month or
                                            over 250$ for a year with our special credit card.</p>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="accordion accordion-simple mb-9">
                                <div class="card">
                                    <div class="card-header">
                                        <a href="#product-tab-description font-weight-bold"style="color:black;font-weight:bold;font-size:20px"
                                            class="collapse">Description</a>
                                    </div>
                                    <div class="card-body " id="product-tab-description">
                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! $product->description ?? 'Product description here..' !!}
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <!-- <div class="card">
                                                                               <div class="card-header ls-normal">
                                                                                   <a href="#product-tab-reviews" class="expand">Customer Reviews(3)</a>
                                                                               </div>
                                                                               <div class="card-body collapsed" id="product-tab-reviews">
                                                                                   <div class="row mb-4">
                                                                                       <div class="col-xl-4 col-lg-5 mb-4">
                                                                                           <div class="ratings-wrapper">
                                                                                               <div class="avg-rating-container">
                                                                                                   <h4 class="avg-mark font-weight-bolder ls-50">3.3</h4>
                                                                                                   <div class="avg-rating">
                                                                                                       <p class="text-dark mb-1">Average Rating</p>
                                                                                                       <div class="ratings-container">
                                                                                                           <div class="ratings-full">
                                                                                                               <span class="ratings" style="width: 60%;"></span>
                                                                                                               <span class="tooltiptext tooltip-top"></span>
                                                                                                           </div>
                                                                                                           <a href="#" class="rating-reviews">(3 Reviews)</a>
                                                                                                       </div>
                                                                                                   </div>
                                                                                               </div>
                                                                                               <div class="ratings-value d-flex align-items-center text-dark ls-25">
                                                                                                   <span class="text-dark font-weight-bold">66.7%</span>Recommended<span
                                                                                                       class="count">(2 of 3)</span>
                                                                                               </div>
                                                                                               <div class="ratings-list">
                                                                                                   <div class="ratings-container">
                                                                                                       <div class="ratings-full">
                                                                                                           <span class="ratings" style="width: 100%;"></span>
                                                                                                           <span class="tooltiptext tooltip-top"></span>
                                                                                                       </div>
                                                                                                       <div class="progress-bar progress-bar-sm ">
                                                                                                           <span></span>
                                                                                                       </div>
                                                                                                       <div class="progress-value">
                                                                                                           <mark>70%</mark>
                                                                                                       </div>
                                                                                                   </div>
                                                                                                   <div class="ratings-container">
                                                                                                       <div class="ratings-full">
                                                                                                           <span class="ratings" style="width: 80%;"></span>
                                                                                                           <span class="tooltiptext tooltip-top"></span>
                                                                                                       </div>
                                                                                                       <div class="progress-bar progress-bar-sm ">
                                                                                                           <span></span>
                                                                                                       </div>
                                                                                                       <div class="progress-value">
                                                                                                           <mark>30%</mark>
                                                                                                       </div>
                                                                                                   </div>
                                                                                                   <div class="ratings-container">
                                                                                                       <div class="ratings-full">
                                                                                                           <span class="ratings" style="width: 60%;"></span>
                                                                                                           <span class="tooltiptext tooltip-top"></span>
                                                                                                       </div>
                                                                                                       <div class="progress-bar progress-bar-sm ">
                                                                                                           <span></span>
                                                                                                       </div>
                                                                                                       <div class="progress-value">
                                                                                                           <mark>40%</mark>
                                                                                                       </div>
                                                                                                   </div>
                                                                                                   <div class="ratings-container">
                                                                                                       <div class="ratings-full">
                                                                                                           <span class="ratings" style="width: 40%;"></span>
                                                                                                           <span class="tooltiptext tooltip-top"></span>
                                                                                                       </div>
                                                                                                       <div class="progress-bar progress-bar-sm ">
                                                                                                           <span></span>
                                                                                                       </div>
                                                                                                       <div class="progress-value">
                                                                                                           <mark>0%</mark>
                                                                                                       </div>
                                                                                                   </div>
                                                                                                   <div class="ratings-container">
                                                                                                       <div class="ratings-full">
                                                                                                           <span class="ratings" style="width: 20%;"></span>
                                                                                                           <span class="tooltiptext tooltip-top"></span>
                                                                                                       </div>
                                                                                                       <div class="progress-bar progress-bar-sm ">
                                                                                                           <span></span>
                                                                                                       </div>
                                                                                                       <div class="progress-value">
                                                                                                           <mark>0%</mark>
                                                                                                       </div>
                                                                                                   </div>
                                                                                               </div>
                                                                                           </div>
                                                                                       </div>
                                                                                       <div class="col-xl-8 col-lg-7 mb-4">
                                                                                           <div class="review-form-wrapper">
                                                                                               <h3 class="title tab-pane-title font-weight-bold mb-1">Submit Your
                                                                                                   Review</h3>
                                                                                               <p class="mb-3">Your email address will not be published. Required
                                                                                                   fields are marked *</p>
                                                                                               <form action="#" method="POST" class="review-form">
                                                                                                   <div class="rating-form">
                                                                                                       <label for="rating">Your Rating Of This Product :</label>
                                                                                                       <span class="rating-stars">
                                                                                                           <a class="star-1" href="#">1</a>
                                                                                                           <a class="star-2" href="#">2</a>
                                                                                                           <a class="star-3" href="#">3</a>
                                                                                                           <a class="star-4" href="#">4</a>
                                                                                                           <a class="star-5" href="#">5</a>
                                                                                                       </span>
                                                                                                       <select name="rating" id="rating" required=""
                                                                                                           style="display: none;">
                                                                                                           <option value="">Rate…</option>
                                                                                                           <option value="5">Perfect</option>
                                                                                                           <option value="4">Good</option>
                                                                                                           <option value="3">Average</option>
                                                                                                           <option value="2">Not that bad</option>
                                                                                                           <option value="1">Very poor</option>
                                                                                                       </select>
                                                                                                   </div>
                                                                                                   <textarea cols="30" rows="6" placeholder="Write Your Review Here..." class="form-control"
                                                                                                       id="review"></textarea>
                                                                                                   <div class="row gutter-md">
                                                                                                       <div class="col-md-6">
                                                                                                           <input type="text" class="form-control" placeholder="Your Name"
                                                                                                               id="author">
                                                                                                       </div>
                                                                                                       <div class="col-md-6">
                                                                                                           <input type="text" class="form-control" placeholder="Your Email"
                                                                                                               id="email_1">
                                                                                                       </div>
                                                                                                   </div>
                                                                                                   <div class="form-group">
                                                                                                       <input type="checkbox" class="custom-checkbox" id="save-checkbox">
                                                                                                       <label for="save-checkbox">Save my name, email, and website
                                                                                                           in this browser for the next time I comment.</label>
                                                                                                   </div>
                                                                                                   <button type="submit" class="btn btn-dark">Submit
                                                                                                       Review</button>
                                                                                               </form>
                                                                                           </div>
                                                                                       </div>
                                                                                   </div>
                                                                                   <div class="tab tab-nav-boxed tab-nav-outline tab-nav-center">
                                                                                       <ul class="nav nav-tabs" role="tablist">
                                                                                           <li class="nav-item">
                                                                                               <a href="#show-all" class="nav-link active">Show All</a>
                                                                                           </li>
                                                                                           <li class="nav-item">
                                                                                               <a href="#helpful-positive" class="nav-link">Most Helpful
                                                                                                   Positive</a>
                                                                                           </li>
                                                                                           <li class="nav-item">
                                                                                               <a href="#helpful-negative" class="nav-link">Most Helpful
                                                                                                   Negative</a>
                                                                                           </li>
                                                                                           <li class="nav-item">
                                                                                               <a href="#highest-rating" class="nav-link">Highest Rating</a>
                                                                                           </li>
                                                                                           <li class="nav-item">
                                                                                               <a href="#lowest-rating" class="nav-link">Lowest Rating</a>
                                                                                           </li>
                                                                                       </ul>
                                                                                       <div class="tab-content">
                                                                                           <div class="tab-pane active" id="show-all">
                                                                                               <ul class="comments list-style-none">
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/1-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:54 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 60%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>pellentesque habitant morbi tristique senectus
                                                                                                                   et. In dictum non consectetur a erat.
                                                                                                                   Nunc ultrices eros in cursus turpis massa
                                                                                                                   tincidunt ante in nibh mauris cursus mattis.
                                                                                                                   Cras ornare arcu dui vivamus arcu felis bibendum
                                                                                                                   ut tristique.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (1)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (0)
                                                                                                                   </a>
                                                                                                                   <div class="review-image">
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-1.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-1-800x900.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/2-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:52 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 80%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>Nullam a magna porttitor, dictum risus nec,
                                                                                                                   faucibus sapien.
                                                                                                                   Ultrices eros in cursus turpis massa tincidunt
                                                                                                                   ante in nibh mauris cursus mattis.
                                                                                                                   Cras ornare arcu dui vivamus arcu felis bibendum
                                                                                                                   ut tristique.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (1)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (0)
                                                                                                                   </a>
                                                                                                                   <div class="review-image">
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-2.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-2.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-3.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-3.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/3-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:21 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 60%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>In fermentum et sollicitudin ac orci phasellus. A
                                                                                                                   condimentum vitae
                                                                                                                   sapien pellentesque habitant morbi tristique
                                                                                                                   senectus et. In dictum
                                                                                                                   non consectetur a erat. Nunc scelerisque viverra
                                                                                                                   mauris in aliquam sem fringilla.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (0)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (1)
                                                                                                                   </a>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                               </ul>
                                                                                           </div>
                                                                                           <div class="tab-pane" id="helpful-positive">
                                                                                               <ul class="comments list-style-none">
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/1-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:54 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 60%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>pellentesque habitant morbi tristique senectus
                                                                                                                   et. In dictum non consectetur a erat.
                                                                                                                   Nunc ultrices eros in cursus turpis massa
                                                                                                                   tincidunt ante in nibh mauris cursus mattis.
                                                                                                                   Cras ornare arcu dui vivamus arcu felis bibendum
                                                                                                                   ut tristique.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (1)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (0)
                                                                                                                   </a>
                                                                                                                   <div class="review-image">
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-1.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-1.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/2-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:52 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 80%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>Nullam a magna porttitor, dictum risus nec,
                                                                                                                   faucibus sapien.
                                                                                                                   Ultrices eros in cursus turpis massa tincidunt
                                                                                                                   ante in nibh mauris cursus mattis.
                                                                                                                   Cras ornare arcu dui vivamus arcu felis bibendum
                                                                                                                   ut tristique.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (1)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (0)
                                                                                                                   </a>
                                                                                                                   <div class="review-image">
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-2.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-2-800x900.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-3.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-3-800x900.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                               </ul>
                                                                                           </div>
                                                                                           <div class="tab-pane" id="helpful-negative">
                                                                                               <ul class="comments list-style-none">
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/3-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:21 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 60%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>In fermentum et sollicitudin ac orci phasellus. A
                                                                                                                   condimentum vitae
                                                                                                                   sapien pellentesque habitant morbi tristique
                                                                                                                   senectus et. In dictum
                                                                                                                   non consectetur a erat. Nunc scelerisque viverra
                                                                                                                   mauris in aliquam sem fringilla.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (0)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (1)
                                                                                                                   </a>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                               </ul>
                                                                                           </div>
                                                                                           <div class="tab-pane" id="highest-rating">
                                                                                               <ul class="comments list-style-none">
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/2-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:52 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 80%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>Nullam a magna porttitor, dictum risus nec,
                                                                                                                   faucibus sapien.
                                                                                                                   Ultrices eros in cursus turpis massa tincidunt
                                                                                                                   ante in nibh mauris cursus mattis.
                                                                                                                   Cras ornare arcu dui vivamus arcu felis bibendum
                                                                                                                   ut tristique.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (1)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (0)
                                                                                                                   </a>
                                                                                                                   <div class="review-image">
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-2.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-2-800x900.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-3.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-3-800x900.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                               </ul>
                                                                                           </div>
                                                                                           <div class="tab-pane" id="lowest-rating">
                                                                                               <ul class="comments list-style-none">
                                                                                                   <li class="comment">
                                                                                                       <div class="comment-body">
                                                                                                           <figure class="comment-avatar">
                                                                                                               <img src="assets/images/agents/1-100x100.png"
                                                                                                                   alt="Commenter Avatar" width="90" height="90">
                                                                                                           </figure>
                                                                                                           <div class="comment-content">
                                                                                                               <h4 class="comment-author">
                                                                                                                   <a href="#">John Doe</a>
                                                                                                                   <span class="comment-date">March 22, 2021 at
                                                                                                                       1:54 pm</span>
                                                                                                               </h4>
                                                                                                               <div class="ratings-container comment-rating">
                                                                                                                   <div class="ratings-full">
                                                                                                                       <span class="ratings" style="width: 60%;"></span>
                                                                                                                       <span class="tooltiptext tooltip-top"></span>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                               <p>pellentesque habitant morbi tristique senectus
                                                                                                                   et. In dictum non consectetur a erat.
                                                                                                                   Nunc ultrices eros in cursus turpis massa
                                                                                                                   tincidunt ante in nibh mauris cursus mattis.
                                                                                                                   Cras ornare arcu dui vivamus arcu felis bibendum
                                                                                                                   ut tristique.</p>
                                                                                                               <div class="comment-action">
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-secondary btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-up"></i>Helpful (1)
                                                                                                                   </a>
                                                                                                                   <a href="#"
                                                                                                                       class="btn btn-dark btn-link btn-underline sm btn-icon-left font-weight-normal text-capitalize">
                                                                                                                       <i class="far fa-thumbs-down"></i>Unhelpful
                                                                                                                       (0)
                                                                                                                   </a>
                                                                                                                   <div class="review-image">
                                                                                                                       <a href="#">
                                                                                                                           <figure>
                                                                                                                               <img src="assets/images/products/default/review-img-3.jpg"
                                                                                                                                   width="60" height="60"
                                                                                                                                   alt="Attachment image of John Doe's review on Electronics Black Wrist Watch"
                                                                                                                                   data-zoom-image="assets/images/products/default/review-img-3-800x900.jpg" />
                                                                                                                           </figure>
                                                                                                                       </a>
                                                                                                                   </div>
                                                                                                               </div>
                                                                                                           </div>
                                                                                                       </div>
                                                                                                   </li>
                                                                                               </ul>
                                                                                           </div>
                                                                                       </div>
                                                                                   </div>
                                                                               </div>
                                                                           </div> -->
                                <!-- <div class="card">
                                                                               <div class="card-header ls-normal">
                                                                                   <a href="#product-tab-vendor" class="expand">Vendor Info</a>
                                                                               </div>
                                                                               <div class="card-body collapsed" id="product-tab-vendor">
                                                                                   <div class="row mb-3">
                                                                                       <div class="col-md-6 mb-4">
                                                                                           <figure class="vendor-banner br-sm">
                                                                                               <img src="assets/images/products/vendor-banner.jpg" alt="Vendor Banner"
                                                                                                   width="610" height="295" style="background-color: #353B55;" />
                                                                                           </figure>
                                                                                       </div>
                                                                                       <div class="col-md-6 pl-2 pl-md-6 mb-4">
                                                                                           <div class="vendor-user">
                                                                                               <figure class="vendor-logo mr-4">
                                                                                                   <a href="#">
                                                                                                       <img src="assets/images/products/vendor-logo.jpg" alt="Vendor Logo"
                                                                                                           width="80" height="80" />
                                                                                                   </a>
                                                                                               </figure>
                                                                                               <div>
                                                                                                   <div class="vendor-name"><a href="#">Jone Doe</a></div>
                                                                                                   <div class="ratings-container">
                                                                                                       <div class="ratings-full">
                                                                                                           <span class="ratings" style="width: 90%;"></span>
                                                                                                           <span class="tooltiptext tooltip-top"></span>
                                                                                                       </div>
                                                                                                       <a href="#" class="rating-reviews">(32 Reviews)</a>
                                                                                                   </div>
                                                                                               </div>
                                                                                           </div>
                                                                                           <ul class="vendor-info list-style-none pl-0">
                                                                                               <li class="store-name">
                                                                                                   <label>Store Name:</label>
                                                                                                   <span class="detail">OAIO Store</span>
                                                                                               </li>
                                                                                               <li class="store-address">
                                                                                                   <label>Address:</label>
                                                                                                   <span class="detail">Steven Street, El Carjon, CA 92020, United
                                                                                                       States (US)</span>
                                                                                               </li>
                                                                                               <li class="store-phone">
                                                                                                   <label>Phone:</label>
                                                                                                   <a href="#tel:">1234567890</a>
                                                                                               </li>
                                                                                           </ul>
                                                                                           <a href="vendor-dokan-store.html"
                                                                                               class="btn btn-dark btn-link btn-underline btn-icon-right">Visit
                                                                                               Store<i class="w-icon-long-arrow-right"></i></a>
                                                                                       </div>
                                                                                   </div>
                                                                                   <p class="mb-5"><strong class="text-dark">L</strong>orem ipsum dolor sit amet,
                                                                                       consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                                                                                       dolore magna aliqua.
                                                                                       Venenatis tellus in metus vulputate eu scelerisque felis. Vel pretium
                                                                                       lectus quam id leo in vitae turpis massa. Nunc id cursus metus aliquam.
                                                                                       Libero id faucibus nisl tincidunt eget. Aliquam id diam maecenas ultricies
                                                                                       mi eget mauris. Volutpat ac tincidunt vitae semper quis lectus. Vestibulum
                                                                                       mattis ullamcorper velit sed. A arcu cursus vitae congue mauris.
                                                                                   </p>
                                                                                   <p class="mb-2"><strong class="text-dark">A</strong> arcu cursus vitae congue
                                                                                       mauris. Sagittis id consectetur purus
                                                                                       ut. Tellus rutrum tellus pellentesque eu tincidunt tortor aliquam nulla.
                                                                                       Diam in
                                                                                       arcu cursus euismod quis. Eget sit amet tellus cras adipiscing enim eu. In
                                                                                       fermentum et sollicitudin ac orci phasellus. A condimentum vitae sapien
                                                                                       pellentesque
                                                                                       habitant morbi tristique senectus et. In dictum non consectetur a erat. Nunc
                                                                                       scelerisque viverra mauris in aliquam sem fringilla.</p>
                                                                               </div>
                                                                           </div> -->
                            </div>
                            <section class="vendor-product-section">
                                <div class="title-link-wrapper mb-4">
                                    <h4 class="title text-left">Related Products </h4>

                                </div>
                                <div class="swiper-container swiper-theme"
                                    data-swiper-options="{
                                   'spaceBetween': 20,
                                   'slidesPerView': 2,
                                   'breakpoints': {
                                       '576': {
                                           'slidesPerView': 3
                                       },
                                       '768': {
                                           'slidesPerView': 4
                                       },
                                       '992': {
                                           'slidesPerView': 3
                                       }
                                   }
                               }">
                                    <div class="swiper-wrapper row cols-lg-3 cols-md-4 cols-sm-3 cols-2">
                                        @foreach ($related_products as $pr)
                                            <x-frontend.product :product="$pr" />
                                        @endforeach
                                        >

                                    </div>
                                </div>
                            </section>

                        </div>
                    </div>

                    <!-- End of Main Content -->

                    <!-- End of Sidebar -->
                </div>
            </div>
        </div>
        <!-- End of Page Content -->
    </main>


@endsection
