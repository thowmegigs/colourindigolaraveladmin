<footer class="bg-white border-bottom border-top d-none d-sm-block">
    @php 
    $setting=\DB::table('settings')->first();
    @endphp
    <div class="container">
        <div class="row no-gutters">
            <div class="col-md-4">
                <div class="border-right py-5 pr-5 ">
                    <h6 class="mt-0 mb-4 f-14 text-dark font-weight-bold">CONTACT INFO</h6>
                    <ul class="list-unstyled">
                            <li class="d-flex">
                                <i class="icofont-location-pin pr-1" style="line-height:25px;"></i>
                                <span>{{$setting->address}}</span>
                            </li>
                            <li class="d-flex">
                                <i class="fa fa-envelope pr-1" style="line-height:25px;"></i>
                               
                                <a href="mailto:info@sitename.com">{{$setting->email}}</a>
                            </li>
                            <li class="d-flex">
                                <i class="icofont-smart-phone pr-1" style="line-height:25px;"></i>
                                <p>+ 91{{$setting->phone}}</p>
                            </li>
                        </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border-right py-5 px-5">
                    <h6 class="mt-0 mb-4 f-14 text-dark font-weight-bold">Important Links</h6>
                    <div class="row no-gutters">
                       
                        <div class="col-6">
                            <ul class="list-unstyled mb-0">
                               
                                <li><a href="{{\URL::to('terms_and_conditions')}}">Terms & Conditions</a></li>
                                <li><a href="{{\URL::to('privacy_policy')}}">Privacy Policy</a></li>
                                <li><a href="{{\URL::to('shipping_policy')}}">Shipping Policy</a></li>
                                <li><a href="{{\URL::to('return_policy')}}">Return Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="py-5 pl-5">
                    <h6 class="mt-0 mb-4 f-14 text-dark font-weight-bold">DOWNLOAD APP</h6>
                    <div class="app">
                        <a href="#">
                            <img class="img-fluid" src="{{ asset('front_assets/img/google.png') }}">
                        </a>
                        <a href="#">
                            <img class="img-fluid" src="{{ asset('front_assets/img/apple.png') }}">
                        </a>
                    </div>
                    <h6 class="mt-4 mb-4 f-14 text-dark font-weight-bold">KEEP IN TOUCH</h6>
                    <div class="footer-social">
                        <a class="btn-facebook" href="#"><i class="icofont-facebook"></i></a>
                        <a class="btn-twitter" href="#"><i class="icofont-twitter"></i></a>
                        <a class="btn-instagram" href="#"><i class="icofont-instagram"></i></a>
                        <a class="btn-whatsapp" href="#"><i class="icofont-whatsapp"></i></a>
                        <a class="btn-messenger" href="#"><i class="icofont-facebook-messenger"></i></a>
                        <a class="btn-google" href="#"><i class="icofont-google-plus"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<div class="copyright bg-light py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center">
                <p class="mb-0">© Copyright 2024 <a href="#">MJFashion</a> . All Rights Reserved
                </p>
            </div>
            <div class="col-md-6 text-right">
                <img class="img-fluid" src="{{ asset('front_assets/img/payment_methods.png') }}">
            </div>
        </div>
    </div>
</div>
</footer>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Search...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <input class="form-control form-control-lg mb-3" type="text"
                    placeholder="Search for products, brands and more">
                <button type="button" class="btn btn-primary btn-block btn-lg">Search</button>
            </div>
        </div>
    </div>
</div>
<div class="cart-sidebar" x-data>
    <div class="cart-sidebar-header">
        <h5>
            My Cart <span class="text-info">( <spann x-text="$store.cart.items.length"></span> item)</span> <a
                data-toggle="offcanvas" class="float-right" href="#"><i
                    class="icofont icofont-close-line"></i>
            </a>
        </h5>
    </div>
    <div class="cart-sidebar-body">
        <template x-if="$store.cart.minimum_cart_amount_offer!=null">
            <div class="alert alert-danger p-2" role="alert">
                Shop more worth
                <span x-text="$store.cart.currency"></span>
                <span x-text="$store.cart.minimum_cart_amount_offer.target_amount"></span>
                to get
                <span x-text="$store.cart.minimum_cart_amount_offer.discount"></span> % discount

            </div>
        </template>
        <template x-if="$store.cart.items.length>0">
            <template x-for="item in $store.cart.items" :key="item.id">
                <div class="cart-list-product" x-data="{
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
                    <a class="float-right remove-cart" href="javascript:void(0)"
                        @click.prevent="$store.cart.deleteItem(item.product_id)"><i
                            class="icofont icofont-close-circled"></i></a>
                    <img class="img-fluid" :src="item.image"
                         alt>
                    <!-- <span class="badge badge-success">50% OFF</span> -->
                    <h5><a :href="$store.cart.product_url(item.name)" x-text="item.name">Floret
                            Printed Ivory Skater Dress</a></h5>

                             <template x-if="item.atributes_json">
                            <div style="font-size:11px;margin-bottom:10px">
                                 <template x-for="[key, value] in Object.entries(item.atributes_json)" :key="key">
                            <p class="m-0">
                              <strong  x-text="key"></strong>: <span x-text="value" class="text-success"></span>
                            </p>
                          </template>
                            </div>
                            </template>
                             
                          
                    <template x-if="item.is_combo_offer=='Yes'">

                        <span>Product Added As combo Offer</span>
                    </template>
                    <template x-if="item.product_discount_offer_text!=null">

                        <span x-text="item.product_discount_offer_text"></span>

                    </template>
                    <p class="f-14 mb-0 text-dark float-right"><span
                            x-text="$store.cart.amount_in_cur(item.sale_price*item.qty)"></span>
                        <del class="small text-secondary"><span x-text="$store.cart.amount_in_cur(item.price*item.qty)"></span>
                        </del>
                    </p>
                    <span class="count-number float-left">
                        <button class="btn btn-outline-secondary  btn-sm left dec" @click="dec()"> <i
                                class="icofont-minus"></i> </button>
                        <input class="count-number-input" max="10" x-model="local_item.qty" type="text"
                            value="1" readonly>
                        <button class="btn btn-outline-secondary btn-sm right inc" @click="inc()"> <i
                                class="icofont-plus"></i> </button>
                    </span>
                </div>
            </template>
        </template>

    </div>

    <div class="cart-sidebar-footer" style="position: fixed;
    bottom: 0px;width:100%">
        <div class="cart-store-details">
            <p>Sub Total <strong class="float-right" x-text="$store.cart.amount_in_cur($store.cart.sub_total)"></strong>
            </p>
            <!-- <p>Delivery Charges <strong class="float-right text-danger">+ $29.69</strong></p> -->
            <h6>Your total savings -<strong class="float-right text-success"
                    x-text="$store.cart.amount_in_cur($store.cart.total_discount)"></strong></h6>
            <template x-if="$store.cart.cart_discount>0">
                <h6>Cart Discount -<strong class="float-right text-danger"
                        x-text="$store.cart.amount_in_cur($store.cart.cart_discount)"></strong></h6>


            </template>
        </div>
         <template x-if="$store.cart.items.length>0">
        <a href="/checkout">
            <button class="btn btn-primary btn-lg btn-block text-left" type="button" ><span class="float-left ">
                    <i class="icofont icofont-cart"></i> Proceed to Checkout </span><span class="float-right"><strong
                        x-text="$store.cart.amount_in_cur($store.cart.sub_total)">
                    </strong> <span class="icofont icofont-bubble-right"></span></span>
            </button>
        </a>
        </template>
    </div>

</div>
<script src="{{ asset('front_assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('front_assets/js/swiper-bundle.min.js')}}"></script>
<script src="{{ asset('front_assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}" type="7956072f252d9bb0d7096083-text/javascript"></script>
<script src="{{ asset('front_assets/vendor/select2/js/select2.min.js')}}" type="7956072f252d9bb0d7096083-text/javascript"></script>
<script src="{{ asset('front_assets/vendor/owl-carousel/owl.carousel.js')}}" type="7956072f252d9bb0d7096083-text/javascript"></script>
<script src="{{ asset('front_assets/vendor/slider/slider.js')}}" type="7956072f252d9bb0d7096083-text/javascript"></script>
 <script src="{{ asset('commonjs/ion.rangeSlider.min.js') }}"></script>
<script src="{{ asset('front_assets/js/custom.js')}}?v=1" type="7956072f252d9bb0d7096083-text/javascript"></script>
<script src="{{ asset('front_assets/js/hc-offcanvas-nav0235.js')}}" type="7956072f252d9bb0d7096083-text/javascript"></script>
 <script src="{{ asset('commonjs/vanilla-notify.js') }}"></script>
<link rel="stylesheet" href="{{ asset('front_assets/js/demo6b00.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<script type="7956072f252d9bb0d7096083-text/javascript">
   (function($) {
       var $main_nav = $('#main-nav');
       var $toggle = $('.toggle');
   
       var defaultOptions = {
         disableAt: false,
         customToggle: $toggle,
         levelSpacing: 40,
         navTitle: 'All Categories',
         levelTitles: true,
         levelTitleAsBack: true,
         pushContent: '#container',
         insertClose: 2
       };
   
       // call our plugin
       var Nav = $main_nav.hcOffcanvasNav(defaultOptions);
   
       // add new items to original nav
       $main_nav.find('li.add').children('a').on('click', function() {
         var $this = $(this);
         var $li = $this.parent();
         var items = eval('(' + $this.attr('data-add') + ')');
   
         $li.before('<li class="new"><a href="#">'+items[0]+'</a></li>');
   
         items.shift();
   
         if (!items.length) {
           $li.remove();
         }
         else {
           $this.attr('data-add', JSON.stringify(items));
         }
   
         Nav.update(true);
       });
   
       // demo settings update
   
       const update = (settings) => {
         if (Nav.isOpen()) {
           Nav.on('close.once', function() {
             Nav.update(settings);
             Nav.open();
           });
   
           Nav.close();
         }
         else {
           Nav.update(settings);
         }
       };
   
       $('.actions').find('a').on('click', function(e) {
         e.preventDefault();
   
         var $this = $(this).addClass('active');
         var $siblings = $this.parent().siblings().children('a').removeClass('active');
         var settings = eval('(' + $this.data('demo') + ')');
   
         update(settings);
       });
   
       $('.actions').find('input').on('change', function() {
         var $this = $(this);
         var settings = eval('(' + $this.data('demo') + ')');
   
         if ($this.is(':checked')) {
           update(settings);
         }
         else {
           var removeData = {};
           $.each(settings, function(index, value) {
             removeData[index] = false;
           });
   
           update(removeData);
         }
       });
     })(jQuery);
</script>
<script src="{{ asset('front_assets/js/rocket-loader.min.js') }}" data-cf-settings="7956072f252d9bb0d7096083-|49" defer>
</script>
<script src="{{ asset('commonjs/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('commonjs/vanilla-notify.js') }}"></script>
<script src="{{ asset('commonjs/ion.rangeSlider.min.js') }}"></script>
<script src="{{ asset('commonjs/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('commonjs/lazyload.min.js') }}"></script>
<script defer src="{{ asset('commonjs/alpine.js') }}"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script type="text/javascript">
    var lazyLoadInstance = new LazyLoad({
        class_loading: 'loading1',
        callback_loaded: (el) => {
            $(el).removeClass('shimmer-background')
        }
    });

    function showLoader(id = null, text = null) {
        if (id) {
            jQuery('#' + id).LoadingOverlay('show', {
            image       : "",
                custom       : `<div class="col-md-12 text-center load-more">
<button class="btn btn-primary btn-sm" type="button" >
<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
Loading...
</button>
</div>`,
                
               
            });
        } else {
            jQuery('.main-content').LoadingOverlay('show', {image       : "",
               custom       : `<div class="col-md-12 text-center load-more">
<button class="btn btn-primary btn-sm" type="button">
<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
Loading...
</button>
</div>`
               
            });
        }

    }

    function isInView(value) {
        const item = value.getBoundingClientRect();
        return (
            item.top >= 0 &&
            item.left >= 0 &&
            item.bottom <= (
                window.innerHeight ||
                document.documentElement.clientHeight) &&
            item.right <= (
                window.innerWidth ||
                document.documentElement.clientWidth)
        );
    }

    function hideLoader(id = null) {
        if (id)
            jQuery('#' + id).LoadingOverlay('hide');
        else
            jQuery('.main-content').LoadingOverlay('hide');

    }
    $(".js-range-slider").ionRangeSlider({
        prefix: '{!! getCurrency() !!}',
        skin: 'sharp',
        type: 'double',

        onFinish: function(v) {

            $('#min_price_input').val(v.from).trigger('change')
            $('#max_price_input').val(v.to).trigger('change')
        }
    });
    document.addEventListener('alpine:initialized', () => {
        hideLoader();
    })
    document.addEventListener('alpine:init', () => {
        showLoader();
        Alpine.store('cart', {
            is_logged_in: '{!! \Auth::check() ? 'Yes' : 'No' !!}',
            items: [],
            applicable_offers: [],
            applied_offers: [],
            cart_discount: 0,
            shipping_discount: 0,
            minimum_cart_amount_offer: undefined,
            total: 0,
            count: 0,
            sub_total: 0,
            coupon_code: null,
            currency: '{!! getCurrency() !!}',
            total_discount: 0,
            is_applying_coupon: false,
            shipping_charge: 0,
            amount_in_cur(amount) {
                return this.currency + amount;
            },
            itemQty(product_id) {
                let pr = this.items.filter(item => item.product_id == product_id);
                let sum = pr.reduce(function(acc, obj) {
                    return acc + obj.qty;
                }, 0);
                return sum;
            },
            product_url(name) {
                name = name.replaceAll(' ', '-');
                return '/product/' + name;
            },
            product_image_url(product_id, image_name) {
                return '/assets/storage/product/' + product_id + '/thumbnail/' + image_name;
            },
            init() {


                let cart_session_data = localStorage.getItem('cart_session_id');
                if (cart_session_data) {
                    fetch('/cartData', {
                        method: 'POST',
                        headers: {

                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            cart_session_id: cart_session_data
                        })
                    }).then(response => response.json()).then(res => {
                        console.log('this is starting cart data');


                        if (res['success']) {

                            this.updateStore(res)
                        }

                    });
                }

            },
            updateStore(res) {
                if (res['success']) {
                    this.items = res['cart_items'];
                    this.applicable_offers = res['applicable_offers'] != null ? [...res[
                        'applicable_offers']] : [];
                    this.applied_offers = res['applied_coupons'] != null ? [...res['applied_coupons']] :
                        [];
                    this.items = [...res['cart_items']];
                    this.cart_discount = res['cart_discount'];
                    this.shipping_discount = res['shipping_discount'];
                    this.minimum_cart_amount_offer = res['minimum_cart_amount_offer'];
                    this.total = res['total'];
                    this.count = res['count'];
                    this.sub_total = res['sub_total'];
                    this.total_discount = res['total_discount'];

                    localStorage.setItem('cart_session_id', res['cart_session_id']);
                } else
                    vNotify.error({
                        text: res['message'],
                        title: 'Suceess'
                    });

            },
            clearCart() {
                let obj = {
                    cart_session_id: localStorage.getItem('cart_session_id')
                };
                fetch('/clear_cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(obj)
                }).then(response => response.json()).then(res => {
                    if (res['success']) {
                        this.resetCart()
                        vNotify.success({
                            text: res['message'],
                            title: 'Suceess'
                        });
                    } else
                        vNotify.error({
                            text: res['message'],
                            title: 'Suceess'
                        });

                })
            },
            resetCart() {
                this.items = [];
                this.applicable_offers = [];
                this.applied_offers = [];
                this.cart_discount = 0;
                this.shipping_discount = 0;
                this.minimum_cart_amount_offer = undefined;
                this.total = 0;
                this.count = 0;
                this.sub_total = 0;
                this.coupon_code = null;

                this.total_discount = 0;
                this.is_applying_coupon = false;
                this.shipping_charge = 0;
            },
            get cartNetAmountAfterOfferDiscountAndShipping() {
                return this.amount_in_cur(this.sub_total - this.cart_discount + (this
                    .shipping_charge - this.shipping_discount))
            },
            applyCouponCode() {
                if (this.coupon_code == null) {
                    vNotify.error({
                        text: 'Please add coupon code',
                        title: 'Error'
                    });
                    return;
                }
                this.is_applying_coupon = true;
                let obj = {
                    coupon_code: this.coupon_code,
                    cart_session_id: localStorage.getItem('cart_session_id')
                };
                fetch('/applyCouponCode', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(obj)
                }).then(response => response.json()).then(res => {
                    this.is_applying_coupon = false;
                    if (res['success']) {
                        this.updateStore(res)
                        vNotify.success({
                            text: 'Coupon Applied Successfully',
                            title: 'Suceess'
                        });
                    } else
                        vNotify.error({
                            text: res['message'],
                            title: 'Suceess'
                        });

                })
            },

            qtyOfItem(item_id) {

                let cartItems = this.items.filter(item => item.product_id === item_id);
                if (cartItems.length > 0) {
                    return cartItems.reduce((accumulator, object) => {
                        return accumulator + object.qty;
                    }, 0);
                } else return 0;

            },
              cartItems(prod_id) {

                return  this.items.filter(item => item.product_id === prod_id);
               

            },
            truncate(name) {
                return name.substring(0, 20) + '...';

            },
            deleteItem(product_id) {
                let delete_item = {
                    product_id
                };
                delete_item['cart_session_id'] = localStorage.getItem('cart_session_id');
                fetch('/deleteCart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(delete_item)
                }).then(response => response.json()).then(res => {
                    if (res['success']) {
                        this.updateStore(res)
                        vNotify.success({
                            text: 'Item Deleted Successfully',
                            title: 'Suceess'
                        });
                    } else
                        vNotify.error({
                            text: res['message'],
                            title: 'Suceess'
                        });

                })
            },
            pay(amount, razorpay_orderid, ) {

                var options = {
                    "key": '{!! env('razor_key') !!}',
                    "amount": amount * 100, // Example: 2000 paise = INR 20
                    "name": "MERCHANT",
                    "order_id": razorpay_orderid,
                    "description": "description",
                    "image": "img/logo.png", // COMPANY LOGO
                    // "handler": function (response) {
                    //     console.log(response);
                    //     // AFTER TRANSACTION IS COMPLETE YOU WILL GET THE RESPONSE HERE.
                    // },
                    "callback_url": "{!! domain_route('razorpay_payment_callback') !!}",
                    "prefill": {
                        "name": "ABC", // pass customer name
                        "email": 'A@A.COM', // customer email
                        "contact": '+919123456780' //customer phone no.
                    },
                    "notes": {
                        "address": "address" //customer address 
                    },
                    "theme": {
                        "color": "#15b8f3" // screen color
                    }
                };
                console.log(options);
                var propay = new Razorpay(options);
                propay.open();
            }

        });

    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),

        }
    });
    var baseurl = window.location.origin;
    if ($('.btn-quickview1').length > 0) {
        $('.btn-quickview1').magnificPopup({
            callbacks: {
                open: function() {
                    window.dispatchEvent(new Event('resize'));

                },
                close: function() {
                    // Will fire when popup is closed
                }
                // e.t.c.
            },
            midClick: true,
            mainClass: 'mfp-fade'
        });
    }
    if ($('.view-order-item-link').length > 0) {
        $('.view-order-item-link').magnificPopup({

            midClick: true,
            mainClass: 'mfp-fade'
        });
    }
    if ($('.billing_adress_modal_link').length > 0) {
        $('.billing_adress_modal_link').magnificPopup({

            midClick: true,
            mainClass: 'mfp-fade'
        });
    }
    if ($('.auth_modal').length > 0) {
        $('.auth_modal').magnificPopup({

            midClick: true,
            mainClass: 'mfp-fade'
        });
    }
    if ($('#offer_dialog').length > 0) {

        $('#offer_dialog').magnificPopup({

            midClick: true,
            mainClass: 'mfp-fade'
        });
    }
    if ($('.all_coupons').length > 0) {

        $('.all_coupons').magnificPopup({

            midClick: true,
            mainClass: 'mfp-fade'
        });
    }
    $(document).ready(function() {
            $('#states').change(function() { 
                var state = $(this).val();
                $('#cities').empty().append('<option value="">Loading cities...</option>'); // Reset city dropdown

                if (state) {
                    $.ajax({
                        url: 'getCities', // Change this to your server-side script
                        type: 'POST',
                        data: { state: state },
                        dataType: 'json',
                        success: function(data) {
                            // Assuming `data` is an array of city names
                           
                                $('#cities').html(data['message']);
                            
                        },
                        error: function() {
                            alert('Failed to fetch cities.');
                        }
                    });
                }
            });
        });
</script>
