@extends('layouts.frontend.app')
@section('content')
    @php
        $p = $product;

        $percent = intval((($p->price - $p->sale_price) / $p->price) * 100);
    
   
$colors=\DB::table('color_mappings')->get()->pluck('code','name')->toArray();

@endphp
<style>
    label.active{
        border:2px solid black;padding:3px;
    }
</style>
    <section class="py-5 shop-single bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="shop-detail-left">
                        <div class="shop-detail-slider position-relative">
                            <div class="favourite-icon"> <a class="fav-btn" title data-placement="bottom" data-toggle="tooltip"
                                    href="#" data-original-title="59% OFF"><i class="icofont-ui-tag"></i></a>
                            </div>
                            <div id="sync1" class="border rounded shadow-sm bg-white mb-2 owl-carousel text-center">
                                @if (count($product->images) > 0)
                                    @foreach ($product->images as $img)
                                        <div class="item bg-b">
                                            <img alt
                                                src="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['large']) }}"
                                                class="img-fluid img-center">
                                        </div>
                                    @endforeach
                                @endif


                            </div>
                            <div id="sync2" class="owl-carousel">
                                @if (count($product->images) > 0)
                                    @foreach ($product->images as $img)
                                        <div class="item">
                                            <img alt
                                                src="{{ asset('storage/products/' . $product->id . '/thumbnail/' . $img->thumbnail['medium']) }}"
                                                class="img-fluid img-center">
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" x-data="{
                    clicked: false,
                    current_single_product_sale_price: {{ count($product->variants) > 0 ? $product->variants[0]->sale_price : $product->sale_price }},
                    current_single_product_price: {{ count($product->variants) > 0 ? $product->variants[0]->price : $product->price }},
                   
                    total_payable: {{ count($product->variants) > 0 ? $product->variants[0]->sale_price : $product->sale_price }},
                
                    is_adding: false,
                    is_added: false,
                    variant_options:'{{ isset($product->variants[0])?$product->variants[0]->atributes_json :'' }}',
                    cart_items:[],
                
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
                        variant_attributes_val:[]
                
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
                       
                
                        if ($(target).is(':checked') || is_qty_changed) {
                            f.push(item_to_add);
                        }
                        this.item.addon_items = f;
                
                       
                
                        this.calculateTotalPayable()
                
                
                
                    },
                    attributeInCartItemArray(atr_val){
                    let f=false;
                    let itms=this.$store.cart.cartItems({{$product->id}})
                 
                    itms.forEach(function(v){
                   
                      if(v.variant_name==atr_val) f=true;
                    })
                    return f;
                    },
                    addVariantOption(option_val) {
                        let target = this.$event.target;
                       this.variant_options=this.variant_options.constructor == Object?this.variant_options:JSON.parse(this.variant_options);
                       console.log(this.variant_options['Color'])
                        let name=target.getAttribute('data-name');
                        if(this.variant_options[name]!==undefined)
                         delete this.variant_options[name]
                        
              
                        
                
                        if (!$(target).hasClass('active')) {
                           this.variant_options[name]=option_val;
                        }
                       
                        this.item.variant_attributes_val = Object.values(this.variant_options);
                        // console.log(this.item.variant_attributes_val)
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
                                this.item.variant_id=res['variant_id'];
                                this.calculateTotalPayable();
                
                            }
                
                        })
                
                
                
                
                    }
                
                
                
                
                }">
                    <div class="shop-detail-right">
                        <div class="border rounded shadow-sm bg-white p-4">
                            <div class="product-name">
                                <p class="text-danger text-uppercase mb-0"> <i class="icofont-sale-discount"></i>
                                    {{ $percent }}% Off</p>
                                <h2>{{ ucwords($product->name) }}</h2>
                                <!-- <span>Product code: <b>OSAHAN456</b> | <strong class="text-info">FREE Delivery</strong> on orders over $299</span> -->
                            </div>
                            <div class="price-box">
                                <h5>
                                    <span class="product-desc-price">{{ getCurrency() }} <span x-text="current_single_product_sale_price"></span></span>
                                    <span class="product-price text-danger">{{ getCurrency() }}<span x-text="current_single_product_price"></span></span><br>
                                    <small class="text-success">You Save
                                        :{{ getCurrency() }}<span x-text="current_single_product_price-current_single_product_sale_price"></span></small>
                                </h5>
                            </div>
                            
                            <div class="clearfix"></div>
                            @php
                                $attrs = $product->attributes;
                            @endphp
                            @if (!is_null($attrs))
                                <div class="product-color-size-area mt-3">
                                    @foreach ($attrs as $y)
                                        @php
                                            $vals = !is_array($y['value'])?explode(',', $y['value']):$y['value'];

                                        @endphp
                                        <div style="display:flex;justify-content:flex-start;margin-bottom:5px;gap:20px">
                                        
                                        <span class="d-inline-block pt-1">{{ $y['name'] }} : </span>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                             @if($y['name']=='Color')
                                                @foreach ($vals as $v)
                                                  
                                                    <label @click="addVariantOption('{!! $v !!}')" data-name="{{$y['name']}}" :class="attributeInCartItemArray('{{$v}}')?'active':''"
                                                        class="btn btn-sm" style="background:{{$colors[$v]}};width: 20px;height: 20px;border-radius: 50%;margin: 5px;">
                                                        <input type="radio" name="options"  autocomplete="off" value="{{$v}}"
                                                            
                                                             ></label>
                                                      @endforeach
                                              @else
                                                 @foreach ($vals as $v)
                                                <label @click="addVariantOption('{!! $v !!}')" data-name="{{$y['name']}}" :class="attributeInCartItemArray('{{$v}}')?'active':''"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <input type="radio" name="options"  autocomplete="off" value="{{$v}}"
                                                        
                                                         >{{$v}}</label>
                                                         @endforeach
                                                @endif
                                              

                                        </div></div>
                                        @endforeach
                                </div>
                            @endif
                            <div class="clearfix"></div>
                            <div class="product-variation">
                                <form action="#" method="post">
                                    <div class="mt-1 pt-2 float-left mr-2">Quantity :</div>
                                    <div class="input-group quantity-input"> <span class="input-group-btn">
                                            <button type="button" class="btn btn-outline-secondary btn-number btn-lg"
                                                data-type="minus" @click="dec()" data-field="quant[1]">
                                                <span class="fa fa-minus"></span>
                                            </button>
                                        </span>
                                        <input type="text" x-model="item.qty" max="10" name="quant[1]"
                                            class="text-center form-control border-form-control form-control-sm input-number"
                                            value="1"> <span class="input-group-btn">
                                            <button type="button" @click="inc()"
                                                class="btn btn-outline-secondary btn-number btn-lg" data-type="plus"
                                                data-field="quant[1]">
                                                <span class="fa fa-plus"></span>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="float-right">
                                       {{-- <button type="button" title data-placement="top" data-toggle="tooltip"
                                            href="#" data-original-title="Add to Wishlist"
                                            class="btn btn-outline-primary btn-lg"><i
                                                class="icofont icofont-heart"></i></button>--}}
                                                <template x-if="!is_adding">
                                                      <button type="button" @if ($product->quantity < 1) disabled @endif
                                            class="btn btn-primary btn-lg"
                                          
                                            @click="addToCart()">&nbsp;&nbsp;&nbsp; <i
                                                class="icofont icofont-shopping-cart"></i> Add To Cart
                                            &nbsp;&nbsp;&nbsp;</button>
                                                    
                                                </template>
                                                 <template x-if="is_adding">
                                                       <button type="button" @if ($product->quantity < 1) disabled @endif
                                            class="btn btn-primary btn-lg"
                                            
                                            @click="addToCart()">&nbsp;&nbsp;&nbsp; <i
                                                class="icofont icofont-shopping-cart"></i> Adding...
                                            &nbsp;&nbsp;&nbsp;</button>
                                                    
                                                </template>
                                       
                                    </span>
                                </form>
                            </div>
                            <div class="short-description border-bottom">
                                <h6 class="mb-3">
                                    <span class="text-dark font-weight-bold">Quick Overview</span>
                                    <small class="float-right">Availability: <strong class="badge @if($product->quantity>0)badge-success @else badge-danger @endif">
                                    @if($product->quantity>0)In Stock @else Out Of Stock @endif </strong></small>
                                </h6>
                                <p> {!! $product->short_description !!} </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="det" class="pb-5 pt-0 shop-single-detail bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="rounded shadow-sm bg-white">
                        <ul class="nav nav-pills p-3" id="pills-tab" role="tablist">
                            <li class="nav-item"> <a class="nav-link active" id="pills-home-tab" data-toggle="pill"
                                    href="#pills-home" role="tab" aria-controls="pills-home"
                                    aria-selected="true">DETAILS</a>
                            </li>

                            <li class="nav-item"> <a class="nav-link" id="pills-contact-tab" data-toggle="pill"
                                    href="#pills-contact" role="tab" aria-controls="pills-contact"
                                    aria-selected="false">REVIEWS (0)</a>
                            </li>
                        </ul>
                        <div class="tab-content p-4 border-top" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                aria-labelledby="pills-home-tab">
                                {!! $product->short_description !!}
                            </div>

                            <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                aria-labelledby="pills-contact-tab">
                                <div class="card-body p-0 reviews-card">
                                    
                                </div>
                                <div class="p-4 bg-light rounded mt-4">
                                    <h5 class="card-title mb-4">Leave a Review</h5>
                                    <form name="sentMessage">
                                        <div class="row">
                                            <div class="control-group form-group col-lg-4 col-md-4">
                                                <div class="controls">
                                                    <label>Your Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="control-group form-group col-lg-4 col-md-4">
                                                <div class="controls">
                                                    <label>Your Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="control-group form-group col-lg-4 col-md-4">
                                                <div class="controls">
                                                    <label>Rating <span class="text-danger">*</span></label>
                                                    <select class="form-control custom-select">
                                                        <option>1 Star</option>
                                                        <option>2 Star</option>
                                                        <option>3 Star</option>
                                                        <option>4 Star</option>
                                                        <option>5 Star</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="control-group form-group">
                                            <div class="controls">
                                                <label>Review <span class="text-danger">*</span></label>
                                                <textarea rows="3" cols="100" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">Send Message</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if ($related_products->count() > 0)
        <section class="product-list pt-5 bg-light pb-4 pbc-5 border-top">
            <div class="container">

                <h4 class="mt-0 mb-3 text-dark">Related Items</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="owl-carousel owl-carousel-category owl-theme">
                            @foreach ($related_products as $pr)
                                <div class="item mx-2">
                                    <x-frontend.product :product="$pr" />
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif


@endsection
