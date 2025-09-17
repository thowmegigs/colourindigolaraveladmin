@props(['product'])

<div class="product product-single product-modal mfp-hide " style="max-width:700px;padding:20px;" id="productModal{{ $product->id }}">
    <div class="row gutter-lg">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="product-gallery product-gallery-sticky">
                <div
                    class="swiper-container product-single-swiper swiper-theme nav-inner swiper-container-initialized swiper-container-horizontal swiper-container-pointer-events">
                    <div class="swiper-wrapper " id="swiper-wrapper-3ebe89b74b75b3b3" aria-live="polite"
                        style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px);">
                        @if (count($product->images) > 0)
                            @foreach ($product->images as $img)
                                <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 4"
                                    style="width: 395px;">
                                    <figure class="product-image"
                                        style="position: relative; overflow: hidden; cursor: pointer;height:200px;width:300px;">
                                        <img src="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['large']) }}"
                                            data-zoom-image="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['small']) }}"
                                            alt="Water Boil Black Utensil"  style="width:200px;object-fit:fill;margin:auto">
                                  
                                    </figure>
                                </div>
                            @endforeach
                          @else
                          <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 4"
                          style="width: 395px;">
                          <figure class="product-image"
                              style="position: relative; overflow: hidden; cursor: pointer;height:200px;width:300px;">
                              <img src="{{ asset('storage/products/' . $product->id . '/' . $product->image) }}"
                                  data-zoom-image="{{ asset('storage/products/' . $product->id . '/' . $product->image) }}"
                                  alt="Water Boil Black Utensil" style="width:200px;object-fit:fill;margin:auto">
                              </figure>
                      </div>
                        @endif

                    </div>
                    <button class="swiper-button-next" tabindex="0" aria-label="Next slide"
                        aria-controls="swiper-wrapper-3ebe89b74b75b3b3" aria-disabled="false"></button>
                    <button class="swiper-button-prev swiper-button-disabled" tabindex="-1" aria-label="Previous slide"
                        aria-controls="swiper-wrapper-3ebe89b74b75b3b3" aria-disabled="true" disabled=""></button>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span><span
                        class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                </div>
                <div class="product-thumbs-wrap swiper-container swiper-container-initialized swiper-container-horizontal swiper-container-pointer-events swiper-container-free-mode swiper-container-thumbs"
                    data-swiper-options="{
                    'navigation': {
                        'nextEl': '.swiper-button-next',
                        'prevEl': '.swiper-button-prev'
                    }
                }">
                    <div class="product-thumbs swiper-wrapper   " id="swiper-wrapper-aa38d350a461cda7"
                        aria-live="polite" style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px);">
                        @if (count($product->images) > 0)
                            @foreach ($product->images as $img)
                                <div class="product-thumb swiper-slide swiper-slide-visible swiper-slide-active swiper-slide-thumb-active"
                                    role="group" aria-label="1 / 4" style="width: 91.25px; margin-right: 10px;">
                                    <img src="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['small']) }}"
                                        alt="Product Thumb" style="width:70px;height:70px">
                                </div>
                            @endforeach
                            @else
                            <div class="product-thumb swiper-slide swiper-slide-visible swiper-slide-active swiper-slide-thumb-active"
                            role="group" aria-label="1 / 4" style="width: 91.25px; margin-right: 10px;">
                            <img src="{{ asset('storage/products/' . $product->id . '/' . $product->image) }}"
                                alt="Product Thumb" style="width:70px;height:70px">
                        </div>

                        @endif


                    </div>
                    <button class="swiper-button-next swiper-button-disabled" tabindex="-1" aria-label="Next slide"
                        aria-controls="swiper-wrapper-aa38d350a461cda7" aria-disabled="true" disabled=""></button>
                    <button class="swiper-button-prev swiper-button-disabled" tabindex="-1" aria-label="Previous slide"
                        aria-controls="swiper-wrapper-aa38d350a461cda7" aria-disabled="true" disabled=""></button>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span><span
                        class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 overflow-hidden p-relative" x-data="{
            clicked: false,
            is_adding: false,
            is_added: false,
        
            item: {
                product_id: {{ $product->id }},
                qty: 1,
                name: '{{ $product->name }}',
                sale_price: {{ $product->sale_price }},
                price: {{ $product->price }},
                sgst: {{ $product->sgst }},
                cgst: {{ $product->cgst }},
                igst: {{ $product->igst }},
                unit: '{{ empty($product->unit) ? 'pcs' : $product->unit }}',
        
            },
        
            init() {
        
        
                this.$watch('$store.cart.items', () => {
                    this.item.qty = $store.cart.itemQty({{ $product->id }});
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
        
        
        
        
        }">
            <div class="product-details scrollable pl-0">
                <h2 class="product-title">{{ ucwords($product->name) }}</h2>
                <div class="product-bm-wrapper">
                   
                     <div class="product-bm-wrapper">

                                        <div class="product-meta">
                                            <div class="product-categories">
                                                Category:
                                                <span class="product-category"><a
                                                        href=/products/{{ \Str::slug($product->category_name) }}">{{ ucwords($product->category_name) }}</a></span>
                                            </div>
                                            @if($product->quantity<1)
                                              <p style="color:red;margin-bottom:0px!important">Out of stock</p>
                                            @endif
                                            <!-- <div class="product-sku">
                                                                                   SKU: <span>MS46891390</span>
                                                                               </div> -->
                                        </div>
                                    </div>
                </div>

             <hr class="product-divider" style='margin:0'>

                <div class="product-price" style="text-align:left">{{ getCurrency() }}{{ formateNumber($product->sale_price) }}</div>

                {{-- <div class="ratings-container">
                    <div class="ratings-full">
                        <span class="ratings" style="width: 80%;"></span>
                        <span class="tooltiptext tooltip-top"></span>
                    </div>
                    <a href="#" class="rating-reviews">(3 Reviews)</a>
                </div> --}}

                <div class="product-short-desc">
                    {{ $product->short_description }}
                </div>

                <hr class="product-divider">



                @php
                    $attrs = $product->attributes != null  ? (!is_array($product->attributes)?json_decode($product->attributes, true):$product->attributes) : [];
                @endphp
                @if (count($attrs) > 0)
                    @foreach ($attrs as $y)
                        @php
                            $vals = explode(',', $y['value']);

                        @endphp
                        <div class="product-form product-variation-form product-size-swatch">
                            <label class="mb-1">{{ $y['name'] }}:</label>
                            <div class="flex-wrap d-flex align-items-center product-variations">
                                @foreach ($vals as $v)
                                    <a href="#" class="size">{{ $v }}</a>
                                @endforeach
                            </div>
                            <a href="#" class="product-variation-clean" style="display: none;">Clean All</a>
                        </div>
                    @endforeach
                @endif 
              

                <div class="product-form" >
                    <div class="product-qty-form">
                        <div class="input-group">
                            <input class="quantity form-control" type="number" min="1"
                                x-model="item.qty" max="10">

                            <button class=" w-icon-plus" @click="inc()"></button>
                            <button class=" w-icon-minus" @click="dec()"></button>
                        </div>
                    </div>
                    <button  @if($product->quantity<1)
                                                disabled
                                                @endif :class="is_adding ? 'load-more-overlay loading' : ''" class="btn btn-primary "  @click="addToCart()">
                         <i class="w-icon-cart"></i>
                        <span>Add to Cart</span>
                    </button>
                </div>


            </div>
        </div>
    </div>
    <button title="Close (Esc)" type="button" class="mfp-close">×</button>
</div>
